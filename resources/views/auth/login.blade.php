<x-layouts.guest>
    @push('styles')
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Poppins', -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f0fdfa 0%, #f8fafc 50%, #f0fdf4 100%);
            position: relative;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background-image:
                radial-gradient(circle at 20% 20%, rgba(20, 184, 166, 0.03) 0%, transparent 40%),
                radial-gradient(circle at 80% 80%, rgba(20, 184, 166, 0.03) 0%, transparent 40%),
                radial-gradient(circle at 50% 50%, rgba(15, 118, 110, 0.02) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }

        .bg-shape {
            position: fixed;
            border-radius: 50%;
            opacity: 0.05;
            background: linear-gradient(135deg, #14b8a6, #0d9488);
            animation: float 20s ease-in-out infinite;
            pointer-events: none;
            z-index: 0;
        }
        .bg-shape-1 { width: 400px; height: 400px; top: -100px; right: -100px; animation-delay: 0s; }
        .bg-shape-2 { width: 300px; height: 300px; bottom: -50px; left: -50px; animation-delay: -5s; }
        .bg-shape-3 { width: 200px; height: 200px; top: 40%; left: 10%; animation-delay: -10s; }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(10deg); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .login-card {
            background: white;
            border-radius: 20px;
            padding: 48px 40px;
            width: 100%;
            max-width: 420px;
            box-shadow:
                0 4px 6px -1px rgba(0,0,0,0.02),
                0 10px 15px -3px rgba(0,0,0,0.04),
                0 20px 40px -10px rgba(20,184,166,0.08);
            position: relative;
            z-index: 1;
            animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(20, 184, 166, 0.08);
        }

        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .brand-logo {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 8px 24px rgba(20,184,166,0.25);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .brand-logo:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(20,184,166,0.35);
        }
        .brand-logo svg { width: 28px; height: 28px; fill: white; }

        .login-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
            letter-spacing: -0.02em;
        }
        .login-title span {
            background: linear-gradient(135deg, #14b8a6, #0d9488);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .login-subtitle {
            font-size: 0.85rem;
            color: #64748b;
            font-weight: 400;
        }

        .error-msg {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-left: 4px solid #ef4444;
            color: #991b1b;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 24px;
            font-size: 0.8rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: shake 0.4s ease-in-out;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-4px); }
            40%, 80% { transform: translateX(4px); }
        }

        .form-group { margin-bottom: 20px; }

        .form-label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-wrap { position: relative; }
        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            opacity: 0.5;
            transition: opacity 0.2s ease;
            pointer-events: none;
            color: #64748b;
        }
        .input-wrap:focus-within .input-icon { opacity: 1; color: #14b8a6; }
        .input-wrap input {
            width: 100%;
            padding: 14px 16px 14px 44px;
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-family: inherit;
            font-size: 0.95rem;
            color: #0f172a;
            outline: none;
            transition: all 0.25s ease;
            height: 50px;
        }
        .input-wrap input:focus {
            border-color: #14b8a6;
            background: white;
            box-shadow: 0 0 0 4px rgba(20,184,166,0.1);
        }
        .input-wrap input::placeholder { color: #94a3b8; font-weight: 400; }

        .pin-dots {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-top: 8px;
        }
        .pin-dot-input {
            width: 56px;
            height: 64px;
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            text-align: center;
            font-size: 1.6rem;
            font-weight: 700;
            font-family: inherit;
            color: #0f172a;
            background: #f8fafc;
            outline: none;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            -webkit-appearance: none;
        }
        .pin-dot-input:focus {
            border-color: #14b8a6;
            background: white;
            box-shadow: 0 0 0 4px rgba(20,184,166,0.1);
            transform: scale(1.05);
        }
        .pin-dot-input.filled {
            border-color: #14b8a6;
            background: #f0fdfa;
        }

        .btn-submit {
            width: 100%;
            padding: 16px;
            background: #0f172a;
            color: white;
            border: none;
            border-radius: 12px;
            font-family: inherit;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            height: 54px;
            margin-top: 8px;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-submit:hover {
            background: #1e293b;
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(15,23,42,0.25);
        }
        .btn-submit:active {
            transform: translateY(0) scale(0.98);
        }
        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .form-footer {
            text-align: center;
            margin-top: 28px;
            padding-top: 24px;
            border-top: 1px solid #f1f5f9;
        }
        .form-footer p { font-size: 0.75rem; color: #94a3b8; }
        .form-footer .brand-name { color: #0f172a; font-weight: 600; }

        @keyframes spin { to { transform: rotate(360deg); } }

        @media (max-width: 480px) {
            body { padding: 16px; align-items: flex-start; padding-top: 40px; }
            .login-card { padding: 32px 24px; border-radius: 16px; }
            .login-title { font-size: 1.3rem; }
            .pin-dot-input { width: 48px; height: 56px; font-size: 1.4rem; }
            .pin-dots { gap: 8px; }
        }
    </style>
    @endpush

    <div class="bg-shape bg-shape-1"></div>
    <div class="bg-shape bg-shape-2"></div>
    <div class="bg-shape bg-shape-3"></div>

    <div class="login-card">
        <div class="login-header">
            <div class="brand-logo">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13 3L4 14h7l-2 7 9-11h-7l2-7z"/>
                </svg>
            </div>
            <h1 class="login-title">Getlead <span>HQ</span></h1>
            <p class="login-subtitle">Sign in to your workspace</p>
        </div>

        @if ($errors->any())
            <div class="error-msg">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}" id="loginForm">
            @csrf
            <input type="hidden" name="pin" id="pinHidden" value="">

            <div class="form-group">
                <label class="form-label">Mobile Number</label>
                <div class="input-wrap">
                    <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="5" y="2" width="14" height="20" rx="2" ry="2"/>
                        <line x1="12" y1="18" x2="12.01" y2="18"/>
                    </svg>
                    <input type="tel" name="mobile" id="mobileInput"
                           inputmode="tel" placeholder="Enter your mobile number"
                           required autocomplete="tel" maxlength="13"
                           value="{{ old('mobile') }}">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">4-Digit PIN</label>
                <div class="pin-dots">
                    <input type="password" class="pin-dot-input" inputmode="numeric" maxlength="1" data-pin="1" autocomplete="off">
                    <input type="password" class="pin-dot-input" inputmode="numeric" maxlength="1" data-pin="2" autocomplete="off">
                    <input type="password" class="pin-dot-input" inputmode="numeric" maxlength="1" data-pin="3" autocomplete="off">
                    <input type="password" class="pin-dot-input" inputmode="numeric" maxlength="1" data-pin="4" autocomplete="off">
                </div>
            </div>

            <button type="submit" class="btn-submit" id="submitBtn">
                Sign In
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"/>
                    <polyline points="12 5 19 12 12 19"/>
                </svg>
            </button>
        </form>

        <div class="form-footer">
            <p><span class="brand-name">Getlead Analytics Pvt Ltd</span><br>Your team's command center</p>
        </div>
    </div>

    <script>
        const pinInputs = document.querySelectorAll('.pin-dot-input');
        const pinHidden = document.getElementById('pinHidden');
        const mobileInput = document.getElementById('mobileInput');
        const form = document.getElementById('loginForm');

        pinInputs.forEach((input, idx) => {
            input.addEventListener('input', function () {
                const val = this.value.replace(/\D/g, '');
                this.value = val;
                if (val) {
                    this.classList.add('filled');
                    if (idx < pinInputs.length - 1) pinInputs[idx + 1].focus();
                } else {
                    this.classList.remove('filled');
                }
                updatePin();
            });

            input.addEventListener('keydown', function (e) {
                if (e.key === 'Backspace' && !this.value && idx > 0) {
                    pinInputs[idx - 1].focus();
                    pinInputs[idx - 1].value = '';
                    pinInputs[idx - 1].classList.remove('filled');
                    updatePin();
                }
                if (e.key === 'Enter') { e.preventDefault(); form.requestSubmit(); }
            });

            input.addEventListener('paste', function (e) {
                e.preventDefault();
                const paste = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
                for (let i = 0; i < Math.min(paste.length, 4); i++) {
                    pinInputs[i].value = paste[i];
                    pinInputs[i].classList.add('filled');
                }
                if (paste.length >= 4) pinInputs[3].focus();
                else if (paste.length > 0) pinInputs[Math.min(paste.length, 3)].focus();
                updatePin();
            });

            input.addEventListener('focus', function () { this.select(); });
        });

        function updatePin() {
            let pin = '';
            pinInputs.forEach(i => pin += i.value);
            pinHidden.value = pin;
        }

        mobileInput.addEventListener('input', function () {
            if (this.value.replace(/\D/g, '').length >= 10) pinInputs[0].focus();
        });

        form.addEventListener('submit', function (e) {
            updatePin();
            if (pinHidden.value.length !== 4) {
                e.preventDefault();
                pinInputs[0].focus();
                return;
            }
            document.getElementById('submitBtn').innerHTML =
                '<span style="display:inline-flex;align-items:center;gap:8px;">' +
                '<span style="width:18px;height:18px;border:2px solid rgba(255,255,255,0.3);border-top-color:white;border-radius:50%;animation:spin .6s linear infinite;"></span>' +
                'Signing in...</span>';
            document.getElementById('submitBtn').disabled = true;
        });
    </script>
</x-layouts.guest>
