<div
    x-data="{ 
        isOpen: {{ $errors->any() ? 'true' : 'false' }}, 
        tab: '{{ old('_form', 'login') }}',
        open(selectedTab) {
            this.tab = selectedTab;
            this.isOpen = true;
        },
        close() {
            this.isOpen = false;
            this.tab = 'login'; // Reset ke tab login saat ditutup
        }
    }"
    x-on:open-login.window="open('login')"
    x-on:open-register.window="open('register')"
    x-on:keydown.escape.window="close()"
    x-show="isOpen"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    x-cloak
>
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-[#18213a]/50 backdrop-blur-sm"
         @click="close()"
         x-transition:enter="transition-opacity duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
    </div>

    {{-- Dialog --}}
    <div class="relative w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl"
         @click.stop
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-2">

        {{-- Header --}}
        <div class="flex items-center justify-between border-b border-[#e5e9f2] px-6 py-4">
            <div class="flex items-center gap-2.5">
                <div class="grid h-8 w-8 place-items-center rounded-lg bg-[#3b6fd4] text-white">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/>
                    </svg>
                </div>
                <span class="text-sm font-bold text-[#18213a]">DrivEase</span>
            </div>
            <button @click="close()"
                    class="grid h-8 w-8 place-items-center rounded-lg text-[#7a8499]
                           hover:bg-[#f1f4fa] transition-colors">
                <x-icon name="x" class="w-4 h-4" />
            </button>
        </div>

        {{-- Tab Switcher --}}
        <div class="flex border-b border-[#e5e9f2] px-6">
            <button @click="tab = 'login'"
                    :class="tab === 'login'
                        ? 'border-b-2 border-[#3b6fd4] text-[#3b6fd4] font-semibold'
                        : 'text-[#7a8499] hover:text-[#18213a]'"
                    class="mr-6 py-3 text-sm transition-colors">
                {{ __('Masuk') }}
            </button>
            <button @click="tab = 'register'"
                    :class="tab === 'register'
                        ? 'border-b-2 border-[#3b6fd4] text-[#3b6fd4] font-semibold'
                        : 'text-[#7a8499] hover:text-[#18213a]'"
                    class="py-3 text-sm transition-colors">
                {{ __('Daftar') }}
            </button>
        </div>

        {{-- ══ TAB LOGIN ══════════════════════════════════════ --}}
        <div x-show="tab === 'login'" class="px-6 py-5">

            {{-- Session Error --}}
            @if($errors->any() && old('_form') === 'login')
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2.5 text-xs text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            @if(session('status'))
                <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-3 py-2.5 text-xs text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-3">
                @csrf
                <input type="hidden" name="_form" value="login">

                <div class="space-y-1">
                    <label class="block text-xs font-medium text-[#18213a]">{{ __('Email') }}</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           placeholder="{{ __('nama@email.com') }}"
                           autocomplete="email"
                           required
                           class="h-10 w-full rounded-xl border border-[#e5e9f2] bg-[#f4f6fb] px-3
                                  text-sm outline-none placeholder:text-[#aab0bf]
                                  focus:border-[#3b6fd4] focus:ring-2 focus:ring-[#3b6fd4]/20
                                  transition-colors
                                  @error('email') border-red-300 bg-red-50 @enderror">
                </div>

                <div class="space-y-1">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-medium text-[#18213a]">{{ __('Kata Sandi') }}</label>
                        <a href="{{ route('password.request') }}"
                           class="text-xs text-[#3b6fd4] hover:underline">
                            {{ __('Lupa kata sandi?') }}
                        </a>
                    </div>
                    <div class="relative" x-data="{ show: false }">
                        <input :type="show ? 'text' : 'password'"
                               name="password"
                               placeholder="••••••••"
                               autocomplete="current-password"
                               required
                               class="h-10 w-full rounded-xl border border-[#e5e9f2] bg-[#f4f6fb]
                                      px-3 pr-10 text-sm outline-none placeholder:text-[#aab0bf]
                                      focus:border-[#3b6fd4] focus:ring-2 focus:ring-[#3b6fd4]/20
                                      transition-colors">
                        <button type="button" @click="show = !show"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-[#7a8499]
                                       hover:text-[#18213a] transition-colors">
                            <template x-if="!show">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                </svg>
                            </template>
                            <template x-if="show">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/>
                                </svg>
                            </template>
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="remember" id="remember"
                           class="h-4 w-4 rounded border-[#e5e9f2] text-[#3b6fd4] focus:ring-[#3b6fd4]">
                    <label for="remember" class="text-xs text-[#7a8499]">{{ __('Ingat saya') }}</label>
                </div>

                <button type="submit"
                        class="flex h-10 w-full items-center justify-center rounded-xl bg-[#3b6fd4]
                               text-sm font-semibold text-white hover:bg-[#2e5bb8] transition-colors">
                    {{ __('Masuk') }}
                </button>
            </form>

            {{-- Divider --}}
            <div class="relative my-4">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-[#e5e9f2]"></div>
                </div>
                <div class="relative flex justify-center">
                    <span class="bg-white px-3 text-xs text-[#7a8499]">{{ __('atau masuk dengan') }}</span>
                </div>
            </div>

            {{-- Google OAuth --}}
            <a href="{{ route('auth.google') }}"
               class="flex h-10 w-full items-center justify-center gap-2.5 rounded-xl border
                      border-[#e5e9f2] bg-white text-sm font-medium text-[#18213a]
                      hover:bg-[#f4f6fb] transition-colors">
                <svg class="h-4 w-4" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                {{ __('Lanjutkan dengan Google') }}
            </a>

            {{-- Footer Legal --}}
            <p class="mt-4 text-center text-[10px] leading-relaxed text-[#7a8499]">
                {{ __('Dengan melanjutkan, kamu menyetujui') }}
                <a href="{{ route('terms') }}"
                   target="_blank" rel="noopener"
                   class="text-[#3b6fd4] underline underline-offset-2 hover:text-[#2e5bb8]">
                    {{ __('Syarat & Ketentuan') }}
                </a>
                {{ __('ini dan kamu sudah diberitahu mengenai') }}
                <a href="{{ route('privacy') }}"
                   target="_blank" rel="noopener"
                   class="text-[#3b6fd4] underline underline-offset-2 hover:text-[#2e5bb8]">
                    {{ __('Pemberitahuan Privasi') }}
                </a>
                {{ __('kami.') }}
            </p>
        </div>

        {{-- ══ TAB REGISTER (2-step wizard) ═══════════════════ --}}
        @php
            $registerStep1Fields = ['name', 'email', 'no_hp'];
            $registerStep2Fields = ['password', 'password_confirmation'];
            $registerInitialStep = $errors->hasAny($registerStep1Fields)
                ? 1
                : ($errors->hasAny($registerStep2Fields) ? 2 : 1);
        @endphp
        <div x-show="tab === 'register'" class="px-6 py-5">

            @if($errors->any() && old('_form') === 'register')
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2.5 text-xs text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-3"
                  x-data="{ step: {{ $registerInitialStep }} }">
                @csrf
                <input type="hidden" name="_form" value="register">

                {{-- Step indicator --}}
                <div class="mb-1 flex items-center gap-1.5 text-[11px] font-medium text-[#7a8499]">
                    <span :class="step === 1 ? 'text-[#3b6fd4]' : ''">{{ __('1. Data Diri') }}</span>
                    <span class="text-[#e5e9f2]">&rarr;</span>
                    <span :class="step === 2 ? 'text-[#3b6fd4]' : ''">{{ __('2. Kata Sandi') }}</span>
                </div>

                {{-- ── Langkah 1: Data Diri ── --}}
                <div x-show="step === 1" x-ref="registerStep1" class="space-y-3"
                     x-on:keydown.enter.prevent="if ([...$refs.registerStep1.querySelectorAll('input')].every(el => el.reportValidity())) step = 2">

                    <div class="space-y-1">
                        <label class="block text-xs font-medium text-[#18213a]">{{ __('Nama Lengkap') }}</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               placeholder="{{ __('Nama kamu') }}"
                               autocomplete="name"
                               required
                               class="h-10 w-full rounded-xl border border-[#e5e9f2] bg-[#f4f6fb] px-3
                                      text-sm outline-none placeholder:text-[#aab0bf]
                                      focus:border-[#3b6fd4] focus:ring-2 focus:ring-[#3b6fd4]/20
                                      transition-colors
                                      @error('name') border-red-300 bg-red-50 @enderror">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-medium text-[#18213a]">{{ __('Email') }}</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               placeholder="{{ __('nama@email.com') }}"
                               autocomplete="email"
                               required
                               class="h-10 w-full rounded-xl border border-[#e5e9f2] bg-[#f4f6fb] px-3
                                      text-sm outline-none placeholder:text-[#aab0bf]
                                      focus:border-[#3b6fd4] focus:ring-2 focus:ring-[#3b6fd4]/20
                                      transition-colors
                                      @error('email') border-red-300 bg-red-50 @enderror">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-medium text-[#18213a]">{{ __('Nomor HP') }}</label>
                        <div class="flex rounded-xl border border-[#e5e9f2] bg-[#f4f6fb] overflow-hidden
                                    focus-within:border-[#3b6fd4] focus-within:ring-2 focus-within:ring-[#3b6fd4]/20
                                    transition-colors">
                            <span class="flex items-center border-r border-[#e5e9f2] bg-white px-3 text-xs
                                         font-medium text-[#7a8499]">+62</span>
                            <input type="tel" name="no_hp" value="{{ old('no_hp') }}"
                                   placeholder="81234567890"
                                   autocomplete="tel"
                                   class="h-10 flex-1 bg-transparent px-3 text-sm outline-none
                                          placeholder:text-[#aab0bf]">
                        </div>
                    </div>

                    <button type="button"
                            @click="if ([...$refs.registerStep1.querySelectorAll('input')].every(el => el.reportValidity())) step = 2"
                            class="flex h-10 w-full items-center justify-center rounded-xl bg-[#3b6fd4]
                                   text-sm font-semibold text-white hover:bg-[#2e5bb8] transition-colors">
                        {{ __('Lanjut') }}
                    </button>

                    {{-- Divider --}}
                    <div class="relative my-4">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-[#e5e9f2]"></div>
                        </div>
                        <div class="relative flex justify-center">
                            <span class="bg-white px-3 text-xs text-[#7a8499]">{{ __('atau daftar dengan') }}</span>
                        </div>
                    </div>

                    {{-- Google OAuth --}}
                    <a href="{{ route('auth.google') }}"
                       class="flex h-10 w-full items-center justify-center gap-2.5 rounded-xl border
                              border-[#e5e9f2] bg-white text-sm font-medium text-[#18213a]
                              hover:bg-[#f4f6fb] transition-colors">
                        <svg class="h-4 w-4" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        {{ __('Lanjutkan dengan Google') }}
                    </a>
                </div>

                {{-- ── Langkah 2: Kata Sandi ── --}}
                <div x-show="step === 2" class="space-y-3">

                    <div class="space-y-1">
                        <label class="block text-xs font-medium text-[#18213a]">{{ __('Kata Sandi') }}</label>
                        <div class="relative" x-data="{ show: false }">
                            <input :type="show ? 'text' : 'password'"
                                   name="password"
                                   placeholder="{{ __('Min. 8 karakter') }}"
                                   autocomplete="new-password"
                                   required
                                   class="h-10 w-full rounded-xl border border-[#e5e9f2] bg-[#f4f6fb]
                                          px-3 pr-10 text-sm outline-none placeholder:text-[#aab0bf]
                                          focus:border-[#3b6fd4] focus:ring-2 focus:ring-[#3b6fd4]/20
                                          transition-colors
                                          @error('password') border-red-300 bg-red-50 @enderror">
                            <button type="button" @click="show = !show"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-[#7a8499]
                                           hover:text-[#18213a] transition-colors">
                                <template x-if="!show">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                                </template>
                                <template x-if="show">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                                </template>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-1">
    <label class="block text-xs font-medium text-[#18213a]">{{ __('Konfirmasi Kata Sandi') }}</label>
    <div class="relative" x-data="{ show: false }">
        <input :type="show ? 'text' : 'password'"
               name="password_confirmation"
               placeholder="{{ __('Ulangi kata sandi') }}"
               autocomplete="new-password"
               required
               class="h-10 w-full rounded-xl border border-[#e5e9f2] bg-[#f4f6fb]
                      px-3 pr-10 text-sm outline-none placeholder:text-[#aab0bf]
                      focus:border-[#3b6fd4] focus:ring-2 focus:ring-[#3b6fd4]/20
                      transition-colors">
        <button type="button" @click="show = !show"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-[#7a8499]
                       hover:text-[#18213a] transition-colors">
            <template x-if="!show">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
            </template>
            <template x-if="show">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
            </template>
        </button>
    </div>
</div>

                    <div class="flex gap-2">
                        <button type="button" @click="step = 1"
                                class="flex h-10 w-24 flex-shrink-0 items-center justify-center rounded-xl border
                                       border-[#e5e9f2] text-sm font-medium text-[#18213a]
                                       hover:bg-[#f1f4fa] transition-colors">
                            {{ __('Kembali') }}
                        </button>
                        <button type="submit"
                                class="flex h-10 flex-1 items-center justify-center rounded-xl bg-[#3b6fd4]
                                       text-sm font-semibold text-white hover:bg-[#2e5bb8] transition-colors">
                            {{ __('Buat Akun') }}
                        </button>
                    </div>

                    {{-- Footer Legal --}}
                    <p class="mt-2 text-center text-[10px] leading-relaxed text-[#7a8499]">
                        {{ __('Dengan melanjutkan, kamu menyetujui') }}
                        <a href="{{ route('terms') }}"
                           target="_blank" rel="noopener"
                           class="text-[#3b6fd4] underline underline-offset-2 hover:text-[#2e5bb8]">
                            {{ __('Syarat & Ketentuan') }}
                        </a>
                        {{ __('ini dan kamu sudah diberitahu mengenai') }}
                        <a href="{{ route('privacy') }}"
                           target="_blank" rel="noopener"
                           class="text-[#3b6fd4] underline underline-offset-2 hover:text-[#2e5bb8]">
                            {{ __('Pemberitahuan Privasi') }}
                        </a>
                        {{ __('kami.') }}
                    </p>
                </div>
            </form>
        </div>

    </div>
</div>