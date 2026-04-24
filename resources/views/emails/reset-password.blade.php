<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f8fafc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            max-width: 560px;
            margin: 40px auto;
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 4px 16px rgba(0, 0, 0, 0.06);
        }

        .header {
            background: #0B192C;
            padding: 32px;
            text-align: center;
        }

        .header h1 {
            color: white;
            font-size: 24px;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .body {
            padding: 32px;
        }

        .body p {
            color: #334155;
            font-size: 15px;
            line-height: 1.7;
            margin: 0 0 16px;
        }

        .btn {
            display: inline-block;
            padding: 14px 32px;
            background: #3B82F6;
            color: white !important;
            text-decoration: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            margin: 8px 0 24px;
        }

        .footer {
            padding: 24px 32px;
            border-top: 1px solid #f1f5f9;
        }

        .footer p {
            color: #94a3b8;
            font-size: 12px;
            line-height: 1.6;
            margin: 0;
        }

        .note {
            background: #f8fafc;
            border-radius: 10px;
            padding: 16px;
            margin-top: 8px;
        }

        .note p {
            color: #64748b;
            font-size: 13px;
            margin: 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>MasukPakEko</h1>
        </div>
        <div class="body">
            <p>Halo <strong>{{ $user->name }}</strong>,</p>
            <p>Kami menerima permintaan untuk mereset password akun anda. Klik tombol di bawah untuk membuat password
                baru:</p>

            <div style="text-align: center;">
                <a href="{{ $resetUrl }}" class="btn">Reset Password</a>
            </div>

            <div class="note">
                <p>Link ini akan kedaluwarsa dalam <strong>60 menit</strong>. Jika anda tidak merasa meminta reset
                    password, abaikan email ini.</p>
            </div>
        </div>
        <div class="footer">
            <p>Email ini dikirim otomatis oleh sistem MasukPakEko.<br>DPMPTSP Kabupaten Pekalongan &copy;
                {{ date('Y') }}</p>
        </div>
    </div>
</body>

</html>
