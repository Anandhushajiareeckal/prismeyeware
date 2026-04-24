<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — Prism Eyewear</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/img/logo/fav.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            background: #f0f4ff;
            overflow: hidden;
        }

        /* ─── LEFT PANEL ─────────────────────────────── */
        .left-panel {
            width: 52%;
            background: linear-gradient(145deg, #0a1f5c 0%, #0d47c4 45%, #1a6cdb 75%, #3b8ef7 100%);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            padding: 60px 80px;
            overflow: hidden;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }

        /* Decorative circles */
        .left-panel::before {
            content: '';
            position: absolute;
            top: -180px; right: -180px;
            width: 520px; height: 520px;
            border-radius: 50%;
            background: rgba(255,255,255,0.06);
            animation: pulse 8s infinite alternate;
        }
        .left-panel::after {
            content: '';
            position: absolute;
            bottom: -140px; left: -140px;
            width: 420px; height: 420px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
            animation: pulse 12s infinite alternate-reverse;
        }
        @keyframes pulse {
            from { transform: scale(1); opacity: 0.05; }
            to { transform: scale(1.1); opacity: 0.08; }
        }

        .circle-3 {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            width: 700px; height: 700px;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,0.06);
        }

        .panel-logo {
            position: relative;
            z-index: 2;
            margin-bottom: 25px;
            background: rgb(255 255 255);
            backdrop-filter: blur(10px);
            padding: 3px 10px;
            border-radius: 20px;
            display: inline-block;
            box-shadow: 0 8px 32px rgba(0,0,0,0.15);
            transition: transform 0.3s ease;
            align-self: center;
        }
        .panel-logo:hover {
            transform: translateY(-5px);
        }
        .panel-logo img {
            height: 104px;
            width: auto;
            object-fit: contain;
            display: block;
        }

        .panel-headline {
            position: relative;
            z-index: 2;
            color: #fff;
            margin-bottom: 32px;
        }
        .panel-headline h1 {
            font-size: 48px;
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: -1.5px;
            margin-bottom: 20px;
        }
        .panel-headline h1 span {
            display: block;
            background: linear-gradient(90deg, #a8d0ff, #e0eeff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .panel-headline p {
            font-size: 18px;
            color: rgba(255,255,255,0.8);
            line-height: 1.6;
            max-width: 440px;
            font-weight: 400;
        }

        .panel-features {
            position: relative;
            z-index: 2;
            margin-top: 50px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .panel-feature {
            display: flex;
            align-items: center;
            gap: 16px;
            color: rgba(255,255,255,0.9);
            font-size: 15px;
            font-weight: 500;
            transition: transform 0.2s;
        }
        .panel-feature:hover { transform: translateX(10px); }
        .panel-feature .feat-icon {
            width: 42px; height: 42px;
            border-radius: 12px;
            background: rgba(255,255,255,0.15);
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
            backdrop-filter: blur(4px);
        }

        /* ─── RIGHT PANEL ────────────────────────────── */
        .right-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 40px;
            background: #fff;
            animation: fadeInRight 0.8s ease-out;
        }

        @keyframes fadeInRight {
            from { opacity: 0; transform: translateX(20px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .login-card {
            width: 100%;
            max-width: 420px;
        }

        .login-card .eyebrow {
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #1a6cdb;
            margin-bottom: 12px;
            opacity: 0.8;
        }
        .login-card h2 {
            font-size: 34px;
            font-weight: 800;
            color: #0a1f5c;
            letter-spacing: -0.8px;
            margin-bottom: 8px;
        }
        .login-card .subtitle {
            font-size: 15px;
            color: #718096;
            margin-bottom: 40px;
        }

        /* Form styling */
        .form-group { margin-bottom: 24px; }
        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 10px;
        }

        .input-wrap {
            position: relative;
        }
        .input-wrap .icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            font-size: 18px;
            pointer-events: none;
            transition: all 0.3s;
        }
        .input-wrap input {
            width: 100%;
            padding: 14px 48px 14px 52px;
            border: 2px solid #edf2f7;
            border-radius: 16px;
            font-size: 15px;
            font-family: 'Inter', sans-serif;
            color: #1a202c;
            background: #f7fafc;
            outline: none;
            transition: all 0.3s;
        }
        .input-wrap input:focus {
            border-color: #1a6cdb;
            background: #fff;
            box-shadow: 0 0 0 5px rgba(26, 108, 219, 0.1);
        }
        .input-wrap input:focus + .icon { color: #1a6cdb; transform: translateY(-50%) scale(1.1); }

        .input-wrap .toggle-pw {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #a0aec0;
            font-size: 18px;
            padding: 4px;
            transition: color 0.2s;
        }
        .input-wrap .toggle-pw:hover { color: #1a6cdb; }

        /* Error */
        .input-error {
            color: #e53e3e;
            font-size: 13px;
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Remember */
        .remember-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 32px;
        }
        .remember-row label {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: #4a5568;
            cursor: pointer;
            user-select: none;
        }
        .remember-row input[type="checkbox"] {
            width: 18px; height: 18px;
            accent-color: #1a6cdb;
            cursor: pointer;
        }

        /* Login button */
        .btn-login {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #1a6cdb, #0a47c2);
            border: none;
            border-radius: 16px;
            color: #fff;
            font-size: 16px;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 10px 20px rgba(26, 108, 219, 0.25);
            letter-spacing: 0.5px;
        }
        .btn-login:hover {
            transform: translateY(-3px) scale(1.01);
            box-shadow: 0 15px 30px rgba(26, 108, 219, 0.35);
        }
        .btn-login:active { transform: translateY(-1px) scale(0.99); }

        /* Alert error */
        .alert-error {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #fff5f5;
            border: 1px solid #feb2b2;
            border-radius: 12px;
            padding: 14px 18px;
            margin-bottom: 24px;
            font-size: 14px;
            color: #c53030;
            box-shadow: 0 4px 6px rgba(197, 48, 48, 0.05);
        }
        .alert-error i { font-size: 18px; flex-shrink: 0; }

        /* Footer */
        .login-footer {
            margin-top: 40px;
            text-align: center;
            font-size: 13px;
            color: #a0aec0;
        }
        .login-footer a { color: #1a6cdb; text-decoration: none; font-weight: 600; }

        /* Security badge */
        .security-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 28px;
            font-size: 13px;
            color: #a0aec0;
        }
        .security-badge i { color: #48bb78; font-size: 15px; }

        /* Responsive */
        @media (max-width: 992px) {
            .left-panel { width: 45%; padding: 40px 50px; }
            .panel-logo img { height: 60px; }
            .panel-headline h1 { font-size: 36px; }
            .panel-headline p { font-size: 16px; }
        }

        @media (max-width: 768px) {
            .left-panel { display: none; }
            .right-panel { padding: 32px 24px; }
        }
    </style>
</head>
<body>
    <!-- Left brand panel -->
    <div class="left-panel">
        <div class="circle-3"></div>

        <div class="panel-logo">
            <img src="{{ asset('assets/img/logo/logo.jpg') }}" alt="Prism Eyewear">
        </div>

        <div class="panel-headline">
            <h1>
                Welcome to
                <span>Prism Eyewear</span>
            </h1>
            <p>Your all-in-one management platform for customers, repairs, orders, and billing — all in one place.</p>
        </div>

        <div class="panel-features">
            <div class="panel-feature">
                <div class="feat-icon"><i class="bi bi-people-fill"></i></div>
                Customer &amp; Prescription Management
            </div>
            <div class="panel-feature">
                <div class="feat-icon"><i class="bi bi-tools"></i></div>
                Repair Jobs &amp; Tracking
            </div>
            <div class="panel-feature">
                <div class="feat-icon"><i class="bi bi-receipt"></i></div>
                Invoicing &amp; Financial Reports
            </div>
        </div>
    </div>

    <!-- Right login panel -->
    <div class="right-panel">
        <div class="login-card">
            <div class="eyebrow">Prism Management System</div>
            <h2>Sign in to your account</h2>
            <p class="subtitle">Enter your credentials to access the dashboard</p>

            @if($errors->any())
            <div class="alert-error">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span>{{ $errors->first() }}</span>
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}" autocomplete="on">
                @csrf

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrap">
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            autocomplete="email"
                            placeholder="admin@prismeyewear.com"
                            required
                        >
                        <i class="bi bi-envelope icon"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            autocomplete="current-password"
                            placeholder="••••••••"
                            required
                        >
                        <i class="bi bi-lock icon"></i>
                        <button type="button" class="toggle-pw" onclick="togglePassword()" id="toggleBtn" title="Show/Hide password">
                            <i class="bi bi-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="remember-row">
                    <label>
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        Keep me signed in
                    </label>
                </div>

                <button type="submit" class="btn-login">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Sign In
                </button>
            </form>

            <div class="security-badge">
                <i class="bi bi-shield-lock-fill"></i>
                Secured with 256-bit encryption &amp; rate limiting
            </div>

            <div class="login-footer">
                &copy; {{ date('Y') }} Prism Eyewear. All rights reserved.
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const pw  = document.getElementById('password');
            const eye = document.getElementById('eyeIcon');
            if (pw.type === 'password') {
                pw.type = 'text';
                eye.className = 'bi bi-eye-slash';
            } else {
                pw.type = 'password';
                eye.className = 'bi bi-eye';
            }
        }
    </script>
</body>
</html>
