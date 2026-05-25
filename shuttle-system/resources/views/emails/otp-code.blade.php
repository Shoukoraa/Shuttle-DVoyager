<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP Shuttle</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f6f7fb; margin: 0; padding: 24px; color: #1f2937;">
    <div style="max-width: 560px; margin: 0 auto; background: #ffffff; border-radius: 16px; padding: 32px; border: 1px solid #e5e7eb;">
        <p style="margin: 0 0 12px; font-size: 16px;">Halo {{ $recipientName ?? 'User' }},</p>
        <p style="margin: 0 0 20px; font-size: 15px; line-height: 1.6;">
            Gunakan kode OTP berikut untuk melanjutkan proses login:
        </p>

        <div style="text-align: center; margin: 24px 0;">
            <div style="display: inline-block; letter-spacing: 6px; font-size: 32px; font-weight: 700; background: #111827; color: #ffffff; padding: 16px 24px; border-radius: 12px;">
                {{ $code }}
            </div>
        </div>

        <p style="margin: 0; font-size: 14px; color: #6b7280; line-height: 1.6;">
            Kode ini berlaku selama {{ $expiresInMinutes }} menit. Jika Anda tidak meminta kode ini, abaikan email ini.
        </p>
    </div>
</body>
</html>