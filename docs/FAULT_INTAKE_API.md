# Fault Intake API (WhatsApp, USSD, Web, Mobile, Call Centre)

One set of endpoints that any channel uses to find a customer's link and log a fault in Impaza.
Faults logged here go through exactly the same pipeline as the NOC "Log fault" form: same
`PWTyymmddNNN` reference, status *Waiting for Assessment*, ON LOGGING remark, NOC SMS/push alert,
customer SMS acknowledgement, receipt e-mail, and POP-outage child faults for POP aggregators.

Base URL: `https://<impaza-host>/api/v1/intake`

---

## 1. Authentication

Send an API key on every request (keys live in `EXTERNAL_API_KEYS`, comma separated):

```
X-API-Key: <key>
Accept: application/json
```

(`Authorization: ApiKey <key>` also works.)

Give each channel its **own key**. Optionally lock a key to a channel so it can't log as another:

```
FAULT_INTAKE_KEY_CHANNELS=<whatsapp-key>:whatsapp,<ussd-key>:ussd
```

Rate limit: 600 requests/minute **per API key** (`FAULT_INTAKE_RATE_LIMIT`), so a single bot server
serving many customers is not throttled by IP.

## 2. Knowing where a fault came from

Every fault now has:

| Column | Meaning |
|---|---|
| `channel` | `whatsapp`, `ussd`, `web`, `mobile`, `call_centre`, `email`, `other` — or `internal` for faults captured by staff in Impaza |
| `channel_reference` | The caller's own id (WhatsApp session id, USSD session id, request UUID). Unique per channel → safe retries |
| `channel_meta` | JSON: chosen category, agent name, anything you send in `meta` |

Each channel also has a system user (e.g. **WhatsApp (Self-service)**), created automatically on
first use, so the existing **Reported By** column in Impaza already shows the channel.

## 3. Every response has `result` and `message`

- `result` — machine-readable outcome, branch on this.
- `message` — customer-ready text (WhatsApp/USSD can send it as-is).

| HTTP | `result` | When |
|---|---|---|
| 200 | `found`, `single_match`, `multiple_matches`, `no_links`, `none`, `already_logged` | Normal reads / idempotent replay |
| 201 | `created` | New fault logged |
| 401 | — | Missing/invalid API key |
| 403 | `channel_not_allowed` | Key is locked to another channel |
| 404 | `customer_not_found`, `link_not_found`, `fault_not_found` | |
| 409 | `open_fault_exists` | Link already has an open fault — `fault` holds it |
| 409 | `multiple_customers` | Phone matches several accounts — ask for account number |
| 422 | `validation_error`, `invalid_phone`, `caller_required` | Bad input — see `errors` |

---

## 4. Suggested WhatsApp flow (step by step)

```
Bot: Please send your Powertel account number.
User: 1200000888
  → GET /customers/lookup?account_number=1200000888        (404 → ask again)

Bot: Which town/area is affected? (or reply ALL)
User: Mutare
  → GET /links?account_number=1200000888&q=Mutare
     single_match      → "Is this the affected link? … YES/NO"
     multiple_matches  → send `message` (numbered menu); keep `links[]` in session
     no_links          → send `message`, ask again
     links[i].open_fault != null → tell the customer it is already being worked on

User: 2                      → link_id = links[option 2].id
Bot: What is the problem?    → GET /fault-categories, send `menu`
User: 3                      → category = categories[option 3].key
Bot: Briefly describe the problem.
User: Very slow since morning
Bot: Your name?  /  Number we can call? (default: the WhatsApp number)  /  Site address? (optional)

Bot: Confirm: <link display> – <category label> – <description>. Reply YES to submit.
  → POST /faults
     201 created           → send `message` (contains the reference)
     409 open_fault_exists → send `message` (existing reference)
     200 already_logged    → send `message` (it was a retry)

Later: "status PWT261019001"
  → GET /faults/PWT261019001?phone=<whatsapp number>   → send `message`
"my faults"
  → GET /faults?phone=<whatsapp number>                 → send `message`
```

Shortcut when the customer doesn't know the account number: use `phone=<whatsapp number>` instead of
`account_number` on `/customers/lookup` and `/links`. It matches the customer's registered contact
number and numbers that logged faults before. If several accounts match you get
`409 multiple_customers` with a list — then ask for the account number.

USSD: same flow; use `menu` strings and keep `q` short (town name). Use the USSD session id as
`channel_reference`.

---

## 5. Endpoints

### GET `/fault-categories`
Customer-friendly problem list (configurable in `config/fault_intake.php`).

```json
{
  "categories": [
    {"option": 1, "key": "link_down", "label": "No internet / link is down", "suspected_rfo": "No Fx Light"},
    {"option": 2, "key": "no_internet", "label": "Connected but no internet", "suspected_rfo": "Connected Without Internet"},
    {"option": 3, "key": "slow", "label": "Slow speeds", "suspected_rfo": "Slow Speeds"}
  ],
  "menu": "1. No internet / link is down\n2. Connected but no internet\n3. Slow speeds\n...",
  "channels": ["whatsapp", "ussd", "web", "mobile", "call_centre", "email", "other"],
  "rfos": [{"id": 1, "name": "Power Outage"}]
}
```

### GET `/customers/lookup?account_number=…` or `?phone=…`

```json
{
  "result": "found",
  "customer": {
    "id": 103, "name": "ZIMRA", "account_number": "1200000888", "contract_number": "40108676",
    "address": null, "links_count": 13, "open_faults_count": 0
  },
  "message": "We found ZIMRA (account 1200000888) with 13 link(s)."
}
```

`account_number` matches the customer account number, contract number, or a link-level account number
(in that last case only links on that account are returned).

### GET `/links?account_number=…|phone=…&q=…&city=…`
Lists the customer's active links (decommissioned hidden). `q` is free text — every word must match
the link name, city, suburb, POP, JCC number, SAP code, comment, service type or customer address.

```json
{
  "result": "multiple_matches",
  "total": 4,
  "truncated": false,
  "customer": { "name": "ZIMRA", "account_number": "1200000888", "...": "..." },
  "links": [
    {
      "option": 1,
      "id": 9,
      "name": "HRE-MUTARE FORBES 20MB JCC 9654",
      "display": "HRE-MUTARE FORBES 20MB JCC 9654 — Mutare",
      "location": { "city": "Mutare", "suburb": null, "region": "East", "pop": null, "address": null },
      "service_type": "Intercity VPN",
      "capacity": "20",
      "account_number": "1200000888",
      "jcc_number": null,
      "link_status": "Connected",
      "open_fault": null
    }
  ],
  "menu": "1. HRE-MUTARE FORBES 20MB JCC 9654 — Mutare\n2. ...",
  "message": "Which link is affected? Reply with the number:\n1. HRE-MUTARE FORBES 20MB JCC 9654 — Mutare\n..."
}
```

`display` is the line to read back to the customer ("link name — suburb, city").
`open_fault` is filled when the link already has an open fault (including POP-outage faults):

```json
"open_fault": {
  "reference": "PWT261019003",
  "status": {"code": "POI", "stage": "in_progress", "label": "Affected by a network outage – being worked on"},
  "logged_at": "2026-10-19T16:34:01+02:00"
}
```

Max 20 results per call (`FAULT_INTAKE_MAX_LINKS`); `truncated: true` means ask the customer to narrow down.

### GET `/links/{id}?account_number=…|phone=…`
Full details for one link (must belong to the caller), plus its last 5 faults in `recent_faults`.

### POST `/faults`

```json
{
  "channel": "whatsapp",
  "channel_reference": "wa-3f2c9a1e-session-77",
  "account_number": "1200000888",
  "link_id": 12,
  "category": "slow",
  "description": "Internet very slow since morning",
  "contact_name": "Edwin Magodi",
  "contact_phone": "0773 537 476",
  "contact_email": null,
  "address": "ZIMRA Mutare, 2nd floor server room",
  "meta": {"wa_id": "263773537476"}
}
```

| Field | Required | Notes |
|---|---|---|
| `channel` | yes | One of the channels above. May also be sent as `X-Channel` header |
| `channel_reference` | recommended | Your unique id. Re-sending the same one returns the original fault (`already_logged`) |
| `account_number` / `phone` | one of | Identifies the customer (same rules as `/links`) |
| `link_id` | yes | `links[].id` from `/links` — must belong to that customer |
| `category` | no | Key from `/fault-categories` (default `link_down`) |
| `suspected_rfo_id` | no | For agent tools: exact RFO instead of a category |
| `description` | yes | 3–2000 chars, what the customer said |
| `contact_name` | yes | |
| `contact_phone` | yes | Zimbabwe mobile in any format: `0773…`, `773…`, `+263773…`, `263773…` → stored as `2637XXXXXXXX` |
| `contact_email` | no | Receipt e-mail is sent if given |
| `address` | no | Site address/landmark; defaults to the customer's address |
| `reported_by` | no | Agent/operator name (call centre, web agents) |
| `allow_duplicate` | no | `true` lets an agent log even if the link has an open fault |
| `meta` | no | Any JSON object, stored in `channel_meta.extra` |

**201 Created**

```json
{
  "result": "created",
  "fault": {
    "reference": "PWT261019001",
    "channel": "whatsapp",
    "channel_reference": "wa-3f2c9a1e-session-77",
    "status": {"code": "WAS", "stage": "received", "label": "Received – waiting for assessment", "internal": "Waiting for Assessment"},
    "problem": "Slow speeds",
    "suspected_rfo": "Slow Speeds",
    "confirmed_rfo": null,
    "customer": {"name": "ZIMRA", "account_number": "1200000888"},
    "link": {
      "id": 12,
      "name": "ZIMRA BAK LOGISTICS MTRE-HQ 5MB JCC 9475",
      "display": "ZIMRA BAK LOGISTICS MTRE-HQ 5MB JCC 9475 — Mutare",
      "service_type": "Metro VPN",
      "capacity": null,
      "location": {"city": "Mutare", "suburb": null, "region": "East", "pop": null, "address": "ZIMRA Mutare, 2nd floor server room"}
    },
    "contact": {"name": "Edwin Magodi", "phone": "263773537476", "email": null},
    "description": "Internet very slow since morning",
    "is_part_of_network_outage": false,
    "parent_reference": null,
    "logged_at": "2026-10-19T16:34:01+02:00",
    "updated_at": "2026-10-19T16:34:01+02:00",
    "summary": "Fault PWT261019001\nLink: ZIMRA BAK LOGISTICS MTRE-HQ 5MB JCC 9475 — Mutare\nStatus: Received – waiting for assessment\nLogged: 19 Oct 2026 16:34",
    "timeline": [{"status": "Received – waiting for assessment", "started_at": "2026-10-19T16:34:01+02:00", "ended_at": null}]
  },
  "message": "Thank you Edwin. Your fault has been logged.\nReference: PWT261019001\nLink: ZIMRA BAK LOGISTICS MTRE-HQ 5MB JCC 9475 — Mutare\nOur NOC team will assess it shortly and keep you updated."
}
```

**409 open_fault_exists** — same shape; `fault` is the fault already open on that link.

### GET `/faults/{reference}?account_number=…` or `?phone=…`
One fault with its timeline. The caller must own it (customer account, or the phone that logged it /
the customer's contact number); otherwise `404 fault_not_found` (same answer as "doesn't exist",
so references can't be guessed). Reference is case-insensitive.

### GET `/faults?account_number=…|phone=…&status=open|all&limit=10`
The caller's faults, newest first. Default `status=open`. With `phone`, returns faults logged with that number.

---

## 6. Customer-facing statuses

| Code | `stage` | `label` |
|---|---|---|
| WAS | received | Received – waiting for assessment |
| ASD | in_progress | Assessed – a technician is being assigned |
| RTN | in_progress | A technician is working on it |
| CLT / CLC | in_progress | Fixed – awaiting final confirmation |
| CLN | resolved | Resolved |
| REF | in_progress | Referred to a specialist team |
| PRK | in_progress | On hold |
| ESC / MES | in_progress | Escalated |
| POI | in_progress | Affected by a network outage – being worked on |
| RVK | closed | Closed |

---

## 7. Spring Boot sketch

```java
@Component
public class ImpazaClient {
    private final WebClient http;

    public ImpazaClient(@Value("${impaza.base-url}") String baseUrl,
                        @Value("${impaza.api-key}") String apiKey) {
        this.http = WebClient.builder()
            .baseUrl(baseUrl + "/api/v1/intake")
            .defaultHeader("X-API-Key", apiKey)
            .defaultHeader(HttpHeaders.ACCEPT, MediaType.APPLICATION_JSON_VALUE)
            .build();
    }

    public JsonNode findLinks(String account, String q) {
        return http.get().uri(u -> u.path("/links")
                .queryParam("account_number", account)
                .queryParamIfPresent("q", Optional.ofNullable(q)).build())
            .exchangeToMono(r -> r.bodyToMono(JsonNode.class))   // read body for 4xx too
            .block();
    }

    public JsonNode logFault(Map<String, Object> body) {
        return http.post().uri("/faults").bodyValue(body)
            .exchangeToMono(r -> r.bodyToMono(JsonNode.class))
            .block();
    }
}
```

Read the body on 4xx too (`exchangeToMono`) — `result` + `message` are there. Branch on `result`, send
`message` to the customer. Use the WhatsApp conversation/session id as `channel_reference` so a retry
after a timeout never logs twice.

---

## 8. Deploying

1. `php artisan migrate` (adds `channel`, `channel_reference`, `channel_meta` to `faults`; existing rows become `internal`).
2. Add a key per channel to `EXTERNAL_API_KEYS`, optionally `FAULT_INTAKE_KEY_CHANNELS`.
3. `php artisan config:clear` (or `config:cache`).
4. Review the category → RFO mapping in `config/fault_intake.php`.
