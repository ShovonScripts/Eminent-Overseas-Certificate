<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Certificate Verification') — {{ $school['name'] }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            color-scheme: light;
            --font-sans: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --font-serif: 'Cinzel', 'Times New Roman', Georgia, serif;
            --font-mono: 'JetBrains Mono', ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            
            --color-gov-navy: #0A192F;
            --color-gov-navy-light: #172A45;
            --color-gov-slate: #334155;
            --color-gov-muted: #64748B;
            --color-gov-border: #E2E8F0;
            --color-gov-card: #FFFFFF;
            --color-gov-bg: #F8FAFC;
            
            --color-verified-forest: #064E3B;
            --color-verified-emerald: #059669;
            --color-verified-bg: #ECFDF5;
            --color-verified-border: #A7F3D0;
            
            --color-gold-deep: #92400E;
            --color-gold-accent: #B45309;
            --color-gold-bg: #FFFBEB;
            --color-gold-border: #FDE68A;
            
            --color-revoked-crimson: #991B1B;
            --color-revoked-bg: #FEF2F2;
            --color-revoked-border: #FECACA;
            
            --radius-sm: 6px;
            --radius-md: 10px;
            --radius-lg: 16px;
            --shadow-subtle: 0 1px 3px rgba(15, 23, 42, 0.05), 0 1px 2px rgba(15, 23, 42, 0.03);
            --shadow-card: 0 4px 20px -2px rgba(15, 23, 42, 0.08), 0 2px 6px -1px rgba(15, 23, 42, 0.04);
            --shadow-elevated: 0 20px 40px -15px rgba(15, 23, 42, 0.12);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: var(--font-sans);
            background-color: #F1F5F9;
            background-image: 
                radial-gradient(circle at 50% 0%, #FFFFFF 0%, #F1F5F9 65%),
                linear-gradient(to bottom, #FFFFFF 0px, transparent 180px);
            color: var(--color-gov-navy);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* ========== Institutional Top Navigation Header ========== */
        .gov-header {
            background: #FFFFFF;
            border-bottom: 1px solid var(--color-gov-border);
            box-shadow: var(--shadow-subtle);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .gov-header-inner {
            max-width: 1040px;
            margin: 0 auto;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .gov-brand {
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
            color: inherit;
        }
        .gov-logo-frame {
            width: 46px;
            height: 46px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #FFFFFF;
            border: 1px solid var(--color-gov-border);
            border-radius: var(--radius-md);
            padding: 4px;
            flex-shrink: 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .gov-logo-frame img { width: 100%; height: 100%; object-fit: contain; }
        .gov-brand-text { display: flex; flex-direction: column; }
        .gov-brand-title {
            font-size: 15px; font-weight: 800; letter-spacing: -0.01em; color: var(--color-gov-navy); line-height: 1.25;
        }
        .gov-brand-subtitle {
            font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-gold-accent);
            margin-top: 2px; display: flex; align-items: center; gap: 6px;
        }
        .gov-brand-subtitle span.dot { width: 4px; height: 4px; background: var(--color-gold-accent); border-radius: 50%; display: inline-block; }
        .gov-header-actions { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
        .gov-nav-btn {
            display: inline-flex; align-items: center; gap: 7px; padding: 7px 14px; font-size: 13px; font-weight: 600;
            border-radius: var(--radius-sm); text-decoration: none; transition: all 0.15s ease; white-space: nowrap;
        }
        .gov-nav-btn.btn-outline { color: var(--color-gov-slate); background: #FFFFFF; border: 1px solid var(--color-gov-border); }
        .gov-nav-btn.btn-outline:hover { background: #F8FAFC; color: var(--color-gov-navy); border-color: #CBD5E1; }
        .gov-nav-btn.btn-primary { background: var(--color-gov-navy); color: #FFFFFF; border: 1px solid var(--color-gov-navy); }
        .gov-nav-btn.btn-primary:hover { background: var(--color-gov-navy-light); border-color: var(--color-gov-navy-light); }

        /* ========== Main Content Area ========== */
        .gov-main { flex: 1; display: flex; flex-direction: column; align-items: center; padding: 32px 18px 48px; width: 100%; }
        .gov-container { width: 100%; max-width: 820px; }

        .gov-footer { width: 100%; max-width: 820px; margin: 28px auto 0; color: var(--color-gov-muted); }
        .gov-footer-notice { display: flex; align-items: flex-start; gap: 11px; padding: 13px 15px; background: #FFFFFF; border: 1px solid var(--color-gov-border); border-radius: var(--radius-md); font-size: 12px; line-height: 1.6; }
        .gov-footer-notice svg { flex: 0 0 17px; margin-top: 1px; color: var(--color-gov-muted); }
        .gov-footer-notice strong { color: var(--color-gov-slate); }
        .gov-footer-notice a { color: #1D4F7A; font-weight: 600; }
        .gov-footer-legal { padding: 12px 2px 0; font-size: 11px; line-height: 1.6; }
        .gov-footer-legal strong { color: var(--color-gov-slate); }
        .toast-copied { position: fixed; right: 20px; bottom: 20px; z-index: 200; padding: 10px 14px; color: #FFFFFF; background: #17385B; border-radius: 7px; box-shadow: 0 4px 14px rgba(15, 23, 42, .18); font-size: 13px; opacity: 0; visibility: hidden; transform: translateY(6px); transition: opacity .18s ease, transform .18s ease, visibility .18s ease; }
        .toast-copied.is-visible { opacity: 1; visibility: visible; transform: translateY(0); }

        /* ========== Verification Card ========== */
        .credential-card {
            background: var(--color-gov-card);
            border-radius: var(--radius-lg);
            border: 1px solid var(--color-gov-border);
            box-shadow: var(--shadow-card);
            overflow: hidden;
            position: relative;
        }

        /* ========== Verification Status Bar ========== */
        .status-banner {
            padding: 20px 24px;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            border-bottom: 1px solid var(--color-gov-border);
            position: relative;
        }
        .status-banner.is-valid { background: linear-gradient(135deg, #064E3B 0%, #065F46 60%, #047857 100%); color: #FFFFFF; }
        .status-banner.is-revoked { background: linear-gradient(135deg, #7F1D1D 0%, #991B1B 60%, #B91C1C 100%); color: #FFFFFF; }
        .status-banner.is-neutral { background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%); color: #FFFFFF; }
        .status-icon { width: 44px; height: 44px; border-radius: 50%; background: rgba(255,255,255,0.16); backdrop-filter: blur(4px); border: 1.5px solid rgba(255,255,255,0.35); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .status-icon svg { width: 22px; height: 22px; }
        .status-banner h1 { font-size: 17px; font-weight: 800; letter-spacing: -0.01em; line-height: 1.25; margin: 0; }
        .status-banner p { font-size: 12.5px; opacity: 0.9; margin-top: 3px; line-height: 1.4; }
        .status-meta { text-align: right; flex-shrink: 0; display: flex; flex-direction: column; gap: 4px; }
        .badge { display: inline-flex; align-items: center; gap: 5px; border-radius: 9999px; padding: 3px 10px; font-size: 10.5px; font-weight: 700; letter-spacing: 0.04em; text-transform: uppercase; }
        .badge.active { background: var(--color-verified-bg); color: var(--color-verified-forest); border: 1px solid var(--color-verified-border); }
        .badge.revoked { background: var(--color-revoked-bg); color: var(--color-revoked-crimson); border: 1px solid var(--color-revoked-border); }
        .record-id { font-size: 10.5px; opacity: 0.8; font-family: var(--font-mono); }

        /* ========== Card Body ========== */
        .card-body { padding: 28px 30px 32px; position: relative; }

        /* Watermark */
        .watermark {
            position: absolute; right: 24px; bottom: 32px; width: 180px; height: 180px;
            opacity: 0.035; pointer-events: none; background-size: contain; background-position: center; background-repeat: no-repeat; z-index: 0;
        }

        /* Certificate Reference */
        .cert-ref {
            display: flex; align-items: center; justify-content: space-between; gap: 16px;
            padding: 14px 18px; background: #F8FAFC; border: 1px solid var(--color-gov-border); border-radius: var(--radius-md);
            margin-bottom: 24px; flex-wrap: wrap; position: relative; z-index: 1;
        }
        .cert-no-block { display: flex; flex-direction: column; gap: 4px; }
        .cert-no-label { font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-gov-muted); }
        .cert-no-row { display: flex; align-items: center; gap: 8px; }
        .cert-no-pill.mono { font-family: var(--font-mono); font-size: 16px; font-weight: 800; color: var(--color-gov-navy); letter-spacing: 0.02em; }
        .btn-copy { background: #fff; border: 1px solid var(--color-gov-border); border-radius: 4px; padding: 2px 7px; font-size: 10.5px; font-weight: 600; color: var(--color-gov-slate); cursor: pointer; display: inline-flex; align-items: center; gap: 4px; transition: all 0.15s ease; }
        .btn-copy:hover { border-color: #94A3B8; color: var(--color-gov-navy); }
        .cert-status-row { display: flex; align-items: center; gap: 10px; }
        .badge { display: inline-flex; align-items: center; gap: 5px; border-radius: 9999px; padding: 3px 10px; font-size: 10.5px; font-weight: 700; letter-spacing: 0.04em; text-transform: uppercase; }
        .badge.active { background: var(--color-verified-bg); color: var(--color-verified-forest); border: 1px solid var(--color-verified-border); }
        .badge.revoked { background: var(--color-revoked-bg); color: var(--color-revoked-crimson); border: 1px solid var(--color-revoked-border); }
        .cert-status-row time { font-size: 11px; color: var(--color-gov-muted); font-weight: 600; }

        /* Sections */
        .section { margin-bottom: 22px; position: relative; z-index: 1; }
        .section h2 {
            font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-gov-navy);
            display: flex; align-items: center; gap: 10px; margin-bottom: 14px; padding-bottom: 7px; border-bottom: 1.5px solid var(--color-gov-border);
        }
        .section-num {
            display: inline-flex; align-items: center; justify-content: center; min-width: 22px; height: 20px; padding: 0 4px;
            border-radius: 4px; background: #F1F5F9; color: var(--color-gov-navy); font-family: var(--font-serif);
            font-weight: 700; font-size: 10.5px; letter-spacing: 0.02em; border: 1px solid var(--color-gov-border); flex-shrink: 0;
        }

        /* Info Grid */
        .info-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px 22px; }
        .item { display: flex; flex-direction: column; gap: 3px; }
        .item.span-2 { grid-column: span 2; }
        .item dt { font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: var(--color-gov-muted); }
        .item dd { font-size: 14px; font-weight: 600; color: var(--color-gov-navy); line-height: 1.4; }
        .name { font-size: 17px; font-weight: 800; letter-spacing: -0.01em; color: var(--color-gov-navy); display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .ja-tag { font-size: 12.5px; font-weight: 600; color: #334155; background: #F1F5F9; border: 1px solid #E2E8F0; border-radius: 6px; padding: 2px 8px; display: inline-block; }
        .level-badge { display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; font-size: 11.5px; font-weight: 700; color: #1E40AF; background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 4px; margin-top: 3px; }

        /* Attendance Table */
        .attendance-table { width: 100%; border-collapse: collapse; margin-top: 12px; font-size: 12.5px; background: #FFFFFF; border: 1px solid var(--color-gov-border); border-radius: var(--radius-sm); overflow: hidden; }
        .attendance-table th { background-color: #F8FAFC; color: #334155; font-size: 10.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; padding: 9px 12px; border: 1px solid var(--color-gov-border); text-align: center; }
        .attendance-table td { padding: 11px 12px; border: 1px solid var(--color-gov-border); text-align: center; vertical-align: middle; }
        .attendance-table .num { font-size: 17px; font-weight: 800; font-family: var(--font-mono); color: var(--color-gov-navy); line-height: 1.2; }
        .attendance-table .badge.done { background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; border-radius: 4px; padding: 2px 8px; font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em; }

        /* Attestation + Seal */
        .attestation {
            display: grid; grid-template-columns: 1fr auto; gap: 20px; align-items: center;
            padding: 16px 20px; background: #F8FAFC; border: 1px solid var(--color-gov-border); border-radius: var(--radius-sm);
            margin-top: 20px; position: relative; z-index: 1;
        }
        .attestation-text { font-size: 11.5px; color: #475569; line-height: 1.6; }
        .attestation-text strong { color: var(--color-gov-navy); }
        .attestation-meta { margin-top: 8px; padding-top: 7px; border-top: 1px dashed var(--color-gov-border); font-size: 10.5px; color: #64748B; display: flex; align-items: center; gap: 14px; flex-wrap: wrap; }
        .attestation-meta code { font-family: var(--font-mono); }
        .seal { width: 80px; height: 80px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; }
        .seal svg { width: 100%; height: 100%; display: block; }

        /* SHA-256 Collapsible */
        .sha256 { margin-top: 6px; }
        .sha256 summary { display: flex; align-items: center; justify-content: space-between; gap: 12px; cursor: pointer; user-select: none; padding: 4px 0; }
        .sha256 summary svg { color: var(--color-gov-muted); flex-shrink: 0; }
        .sha256 summary::-webkit-details-marker { display: none; }
        .sha256-box { background: #F8FAFC; border: 1px solid var(--color-gov-border); border-radius: var(--radius-sm); padding: 8px 12px; display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-top: 6px; }
        .sha256-box code { font-family: var(--font-mono); font-size: 11px; color: #475569; word-break: break-all; line-height: 1.4; flex: 1; }
        .sha256 .btn-copy { background: #fff; border: 1px solid var(--color-gov-border); border-radius: 4px; padding: 2px 7px; font-size: 10.5px; font-weight: 600; color: var(--color-gov-slate); cursor: pointer; display: inline-flex; align-items: center; gap: 4px; transition: all 0.15s ease; }
        .sha256 .btn-copy:hover { border-color: #94A3B8; color: var(--color-gov-navy); }

        /* Privacy Notice */
        .privacy-notice { display: flex; align-items: flex-start; gap: 10px; background: #F8FAFC; border: 1px solid var(--color-gov-border); border-radius: var(--radius-sm); padding: 9px 13px; margin-top: 16px; font-size: 11px; color: #64748B; line-height: 1.45; }
        .privacy-notice svg { color: var(--color-gold-accent); flex-shrink: 0; margin-top: 1px; }

        /* Actions */
        .actions { margin-top: 26px; padding-top: 20px; border-top: 1px solid var(--color-gov-border); display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap; position: relative; z-index: 1; }
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 7px; padding: 10px 18px; font-size: 13px; font-weight: 700; border-radius: var(--radius-sm); text-decoration: none; cursor: pointer; transition: all 0.15s ease; white-space: nowrap; border: none; }
        .btn svg { width: 13px; height: 13px; }
        .btn-web { background: var(--color-gov-navy); color: #fff; border: 1px solid var(--color-gov-navy); box-shadow: 0 2px 6px rgba(10,25,47,0.18); }
        .btn-web:hover { background: var(--color-gov-navy-light); border-color: var(--color-gov-navy-light); transform: translateY(-1px); }
        .btn-print { background: #fff; color: var(--color-gov-navy); border: 1px solid var(--color-gov-border); }
        .btn-print:hover { background: #F8FAFC; border-color: #CBD5E1; }
        .btn-verify { background: transparent; color: var(--color-gov-slate); border: 1px solid transparent; }
        .btn-verify:hover { background: #F1F5F9; color: var(--color-gov-navy); }

        /* Cert Ref */
        .cert-ref { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 14px 18px; background: #F8FAFC; border: 1px solid var(--color-gov-border); border-radius: var(--radius-md); margin-bottom: 24px; flex-wrap: wrap; position: relative; z-index: 1; }
        .cert-no-block { display: flex; flex-direction: column; gap: 4px; }
        .cert-no-label { font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-gov-muted); }
        .cert-no-row { display: flex; align-items: center; gap: 8px; }
        .cert-no-pill.mono { font-family: var(--font-mono); font-size: 16px; font-weight: 800; color: var(--color-gov-navy); letter-spacing: 0.02em; }
        .cert-status-row { display: flex; align-items: center; gap: 10px; }
        .badge.active { background: var(--color-verified-bg); color: var(--color-verified-forest); border: 1px solid var(--color-verified-border); }
        .badge.revoked { background: var(--color-revoked-bg); color: var(--color-revoked-crimson); border: 1px solid var(--color-revoked-border); }
        .cert-status-row time { font-size: 11px; color: var(--color-gov-muted); font-weight: 600; }

        /* Watermark */
        .watermark { position: absolute; right: 24px; bottom: 32px; width: 180px; height: 180px; opacity: 0.035; pointer-events: none; background-size: contain; background-position: center; background-repeat: no-repeat; z-index: 0; }

        /* Card Body */
        .card-body { padding: 28px 30px 32px; position: relative; }

        /* Print Header */
        .print-header { display: flex; align-items: center; gap: 14px; padding-bottom: 8px; border-bottom: 2px solid #0A192F; margin-bottom: 12px; }
        .print-logo-wrap { width: 48px; height: 48px; flex-shrink: 0; }
        .print-logo-wrap img { width: 100%; height: 100%; object-fit: contain; }
        .print-header-titles h2 { font-family: var(--font-serif); font-size: 16.5px; font-weight: 800; color: #0A192F; margin: 0; line-height: 1.15; letter-spacing: 0.02em; }
        .print-subhead { font-size: 10px; font-weight: 800; color: #B45309; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 2px; }
        .print-meta { font-size: 8.5px; color: #475569; margin-top: 2px; line-height: 1.35; }

        /* Print Record */
        .print-record { display: flex; align-items: center; justify-content: space-between; padding: 8px 14px; background: #F8FAFC; border: 1px solid #CBD5E1; border-radius: 4px; margin-bottom: 14px; font-size: 9.5px; }
        .rec-no { font-family: var(--font-mono); font-size: 12.5px; font-weight: 800; color: #0A192F; }
        .rec-status { font-weight: 800; color: #065F46; background: #ECFDF5; border: 1px solid #A7F3D0; padding: 2px 8px; border-radius: 3px; font-size: 9px; letter-spacing: 0.03em; }
        .rec-meta { color: #475569; font-size: 8.5px; font-weight: 600; }

        /* Print Only / Screen Only */
        .print-only { display: none !important; }
        .screen-only { display: block; }

        @media print {
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; box-sizing: border-box !important; }
            html, body { background: #fff !important; color: #0F172A !important; margin: 0 !important; padding: 0 !important; font-size: 10.5px !important; line-height: 1.45 !important; }
            .screen-only, .gov-header, .actions, .btn-copy, #toast-copied, .gov-nav-btn, .privacy-notice, .gov-footer-notice, .watermark, .status-banner, .cert-ref { display: none !important; }
            .print-only { display: block !important; }
            .gov-main { padding: 0 !important; margin: 0 !important; width: 100% !important; max-width: 100% !important; }
            .gov-container { max-width: 100% !important; width: 100% !important; padding: 0 !important; }
            .credential-card { border: none !important; border-radius: 0 !important; box-shadow: none !important; background: transparent !important; padding: 0 !important; margin: 0 !important; page-break-inside: avoid !important; break-inside: avoid !important; }
            .card-body { padding: 0 !important; }
            .print-header { display: flex !important; align-items: center; gap: 14px; padding-bottom: 8px !important; border-bottom: 2px solid #0A192F !important; margin-bottom: 12px !important; }
            .print-header::after { content: ''; position: absolute; bottom: -4px; left: 0; right: 0; height: 0.75px; background: #0A192F; }
            .print-logo-wrap { width: 48px; height: 48px; flex-shrink: 0; }
            .print-logo-wrap img { width: 100%; height: 100%; object-fit: contain; }
            .print-header-titles h2 { font-family: var(--font-serif); font-size: 16.5px; font-weight: 800; color: #0A192F; margin: 0; line-height: 1.15; letter-spacing: 0.02em; }
            .print-subhead { font-size: 10px; font-weight: 800; color: #B45309; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 2px; }
            .print-meta { font-size: 8.5px; color: #475569; margin-top: 2px; line-height: 1.35; }
            .print-record { display: flex !important; align-items: center; justify-content: space-between; padding: 8px 14px !important; background: #F8FAFC !important; border: 1px solid #CBD5E1 !important; border-radius: 4px !important; margin-bottom: 14px !important; font-size: 9.5px !important; }
            .rec-no { font-family: var(--font-mono); font-size: 12.5px; font-weight: 800; color: #0A192F; }
            .rec-status { font-weight: 800; color: #065F46; background: #ECFDF5; border: 1px solid #A7F3D0; padding: 2px 8px; border-radius: 3px; font-size: 9px; letter-spacing: 0.03em; }
            .rec-meta { color: #475569; font-size: 8.5px; font-weight: 600; }
            .section { margin-bottom: 10px !important; page-break-inside: avoid !important; break-inside: avoid !important; }
            .section h2 { font-size: 10px !important; font-weight: 800 !important; letter-spacing: 0.05em !important; text-transform: uppercase !important; color: #0A192F !important; border-bottom: 1px solid #0A192F !important; padding-bottom: 3px !important; margin-bottom: 6px !important; }
            .section-num { background: transparent !important; border: none !important; font-size: 10.5px !important; min-width: auto !important; height: auto !important; padding: 0 !important; margin-right: 2px !important; }
            .info-grid { gap: 5px 18px !important; }
            .item dt { font-size: 8px !important; font-weight: 700 !important; color: #475569 !important; }
            .item dd { font-size: 11px !important; font-weight: 600 !important; color: #0F172A !important; }
            .name { font-size: 13.5px !important; font-weight: 800 !important; }
            .ja-tag { font-size: 10px !important; padding: 1px 6px !important; border: 1px solid #CBD5E1 !important; background: #F8FAFC !important; color: #1E293B !important; }
            .level-badge { font-size: 9px !important; padding: 1px 5px !important; border: 1px solid #94A3B8 !important; background: #fff !important; color: #0F172A !important; }
            .attendance-table { margin-top: 6px !important; border: 1px solid #334155 !important; page-break-inside: avoid !important; break-inside: avoid !important; }
            .attendance-table th { background-color: #F1F5F9 !important; color: #0F172A !important; font-size: 8.5px !important; font-weight: 800 !important; padding: 6px 8px !important; border: 1px solid #CBD5E1 !important; }
            .attendance-table td { padding: 6px 8px !important; border: 1px solid #CBD5E1 !important; }
            .attendance-table .num { font-size: 13px !important; font-weight: 800 !important; color: #0A192F !important; }
            .attendance-table .badge.done { font-size: 8.5px !important; padding: 1px 5px !important; border: 1px solid #059669 !important; color: #065F46 !important; background: #ECFDF5 !important; }
            .sha256-box { padding: 3px 6px !important; background: #F8FAFC !important; border: 1px solid #CBD5E1 !important; margin-top: 2px !important; }
            .sha256-box code { font-size: 8px !important; color: #334155 !important; }
            .attestation { display: grid !important; grid-template-columns: 1fr 110px !important; gap: 14px !important; align-items: center !important; padding: 10px 12px !important; border: 1px solid #CBD5E1 !important; border-radius: 4px !important; background: #FAFAFA !important; margin-top: 8px !important; page-break-inside: avoid !important; break-inside: avoid !important; }
            .attestation-text { font-size: 8px !important; line-height: 1.45 !important; color: #334155 !important; }
            .attestation-meta { margin-top: 5px !important; padding-top: 5px !important; font-size: 8px !important; }
            .seal { width: 80px !important; height: 80px !important; margin: 0 auto !important; }
            .seal svg { width: 80px !important; height: 80px !important; }
            .privacy-notice { display: none !important; }
            .actions { display: none !important; }
            .gov-header, .gov-footer, .gov-footer-notice, .watermark, .status-banner, .cert-ref, .btn-copy { display: none !important; }
        }

        @media (max-width: 680px) {
            .gov-header-inner { padding: 10px 16px; }
            .gov-brand-title { font-size: 14px; }
            .gov-brand-subtitle { font-size: 10px; }
            .gov-footer { margin-top: 22px; }
            .gov-footer-notice { padding: 12px; }
            .status-banner { padding: 18px 18px; flex-direction: column; align-items: flex-start; }
            .status-meta { text-align: left; margin-top: 8px; width: 100%; flex-direction: row; flex-wrap: wrap; }
            .card-body { padding: 22px 18px; }
            .info-grid { grid-template-columns: 1fr; }
            .item.span-2 { grid-column: auto; }
            .cert-ref { flex-direction: column; align-items: flex-start; }
            .attestation { grid-template-columns: 1fr; text-align: center; }
            .seal { margin: 0 auto; }
            .actions { flex-direction: column; align-items: stretch; }
            .btn { width: 100%; justify-content: center; }
        }
    </style>
    @yield('styles')
</head>
<body>
    <!-- Institutional Top Navigation Header -->
    <header class="gov-header">
        <div class="gov-header-inner">
            <a href="{{ route('verify.form') }}" class="gov-brand" title="{{ $school['name'] }}">
                <div class="gov-logo-frame">
                    <img src="{{ asset($school['images']['logo']) }}" alt="{{ $school['name'] }}">
                </div>
                <div class="gov-brand-text">
                    <span class="gov-brand-title">{{ $school['name'] }}</span>
                    <span class="gov-brand-subtitle">
                        Official Credential Registry <span class="dot"></span> Japanese Studies
                    </span>
                </div>
            </a>

            <div class="gov-header-actions">
                <a href="{{ $school['website'] ?? config('school.website', 'https://eminentoverseas.uk') }}"
                   target="_blank" rel="noopener noreferrer" class="gov-nav-btn btn-primary" title="Visit the official school website">
                    <span>Visit Website</span>
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                        <polyline points="15 3 21 3 21 9"/>
                        <line x1="10" y1="14" x2="21" y2="3"/>
                    </svg>
                </a>

                <a href="{{ route('verify.form') }}" class="gov-nav-btn btn-outline" title="Verify another credential">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <span>Search Registry</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="gov-main">
        <div class="gov-container">
            @yield('content')
        </div>

        <!-- Institutional Footer -->
        <footer class="gov-footer">
            <div class="gov-footer-notice">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                <div>
                    <strong>Notice to Consulates, Embassies, and Immigration Authorities (出入国在留管理庁):</strong>
                    This verification portal validates certified records issued by {{ $school['name'] }}. Records displayed here reflect the verified archival database. For diplomatic or consular queries, contact the Registrar at <a href="mailto:{{ $school['email'] }}">{{ $school['email'] }}</a> or telephone {{ $school['phone'] }}.
                </div>
            </div>

            <div class="gov-footer-legal">
                <strong>{{ $school['name'] }}</strong> &middot; {{ $school['address'] }}<br>
                <span>Official Japanese Language Education Registry &middot; Cryptographically Signed Credential System</span>
            </div>
        </footer>
    </main>

    <div id="toast-copied" class="toast-copied">Copied to clipboard</div>

    <script>
        function copyText(text, label) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text);
            } else {
                const input = document.createElement('textarea');
                input.value = text;
                input.style.position = 'fixed';
                input.style.left = '-9999px';
                document.body.appendChild(input);
                input.focus();
                input.select();
                document.execCommand('copy');
                document.body.removeChild(input);
            }
            const toast = document.getElementById('toast-copied');
            if (toast) {
                toast.textContent = (label || 'Copied') + ' copied to clipboard';
                toast.classList.add('is-visible');
                setTimeout(() => toast.classList.remove('is-visible'), 2200);
            }
        }
    </script>
</body>
</html>
