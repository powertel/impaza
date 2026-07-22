<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <title>Fault Received: {{ $fault_ref }}</title>
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
            font-size: 24px;
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
            padding: 16px 26px 18px;
            background: #ffffff;
        }
        .hero-meta {
            width: 100%;
            margin-top: 12px;
            border-collapse: separate;
            border-spacing: 0 6px;
        }
        .hero-meta td {
            width: 100%;
            padding: 10px 12px;
            border-radius: 12px;
            background: #ffffff;
            border: 1px solid #d7e5ff;
            vertical-align: top;
        }
        .hero-meta .meta-label {
            display: block;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #5b6b81;
            margin-bottom: 3px;
        }
        .hero-meta .meta-value {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.35;
            word-break: break-word;
        }
        .note-panel {
            margin-bottom: 18px;
            padding: 18px 20px;
            border-radius: 18px;
            background: #f8fbff;
            border: 1px solid #d9e7ff;
        }
        .note-panel p {
            margin: 0;
            font-size: 14px;
            line-height: 1.7;
            color: #425166;
        }
        .badge {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 999px;
            background: #e8f1ff;
            color: #0f4aa1;
            font-size: 12px;
            font-weight: 600;
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
        .banner {
            width: 100%;
            height: auto;
            display: block;
        }
        @media (prefers-color-scheme: dark) {
            body {
                background: #0f172a !important;
                color: #e5edf7 !important;
            }
            .container,
            .hero-meta td,
            .note-panel {
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
            .hero-meta .meta-value {
                color: #f8fafc !important;
            }
            .hero p,
            .hero-meta .meta-label,
            .note-panel p,
            .footer,
            .footer p {
                color: #cbd5e1 !important;
            }
            .badge {
                background: #1d4ed8 !important;
                color: #bfdbfe !important;
            }
        }
        @media only screen and (max-width: 680px) {
            .hero,
            .content,
            .footer {
                padding-left: 14px;
                padding-right: 14px;
            }
            .hero-meta,
            .hero-meta tbody,
            .hero-meta tr,
            .hero-meta td {
                display: block;
                width: 100%;
            }
            .hero-meta {
                border-spacing: 0;
            }
            .hero-meta td {
                margin-bottom: 12px;
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
            <span class="eyebrow" style="display:inline-block; margin-bottom:8px; padding:4px 10px; border-radius:999px; background:#dbeafe; border:1px solid #bfdbfe; font-size:10px; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; color:#1e40af;">Customer Notification</span>
            <h1 style="margin:0 0 6px; font-size:24px; line-height:1.15; color:#0f172a;">Fault received</h1>
            <p style="margin:0; font-size:13px; line-height:1.55; color:#425166;">We acknowledge receipt of your fault report. Please quote the fault number below in any correspondence.</p>

            <table class="hero-meta" role="presentation" style="width:100%; margin-top:12px; border-collapse:separate; border-spacing:0 6px;">
                <tr>
                    <td style="width:100%; padding:10px 12px; border-radius:12px; background:#ffffff; border:1px solid #d7e5ff; vertical-align:top;">
                        <span class="meta-label" style="display:block; font-size:10px; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; color:#5b6b81; margin-bottom:3px;">Fault Number</span>
                        <span class="meta-value" style="display:block; font-size:13px; font-weight:700; color:#0f172a; line-height:1.35;">{{ $fault_ref }}</span>
                    </td>
                </tr>
                <tr>
                    <td style="width:100%; padding:10px 12px; border-radius:12px; background:#ffffff; border:1px solid #d7e5ff; vertical-align:top;">
                        <span class="meta-label" style="display:block; font-size:10px; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; color:#5b6b81; margin-bottom:3px;">Customer</span>
                        <span class="meta-value" style="display:block; font-size:13px; font-weight:700; color:#0f172a; line-height:1.35;">{{ $customer }}</span>
                    </td>
                </tr>
                <tr>
                    <td style="width:100%; padding:10px 12px; border-radius:12px; background:#ffffff; border:1px solid #d7e5ff; vertical-align:top;">
                        <span class="meta-label" style="display:block; font-size:10px; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; color:#5b6b81; margin-bottom:3px;">Received</span>
                        <span class="meta-value" style="display:block; font-size:13px; font-weight:700; color:#0f172a; line-height:1.35;">{{ $received_at }}</span>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="content">
            <p style="margin:0; font-size:12px; line-height:1.5; color:#52637a;">Our team is reviewing the issue and will provide updates as we progress.</p>
        </div>
        <div class="footer">
            <p>This is an automated management email generated by Impazamon.</p>
            <p>&copy; {{ date('Y') }} PowerTel Communications. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
