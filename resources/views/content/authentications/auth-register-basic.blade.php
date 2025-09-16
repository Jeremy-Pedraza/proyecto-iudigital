@extends('layouts/blankLayout')

@section('title', 'Register Basic - Pages')

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection


@section('content')
    <div class="position-relative">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-6 mx-4">

                <!-- Register Card -->
                <div class="card p-7">
                    <!-- Logo -->
                    <div class="app-brand justify-content-center mt-5">
                        <a href="{{ route('welcome') }}" class="app-brand-link gap-3">
                            <span class="app-brand-logo demo">@include('_partials.macros', ['height' => 20])</span>
                            <span
                                class="app-brand-text demo text-heading fw-semibold">{{ config('variables.templateName') }}</span>
                        </a>
                    </div>
                    <!-- /Logo -->
                    <div class="card-body mt-1">
                        <h4 class="mb-1">Adventure starts here 🚀</h4>
                        <p class="mb-5">Make your app management easy and fun!</p>

                        <form id="formAuthentication" class="mb-5" action="{{ route('submit-auth-register') }}"
                            method="POST">
                            @csrf
                            <div class="form-floating form-floating-outline mb-5">
                                <input type="text" class="form-control" id="username" name="username"
                                    placeholder="Enter your username" autofocus>
                                <label for="username">Username</label>
                            </div>
                            <div class="form-floating form-floating-outline mb-5">
                                <input type="text" class="form-control" id="email" name="email"
                                    placeholder="Enter your email">
                                <label for="email">Email</label>
                            </div>
                            <div class="mb-5 form-password-toggle">
                                <div class="input-group input-group-merge">
                                    <div class="form-floating form-floating-outline">
                                        <input type="password" id="password" class="form-control" name="password"
                                            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                            aria-describedby="password" />
                                        <label for="password">Password</label>
                                    </div>
                                    <span class="input-group-text cursor-pointer"><i
                                            class="ri-eye-off-line ri-20px"></i></span>
                                </div>
                                {{-- Meter de fuerza de contraseña --}}
                                <div id="password-strength" class="mt-2">
                                    <div class="progress" style="height:6px">
                                        <div class="progress-bar" id="pswBar" role="progressbar" style="width:0%"
                                            aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>

                                    <div class="d-flex justify-content-between small mt-2">
                                        <span id="pswLabel" class="fw-medium">Seguridad: —</span>
                                        <span id="pswCount">0/4</span>
                                    </div>

                                    <ul class="list-unstyled small mt-2 mb-0" id="pswChecks">
                                        <li data-check="upper"><i class="ri-close-circle-line text-muted me-1"></i>
                                            Una mayúscula (A–Z)</li>
                                        <li data-check="lower"><i class="ri-close-circle-line text-muted me-1"></i>
                                            Una minúscula (a–z)</li>
                                        <li data-check="number"><i class="ri-close-circle-line text-muted me-1"></i>
                                            Un número (0–9)</li>
                                        <li data-check="special"><i class="ri-close-circle-line text-muted me-1"></i> Un
                                            caracter
                                            especial (!@#$…)</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="mb-5 form-password-toggle">
                                <div class="input-group input-group-merge">
                                    <div class="form-floating form-floating-outline">
                                        <input type="password" id="password_confirmation" class="form-control"
                                            name="password_confirmation"
                                            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                            aria-describedby="password_confirmation" />
                                        <label for="password_confirmation">Confirm Password</label>
                                    </div>
                                    <span class="input-group-text cursor-pointer"><i
                                            class="ri-eye-off-line ri-20px"></i></span>
                                </div>
                                {{-- Mensaje de coincidencia --}}
                                <small id="confirmMessage" class="form-text mt-1"></small>
                            </div>

                            <div class="mb-5 py-2">
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="checkbox" id="terms-conditions" name="terms">
                                    <label class="form-check-label" for="terms-conditions">
                                        I agree to
                                        <a href="javascript:void(0);">privacy policy & terms</a>
                                    </label>
                                </div>
                            </div>
                            <button class="btn btn-primary d-grid w-100 mb-5">
                                Sign up
                            </button>
                        </form>

                        <p class="text-center mb-5">
                            <span>Already have an account?</span>
                            <a href="{{ route('auth-login') }}">
                                <span>Sign in instead</span>
                            </a>
                        </p>
                    </div>
                </div>
                <!-- Register Card -->
                <img src="{{ asset('assets/img/illustrations/tree-3.png') }}" alt="auth-tree"
                    class="authentication-image-object-left d-none d-lg-block">
                <img src="{{ asset('assets/img/illustrations/auth-basic-mask-light.png') }}"
                    class="authentication-image d-none d-lg-block" height="172" alt="triangle-bg">
                <img src="{{ asset('assets/img/illustrations/tree.png') }}" alt="auth-tree"
                    class="authentication-image-object-right d-none d-lg-block">
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        (function() {
            const password = document.getElementById('password');
            const bar = document.getElementById('pswBar');
            const label = document.getElementById('pswLabel');
            const count = document.getElementById('pswCount');
            const checksList = document.getElementById('pswChecks');
            const confirm = document.getElementById('password_confirmation');
            const message = document.getElementById('confirmMessage');

            // Reglas solicitadas
            const checks = {
                upper: /[A-Z]/,
                lower: /[a-z]/,
                number: /[0-9]/,
                special: /[^A-Za-z0-9]/ // cualquier caracter no alfanumérico
            };

            function updateStrength(value) {
                let passed = 0;

                Object.entries(checks).forEach(([key, regex]) => {
                    const li = checksList.querySelector(`li[data-check="${key}"]`);
                    const icon = li.querySelector('i');
                    const ok = regex.test(value);

                    if (ok) {
                        icon.classList.remove('ri-close-circle-line', 'text-muted');
                        icon.classList.add('ri-check-line', 'text-success');
                        li.classList.add('text-success');
                    } else {
                        icon.classList.add('ri-close-circle-line', 'text-muted');
                        icon.classList.remove('ri-check-line', 'text-success');
                        li.classList.remove('text-success');
                    }
                    if (ok) passed++;
                });

                const pct = (passed / 4) * 100;
                bar.style.width = pct + '%';
                bar.setAttribute('aria-valuenow', pct);

                bar.classList.remove('bg-danger', 'bg-warning', 'bg-info', 'bg-success');
                let text = '—';
                if (passed === 0) {
                    bar.classList.add('bg-danger');
                    text = 'Muy débil';
                }
                if (passed === 1) {
                    bar.classList.add('bg-danger');
                    text = 'Débil';
                }
                if (passed === 2) {
                    bar.classList.add('bg-warning');
                    text = 'Aceptable';
                }
                if (passed === 3) {
                    bar.classList.add('bg-info');
                    text = 'Buena';
                }
                if (passed === 4) {
                    bar.classList.add('bg-success');
                    text = 'Fuerte';
                }

                label.textContent = 'Seguridad: ' + text;
                count.textContent = passed + '/4';
            }

            // Bind
            if (password) {
                password.addEventListener('input', e => updateStrength(e.target.value));
                // Inicializa por si el input viene con valor (autocomplete)
                updateStrength(password.value || '');
            }

            function validateMatch() {
                if (!confirm.value) {
                    message.textContent = '';
                    message.className = 'form-text mt-1';
                    return;
                }

                if (password.value === confirm.value) {
                    message.textContent = '✔ Las contraseñas coinciden';
                    message.className = 'form-text mt-1 text-success fw-medium';
                } else {
                    message.textContent = '✘ Las contraseñas no coinciden';
                    message.className = 'form-text mt-1 text-danger fw-medium';
                }
            }

            if (password && confirm) {
                password.addEventListener('input', validateMatch);
                confirm.addEventListener('input', validateMatch);
            }

            // (Opcional) Bloquear envío si no cumple las 4 reglas:
            const form = document.getElementById('formAuthentication');
            if (form && password) {
                form.addEventListener('submit', (e) => {
                    const invalid = Object.values(checks).some(r => !r.test(password.value));
                    if (invalid) {
                        e.preventDefault();
                        password.setCustomValidity(
                            'La contraseña debe tener al menos una mayúscula, una minúscula, un número y un caracter especial.'
                        );
                        password.reportValidity();
                        setTimeout(() => password.setCustomValidity(''), 1000);
                    }
                });
            }
        })();
    </script>
@endsection
