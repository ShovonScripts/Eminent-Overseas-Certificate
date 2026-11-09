<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('school.name') }} &middot; Certificate Portal</title>
    <meta http-equiv="refresh" content="0; url={{ route('verify.form') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            background: linear-gradient(135deg, #0F172A 0%, #1E3A8A 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FFFFFF;
            padding: 20px;
        }
        .portal-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 24px;
            padding: 40px;
            max-width: 480px;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
        }
        .brand-badge {
            width: 72px;
            height: 72px;
            background: #FFFFFF;
            border-radius: 20px;
            padding: 10px;
            margin: 0 auto 20px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
        }
        .brand-badge img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        h1 {
            font-size: 22px;
            font-weight: 800;
            margin: 0 0 8px;
        }
        p {
            color: #94A3B8;
            font-size: 14px;
            margin: 0 0 24px;
            line-height: 1.5;
        }
        .actions {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 20px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.15s ease;
        }
        .btn-primary {
            background: #2563EB;
            color: #FFFFFF;
        }
        .btn-primary:hover {
            background: #1D4ED8;
            transform: translateY(-1px);
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: #FFFFFF;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.18);
        }
    </style>
</head>
<body>
    <div class="portal-card">
        <div class="brand-badge">
            <img src="{{ asset($schoolConfig['images']['logo']) }}" alt="{{ $schoolConfig['name'] }}">
        </div>
        <h1>{{ $schoolConfig['name'] }}</h1>
        <p>Official Bilingual Certificate Issuing & Public Verification Portal</p>
        <div class="actions">
            <a href="{{ route('verify.form') }}" class="btn btn-primary">Open Verification Service &rarr;</a>
            <a href="{{ route('login') }}" class="btn btn-secondary">Staff Sign In</a>
        </div>
    </div>
</body>
</html>
