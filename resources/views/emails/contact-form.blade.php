<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Form Message</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .wrapper { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .header { background: #1e293b; padding: 32px 40px; }
        .header img { height: 40px; }
        .header h1 { color: #F5A623; margin: 16px 0 4px; font-size: 20px; }
        .header p { color: #94a3b8; margin: 0; font-size: 14px; }
        .body { padding: 32px 40px; }
        .field { margin-bottom: 20px; }
        .label { font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #64748b; margin-bottom: 4px; }
        .value { font-size: 15px; color: #1e293b; }
        .message-box { background: #f8fafc; border-left: 4px solid #F5A623; padding: 16px 20px; border-radius: 4px; font-size: 15px; color: #334155; line-height: 1.6; white-space: pre-wrap; }
        .footer { background: #f8fafc; padding: 20px 40px; border-top: 1px solid #e2e8f0; font-size: 12px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>New Contact Form Submission</h1>
            <p>PT. Roda Jaya Sakti — Website Contact Form</p>
        </div>
        <div class="body">
            <div class="field">
                <div class="label">Name</div>
                <div class="value">{{ $senderName }}</div>
            </div>
            <div class="field">
                <div class="label">Email</div>
                <div class="value">{{ $senderEmail }}</div>
            </div>
            @if ($senderPhone)
            <div class="field">
                <div class="label">Phone</div>
                <div class="value">{{ $senderPhone }}</div>
            </div>
            @endif
            <div class="field">
                <div class="label">Message</div>
                <div class="message-box">{{ $msgBody }}</div>
            </div>
        </div>
        <div class="footer">
            This message was sent via the contact form at rodajayasakti.id. Reply directly to this email to respond to {{ $senderName }}.
        </div>
    </div>
</body>
</html>
