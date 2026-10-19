<?php

/*
|--------------------------------------------------------------------------
| Fault intake (external channels)
|--------------------------------------------------------------------------
| Settings for /api/v1/intake/* — the endpoints WhatsApp, USSD, web, mobile
| and call-centre tools use to look up links and log faults in Impaza.
*/

return [

    /*
     | Channels allowed to log faults. Each channel gets its own "system user"
     | (created automatically on first use) so the existing "Reported By"
     | column in Impaza shows where the fault came from.
     */
    'channels' => [
        'whatsapp'    => 'WhatsApp (Self-service)',
        'ussd'        => 'USSD (Self-service)',
        'web'         => 'Web Portal (Self-service)',
        'mobile'      => 'Mobile App (Self-service)',
        'call_centre' => 'Call Centre (API)',
        'email'       => 'Email (API)',
        'other'       => 'Other Channel (API)',
    ],

    // Domain used for the auto-created channel system users, e.g. whatsapp@channels.impaza.local
    'system_user_email_domain' => env('FAULT_INTAKE_USER_DOMAIN', 'channels.impaza.local'),

    /*
     | Optional: lock an API key to one channel so a key issued to the WhatsApp
     | bot can never log a fault as "ussd". Format: "key1:whatsapp,key2:ussd".
     | Keys not listed here may send any channel in the request body.
     */
    'key_channels' => env('FAULT_INTAKE_KEY_CHANNELS', ''),

    /*
     | Customer-friendly problem categories shown in bot / USSD menus.
     | 'rfo' must match a value in reasons_for_outages.RFO — it becomes the
     | fault's *suspected* RFO; NOC still confirms the real RFO on assessment.
     | Order here = menu order (option 1, 2, 3 ...).
     */
    'categories' => [
        'link_down' => [
            'label' => 'No internet / link is down',
            'rfo'   => 'No Fx Light',
            'fault_type' => 'Link Down',
        ],
        'no_internet' => [
            'label' => 'Connected but no internet',
            'rfo'   => 'Connected Without Internet',
            'fault_type' => 'No Internet',
        ],
        'slow' => [
            'label' => 'Slow speeds',
            'rfo'   => 'Slow Speeds',
            'fault_type' => 'Slow Speeds',
        ],
        'intermittent' => [
            'label' => 'Intermittent / dropping connection',
            'rfo'   => 'Packet Losses',
            'fault_type' => 'Intermittent',
        ],
        'power' => [
            'label' => 'Power problem at site / equipment off',
            'rfo'   => 'Power Outage',
            'fault_type' => 'Power',
        ],
        'equipment' => [
            'label' => 'Router / CPE equipment faulty',
            'rfo'   => 'CPE Faulty',
            'fault_type' => 'Equipment',
        ],
        'cable' => [
            'label' => 'Damaged or cut cable',
            'rfo'   => 'Cable Fault',
            'fault_type' => 'Cable',
        ],
    ],

    // Used when the caller sends neither category nor suspected_rfo_id.
    'default_category' => 'link_down',

    /*
     | Statuses treated as "closed" when checking whether a link already has
     | an open fault (status_code values from the statuses table).
     */
    'closed_status_codes' => ['CLN', 'RVK'],

    // link_status ids hidden from link search (4 = Decommissioned).
    'hidden_link_statuses' => [4],

    // Max links returned by a search (keeps WhatsApp/USSD menus short).
    'max_link_results' => (int) env('FAULT_INTAKE_MAX_LINKS', 20),
];
