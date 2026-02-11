<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f7f6;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #dc3545;
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            letter-spacing: 1px;
        }
        .content {
            padding: 30px;
        }
        .fault-card {
            background-color: #fff5f5;
            border-left: 4px solid #dc3545;
            padding: 20px;
            margin-bottom: 25px;
            border-radius: 0 4px 4px 0;
        }
        .fault-card h2 {
            margin-top: 0;
            color: #dc3545;
            font-size: 18px;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 10px;
        }
        .info-row {
            display: flex;
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            width: 150px;
            color: #555;
        }
        .info-value {
            flex: 1;
        }
        .footer {
            background-color: #f1f1f1;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
        .banner {
            width: 100%;
            height: auto;
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Fault Escalation Notification</h1>
        </div>
        
        <div class="content">
            <p>Hello,</p>
            <p>The following fault has been escalated and requires your immediate attention:</p>
            
            <div class="fault-card">
                <h2>Reference: {{ $fault_ref }}</h2>
                
                <div class="info-row">
                    <span class="info-label">Customer:</span>
                    <span class="info-value">{{ $customer }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Service Type:</span>
                    <span class="info-value">{{ $service_type }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Escalation Type:</span>
                    <span class="info-value">{{ $escalation_type }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Escalated At:</span>
                    <span class="info-value">{{ $escalated_at }}</span>
                </div>
            </div>
            
            <p>Please log in to the system to review the escalation details and intervene as necessary.</p>
        </div>

        <img src="https://mail.powertel.co.zw/owa/auth/15.2.1748/themes/resources/powertel_banner.png" alt="PowerTel Banner" class="banner">
        
        <div class="footer">
            <p>This is an automated notification from Impazamon.<br>
            &copy; {{ date('Y') }} PowerTel Communications. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
