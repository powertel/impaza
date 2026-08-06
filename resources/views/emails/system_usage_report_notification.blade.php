<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <title>{{ $subject }}</title>
    <style>
        body {
            margin: 0;
            padding: 16px 10px;
            background: #f3f6fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #142033;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #d9e3ef;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 18px 48px rgba(15, 23, 42, 0.10);
        }
        .hero {
            background: #eef5ff;
            color: #142033;
            padding: 24px 26px 18px;
            border-bottom: 1px solid #d7e5ff;
            border-top: 6px solid #1f6feb;
        }
        .letterhead {
            margin-bottom: 10px;
            text-align: center;
        }
        .letterhead img {
            display: block;
            max-width: 180px;
            width: 100%;
            height: auto;
            margin: 0 auto;
        }
        .eyebrow {
            display: inline-block;
            margin-bottom: 8px;
            padding: 4px 10px;
            border-radius: 999px;
            background: #dbeafe;
            border: 1px solid #bfdbfe;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #1e40af;
        }
        .hero h1 {
            margin: 0 0 6px;
            font-size: 22px;
            line-height: 1.15;
            color: #0f172a;
        }
        .hero p {
            margin: 0;
            font-size: 13px;
            line-height: 1.55;
            color: #425166;
        }
        .content {
            padding: 24px 26px 22px;
            background: #ffffff;
        }
        .greeting {
            margin: 0 0 14px;
            font-size: 15px;
            line-height: 1.6;
            color: #142033;
            font-weight: 600;
        }
        .message {
            margin: 0 0 18px;
            font-size: 14px;
            line-height: 1.7;
            color: #425166;
        }
        .summary-card {
            padding: 16px 18px;
            border-radius: 16px;
            background: #f8fbff;
            border: 1px solid #d9e7ff;
            margin-bottom: 18px;
        }
        .summary-label {
            display: block;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #5b6b81;
            margin-bottom: 5px;
        }
        .summary-value {
            display: block;
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.4;
        }
        .attachment-note {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 14px 16px;
            border-radius: 14px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            margin-bottom: 18px;
        }
        .attachment-icon {
            flex-shrink: 0;
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: #dcfce7;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: #15803d;
        }
        .attachment-text {
            margin: 0;
            font-size: 13px;
            line-height: 1.55;
            color: #14532d;
        }
        .attachment-text strong {
            color: #166534;
        }
        .footer {
            padding: 14px 26px 16px;
            background: #f8fafc;
            color: #64748b;
            font-size: 11px;
            line-height: 1.45;
            border-top: 1px solid #e2e8f0;
        }
        .footer p {
            margin: 0 0 4px;
        }
        .footer p:last-child {
            margin-bottom: 0;
        }
        @media (prefers-color-scheme: dark) {
            body {
                background: #0f172a !important;
                color: #e5edf7 !important;
            }
            .container,
            .summary-card {
                background: #111827 !important;
                border-color: #334155 !important;
                color: #e5edf7 !important;
                box-shadow: none !important;
            }
            .hero {
                background: #162133 !important;
                border-top-color: #3b82f6 !important;
                color: #e5edf7 !important;
            }
            .eyebrow {
                background: #1e3a8a !important;
                border-color: #2563eb !important;
                color: #dbeafe !important;
            }
            .content {
                background: #0f172a !important;
            }
            .footer {
                background: #162133 !important;
                border-color: #334155 !important;
                color: #cbd5e1 !important;
            }
            .hero h1,
            .summary-value {
                color: #f8fafc !important;
            }
            .hero p,
            .summary-label,
            .message,
            .footer,
            .footer p {
                color: #cbd5e1 !important;
            }
            .greeting {
                color: #e5edf7 !important;
            }
            .attachment-note {
                background: #052e16 !important;
                border-color: #166534 !important;
            }
            .attachment-icon {
                background: #14532d !important;
                color: #bbf7d0 !important;
            }
            .attachment-text {
                color: #bbf7d0 !important;
            }
            .attachment-text strong {
                color: #86efac !important;
            }
        }
        @media only screen and (max-width: 680px) {
            .hero,
            .content,
            .footer {
                padding-left: 14px;
                padding-right: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="hero" style="background:#eef5ff; color:#142033; padding:24px 26px 18px; border-bottom:1px solid #d7e5ff; border-top:6px solid #1f6feb;">
            <div class="letterhead" style="margin-bottom:10px; text-align:center;">
                <img src="{{ $message->embed(public_path('img/powertel.png')) }}" alt="PowerTel Communications" style="display:block; max-width:180px; width:100%; height:auto; margin:0 auto;">
            </div>
            <span class="eyebrow" style="display:inline-block; margin-bottom:8px; padding:4px 10px; border-radius:999px; background:#dbeafe; border:1px solid #bfdbfe; font-size:10px; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; color:#1e40af;">Management Report</span>
            <h1 style="margin:0 0 6px; font-size:22px; line-height:1.15; color:#0f172a;">Impazamon System Usage Report</h1>
            <p style="margin:0; font-size:13px; line-height:1.55; color:#425166;">Weekly platform activity and operational performance report.</p>
        </div>

        <div class="content">
            <p class="greeting" style="margin:0 0 14px; font-size:15px; line-height:1.6; color:#142033; font-weight:600;">Good Day,</p>

            <p class="message" style="margin:0 0 18px; font-size:14px; line-height:1.7; color:#425166;">
                Please find attached is the report for Impazamon system usage from <strong>{{ $period_start }}</strong> to <strong>{{ $period_end }}</strong>.
            </p>

            <div class="summary-card" style="padding:16px 18px; border-radius:16px; background:#f8fbff; border:1px solid #d9e7ff; margin-bottom:18px;">
                <span class="summary-label" style="display:block; font-size:10px; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; color:#5b6b81; margin-bottom:5px;">Reporting Period</span>
                <span class="summary-value" style="display:block; font-size:15px; font-weight:700; color:#0f172a; line-height:1.4;">{{ $period_label }}</span>
            </div>

            <div class="attachment-note" style="display:flex; align-items:flex-start; gap:10px; padding:14px 16px; border-radius:14px; background:#f0fdf4; border:1px solid #bbf7d0; margin-bottom:18px;">
                <div class="attachment-icon" style="flex-shrink:0; width:34px; height:34px; border-radius:10px; background:#dcfce7; display:flex; align-items:center; justify-content:center; font-size:16px; color:#15803d;">
                    &#128196;
                </div>
                <p class="attachment-text" style="margin:0; font-size:13px; line-height:1.55; color:#14532d;">
                    <strong>PDF attached:</strong> {{ $pdf_filename }}
                </p>
            </div>

            <p class="message" style="margin:0; font-size:13px; line-height:1.7; color:#425166;">
                The report provides a consolidated view of monitored user participation, total platform activity, operational performance by role, and regional breakdowns for the selected reporting period.
            </p>
        </div>

        <div class="footer">
            <p>This is an automated management email generated by Impazamon.</p>
            <p>&copy; {{ date('Y') }} PowerTel Communications. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
