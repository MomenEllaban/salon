<x-auth-layout>
    <x-slot name="title">
        @lang('Login')
    </x-slot>

    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo />
            </a>
        </x-slot>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Social Login -->
        <x-auth-social-login />

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ $url ?? route('login') }}">
            @csrf

            <!-- Email Address -->
            <div>
            <x-label for="email" :value="__('Email')" />

<x-input id="email"
         type="email"
         name="email"
         :value="old('email')"
         placeholder="Enter Email"
         required
         autofocus
         pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
         title="Please enter a valid email address." />

<!-- Custom error message for invalid email format -->
<span id="emailError" class="text-danger" style="display: none;">Invalid email format</span>
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-label for="password" :value="__('Password')" />

                <x-input id="password"
                         type="password"
                         name="password"
                         placeholder="Enter  password"
                         required
                         minlength="8"
                         maxlength="12"
                         autocomplete="current-password"
                         title="Password must be between 8 and 12 characters." />

                <!-- Custom error message for invalid password length -->
                <span id="passwordError" class="text-danger" style="display: none;">Password must be between 8 and 12 characters</span>
            </div>

              

            <!-- Remember Me -->
            <div class="mt-4">
                <label for="remember_me" class="d-inline-flex">
                    <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                    <span class="ms-2">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="d-flex align-items-center justify-content-between mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900"
                        href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <x-button>
                    {{ __('Log in') }}
                </x-button>
            </div>

        </form>
        @if (env('IS_DEMO'))
            <div>
                <h6 class="text-center border-top py-3 mt-3">Demo Accounts</h6>
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="mb-0" id="admin_email">admin@salon.com</p>
                        <p id="admin_password">12345678</p>
                    </div>
                    <div>
                        <a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-title="Click To Copy"
                            onclick="setLoginCredentials('admin')">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" width="18" height="18">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.666 3.888A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 01-.75.75H9a.75.75 0 01-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184" />
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="mb-0" id="employee_email">manager@salon.com</p>
                        <p id="employee_password">12345678</p>
                    </div>
                    <div>
                        <a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-title="Click To Copy"
                            onclick="setLoginCredentials('employee')">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" width="18" height="18">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.666 3.888A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 01-.75.75H9a.75.75 0 01-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <x-slot name="extra">
            @if (Route::has('register'))
                <p class="text-center text-gray-600 mt-4">
                    Do not have an account? <a href="{{ route('register') }}"
                        class="underline hover:text-gray-900">Register</a>.
                </p>
            @endif
        </x-slot>
    </x-auth-card>

    <script type="text/javascript">
    function domId (name) {
      return document.getElementById(name)
    }
    function setLoginCredentials(type) {
      domId('email').value = domId(type+'_email').textContent
      domId('password').value = domId(type+'_password').textContent
    }
      // Custom email validation logic
      document.getElementById('email').addEventListener('input', function() {
            var emailField = document.getElementById('email');
            var emailError = document.getElementById('emailError');

            // Email validation pattern
            var emailPattern = /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/i;

            if (emailField.value && !emailPattern.test(emailField.value)) {
                emailError.style.display = 'block'; // Show error message
                emailField.classList.add('is-invalid'); // Add error styling if needed
            } else {
                emailError.style.display = 'none'; // Hide error message
                emailField.classList.remove('is-invalid'); // Remove error styling if fixed
            }
        });
        // Custom password validation logic for length
        document.getElementById('password').addEventListener('input', function() {
            var passwordField = document.getElementById('password');
            var passwordError = document.getElementById('passwordError');

            if (passwordField.value.length < 8 || passwordField.value.length > 12) {
                passwordError.style.display = 'block'; // Show error message
                passwordField.classList.add('is-invalid'); // Add error styling
            } else {
                passwordError.style.display = 'none'; // Hide error message
                passwordField.classList.remove('is-invalid'); // Remove error styling
            }
        });
  </script>
</x-auth-layout>
