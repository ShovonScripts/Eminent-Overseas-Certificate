<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Certificate Verification') — {{ $school['name'] }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            color-scheme: light;
            --font-sans: 'Plus Jakarta Sans', ui-sans-serif, system-ui, -apple-system, sans-serif;
            --font-mono: 'JetBrains Mono', ui-monospace, monospace;
            --color-navy: #0F172A;
            --color-blue: #2563EB;
            --color-gold: #D97706;
            --color-emerald: #059669;
            --color-slate: #64748B;
            --color-border: #E2E8F0;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: var(--font-sans);
            background: linear-gradient(175deg, #F8FAFC 0%, #EEF2F6 40%, #E2E8F0 100%);
            color: #0F172A;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 18px 60px;
            background-image: 
                radial-gradient(at 50% 0%, rgba(37, 99, 235, 0.06) 0px, transparent 60%),
                radial-gradient(at 100% 100%, rgba(217, 119, 6, 0.04) 0px, transparent 50%);
            background-attachment: fixed;
            -webkit-font-smoothing: antialiased;
        }
        .brand {
            text-align: center;
            margin-bottom: 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .brand-badge {
            width: 72px;
            height: 72px;
            background: #FFFFFF;
            border-radius: 20px;
            padding: 10px;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.12), 0 0 0 1px rgba(217, 119, 6, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
        }
        .brand img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .brand .name {
            font-weight: 800;
            font-size: 19px;
            letter-spacing: -0.02em;
            color: #0F172A;
            line-height: 1.25;
        }
        .brand .sub {
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--color-gold);
            margin-top: 4px;
        }
        .card {
            width: 100%;
            max-width: 660px;
            background: #FFFFFF;
            border-radius: 24px;
            box-shadow: 0 20px 50px -10px rgba(15, 23, 42, 0.12), 0 0 0 1px rgba(15, 23, 42, 0.05);
            overflow: hidden;
            transition: transform 0.2s ease;
        }
        .banner {
            padding: 28px 32px;
            color: #FFFFFF;
            display: flex;
            align-items: center;
            gap: 18px;
            position: relative;
            overflow: hidden;
        }
        .banner::after {
            content: "";
            position: absolute;
            right: -20px;
            bottom: -30px;
            width: 140px;
            height: 140px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
            pointer-events: none;
        }
        .banner .icon-wrapper {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.3);
        }
        .banner h1 {
            font-size: 22px;
            font-weight: 800;
            margin: 0;
            line-height: 1.2;
            letter-spacing: -0.02em;
        }
        .banner p {
            margin: 4px 0 0;
            font-size: 13.5px;
            opacity: 0.92;
            line-height: 1.4;
        }
        .banner.valid {
            background: linear-gradient(135deg, #064E3B 0%, #059669 60%, #10B981 100%);
        }
        .banner.revoked {
            background: linear-gradient(135deg, #881337 0%, #BE123C 60%, #E11D48 100%);
        }
        .banner.neutral {
            background: linear-gradient(135deg, #0F172A 0%, #1E3A8A 100%);
        }
        .content {
            padding: 32px 34px 36px;
        }
        dl {
            margin: 0;
            display: grid;
            grid-template-columns: 180px 1fr;
            row-gap: 16px;
            column-gap: 20px;
            align-items: baseline;
        }
        dt {
            color: #64748B;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        dd {
            margin: 0;
            font-size: 15px;
            font-weight: 600;
            color: #0F172A;
            line-height: 1.4;
        }
        .mono-val {
            font-family: var(--font-mono);
            font-size: 14.5px;
            color: #1E3A8A;
            background: #EFF6FF;
            padding: 3px 10px;
            border-radius: 6px;
            display: inline-block;
            border: 1px solid #DBEAFE;
            font-weight: 700;
        }
        .ja-tag {
            display: inline-block;
            font-size: 12.5px;
            font-weight: 500;
            color: #475569;
            background: #F1F5F9;
            padding: 1px 8px;
            border-radius: 9999px;
            margin-left: 4px;
        }
        .divider {
            height: 1px;
            background: #F1F5F9;
            margin: 26px 0;
        }
        .muted {
            color: #64748B;
            font-size: 13px;
            line-height: 1.6;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 26px;
            padding: 13px 24px;
            border-radius: 12px;
            background: linear-gradient(180deg, #1E3A8A 0%, #0F172A 100%);
            color: #FFFFFF;
            text-decoration: none;
            font-weight: 700;
            font-size: 14.5px;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.2);
            transition: all 0.15s ease;
            border: none;
            cursor: pointer;
            width: 100%;
        }
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(15, 23, 42, 0.28);
        }
        label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        input[type=text] {
            width: 100%;
            padding: 14px 16px;
            border: 1.5px solid #CBD5E1;
            border-radius: 12px;
            font-size: 16px;
            font-family: inherit;
            outline: none;
            transition: all 0.15s ease;
            background: #FFFFFF;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        }
        input[type=text]:focus {
            border-color: #2563EB;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }
        .error {
            color: #DC2626;
            font-size: 13px;
            font-weight: 600;
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .official-seal-note {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #F8FAFC;
            padding: 14px 18px;
            border-radius: 12px;
            border: 1px solid #E2E8F0;
            margin-top: 24px;
        }
        .official-seal-note svg {
            color: #059669;
            flex-shrink: 0;
        }
        .official-seal-note span {
            font-size: 12px;
            color: #475569;
            line-height: 1.5;
        }
        .footer {
            max-width: 660px;
            width: 100%;
            text-align: center;
            color: #64748B;
            font-size: 12px;
            margin-top: 28px;
            line-height: 1.6;
        }
        @media (max-width: 560px) {
            dl { grid-template-columns: 1fr; row-gap: 6px; }
            dd { margin-bottom: 12px; }
            .content { padding: 24px 20px 28px; }
            .banner { padding: 22px 20px; }
        }
    </style>
</head>
<body>
    <div class="brand">
        <div class="brand-badge">
            <img src="{{ asset($school['images']['logo']) }}" alt="{{ $school['name'] }}">
        </div>
        <div class="name">{{ $school['name'] }}</div>
        <div class="sub">Official Credential Verification Portal</div>
    </div>

    <div class="card">
        @yield('content')
    </div>

    <div class="footer">
        Official Certificate Verification Service of {{ $school['name'] }}.<br>
        {{ $school['address'] }} &middot; {{ $school['phone'] }}<br>
        <span style="opacity: 0.75; font-size: 11px;">Cryptographically secured validation &middot; Zero private student data exposed</span>
    </div>
</body>
</html>
