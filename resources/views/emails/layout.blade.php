<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DrivEase</title>
    <style>
        /* ── Reset ─────────────────────────────────────────────── */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        /* ── Base ──────────────────────────────────────────────── */
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            color: #333;
            -webkit-font-smoothing: antialiased;
        }

        /* ── Wrapper ───────────────────────────────────────────── */
        .wrapper {
            max-width: 600px;
            margin: 30px auto;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        /* ── Header ────────────────────────────────────────────── */
        .header {
            background: #1E3A5F;
            padding: 28px 40px;
            text-align: center;
        }
        .header-logo {
            display: inline-block;
            vertical-align: middle;
            margin-right: 10px;
        }
        .header-title {
            color: #fff;
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 1px;
            vertical-align: middle;
        }
        .header-subtitle {
            color: #A9C4E4;
            font-size: 12px;
            margin-top: 6px;
        }

        /* ── Body ──────────────────────────────────────────────── */
        .body {
            padding: 36px 40px;
        }

        /* ── Typography ────────────────────────────────────────── */
        .greeting {
            font-size: 16px;
            margin-bottom: 16px;
        }
        .message {
            font-size: 15px;
            line-height: 1.7;
            color: #444;
            margin-bottom: 24px;
        }
        .section-title {
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #1E3A5F;
            margin-bottom: 12px;
        }

        /* ── Info Card ─────────────────────────────────────────── */
        .card {
            background: #F0F6FC;
            border-left: 4px solid #2E75B6;
            border-radius: 6px;
            padding: 20px 24px;
            margin-bottom: 24px;
        }
        .card-row {
            display: flex;
            justify-content: space-between;
            padding: 7px 0;
            border-bottom: 1px solid #dde8f3;
            font-size: 14px;
        }
        .card-row:last-child {
            border-bottom: none;
        }
        .card-row .label {
            color: #666;
        }
        .card-row .value {
            font-weight: bold;
            color: #1E3A5F;
            text-align: right;
        }

        /* ── Checklist ─────────────────────────────────────────── */
        .checklist {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }
        .checklist tr {
            border-bottom: 1px solid #eef2f7;
        }
        .checklist tr:last-child {
            border-bottom: none;
        }
        .checklist-icon {
            width: 36px;
            padding: 10px 8px 10px 0;
            vertical-align: middle;
        }
        .checklist-text {
            font-size: 14px;
            color: #374151;
            padding: 10px 0;
            vertical-align: middle;
        }

        /* ── Badge ─────────────────────────────────────────────── */
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-success { background: #E2F0D9; color: #1E7145; }
        .badge-warning { background: #FCE4D6; color: #C55A11; }
        .badge-info    { background: #D5E8F0; color: #2E75B6; }
        .badge-danger  { background: #FCE4E4; color: #9B1C1C; }

        /* ── CTA Button ────────────────────────────────────────── */
        .btn {
            display: inline-block;
            background: #2E75B6;
            color: #fff !important;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 6px;
            font-size: 15px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .btn:hover {
            background: #1E5D99;
        }

        /* ── Footer ────────────────────────────────────────────── */
        .footer {
            background: #F5F5F5;
            padding: 20px 40px;
            text-align: center;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #eee;
        }
        .footer a {
            color: #2E75B6;
            text-decoration: none;
        }
        .footer-divider {
            margin: 8px 0;
            color: #ddd;
        }
    </style>
</head>
<body>
<div class="wrapper">

    {{-- ── Header ── --}}
    <div class="header">
        {{-- SVG: steering wheel logo --}}
        <span class="header-logo">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                 fill="none" stroke="#A9C4E4" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <circle cx="12" cy="12" r="3"/>
                <line x1="12" y1="2"  x2="12" y2="9"/>
                <line x1="4.22" y1="6.22"  x2="9.17" y2="9.17"/>
                <line x1="19.78" y1="6.22" x2="14.83" y2="9.17"/>
            </svg>
        </span>
        <span class="header-title">DrivEase</span>
        <p class="header-subtitle">Rental Mobil Terpercaya</p>
    </div>

    {{-- ── Body ── --}}
    <div class="body">
        @yield('content')
    </div>

    {{-- ── Footer ── --}}
    <div class="footer">
        <p>Email ini dikirim otomatis oleh sistem Yoza Rent Car. Jangan balas email ini.</p>
        <p class="footer-divider">&#8212;</p>
        <p>Butuh bantuan? Hubungi kami via
            <a href="{{ config('app.url') }}/chat">Live Chat</a>.
        </p>
        <p style="margin-top:10px;">&copy; {{ date('Y') }} Yoza Rent Car. All rights reserved.</p>
    </div>

</div>
</body>
</html>