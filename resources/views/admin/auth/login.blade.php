<!DOCTYPE html>
<html lang="en" class="h-full bg-[#08080E]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Login — ENGORA</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-['Outfit',sans-serif] text-slate-100 antialiased flex items-center justify-center relative overflow-hidden bg-[#08080E]">

    <!-- Ambient Cinema Backdrops & Glowing Orbs -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-[#E50914]/20 rounded-full blur-[140px]"></div>
        <div class="absolute top-1/2 -right-40 w-96 h-96 bg-fuchsia-600/15 rounded-full blur-[160px]"></div>
        <div class="absolute -bottom-40 left-1/3 w-[500px] h-96 bg-violet-600/15 rounded-full blur-[150px]"></div>
        <div class="absolute inset-0 bg-[radial-gradient(#ffffff08_1px,transparent_1px)] [background-size:24px_24px]"></div>
    </div>

    <!-- Main Container -->
    <div class="relative z-10 w-full max-w-md px-6 py-10">
        
        <!-- Brand Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-[#E50914] via-[#B81D24] to-[#7A0E13] p-0.5 shadow-2xl shadow-[#E50914]/30 mb-4 border border-white/20">
                <div class="w-full h-full bg-[#0D0D14] rounded-[14px] flex items-center justify-center">
                    <span class="text-2xl font-black text-[#E50914] tracking-tight">E</span>
                </div>
            </div>
            <h1 class="text-3xl font-black tracking-tight text-white flex items-center justify-center gap-2">
                ENGORA
                <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 bg-[#E50914]/20 text-[#FF2E3D] rounded-full border border-[#E50914]/40">Admin</span>
            </h1>
            <p class="text-slate-400 text-sm mt-2 font-medium">Encrypted Authentication Gateway</p>
        </div>

        <!-- Glass Login Card -->
        <div class="bg-[#12121E]/80 backdrop-blur-2xl border border-white/10 rounded-3xl p-8 shadow-2xl shadow-black/80 space-y-6">
            
            <!-- Success Message -->
            @if(session('success'))
                <div class="bg-emerald-500/10 border border-emerald-500/30 rounded-2xl p-4 flex items-center gap-3 text-emerald-400 text-sm">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- General Error / Alert Message -->
            @if(session('error'))
                <div class="bg-rose-500/10 border border-rose-500/30 rounded-2xl p-4 flex items-center gap-3 text-rose-400 text-sm">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->has('login'))
                <div class="bg-rose-500/10 border border-rose-500/30 rounded-2xl p-4 flex items-center gap-3 text-rose-400 text-sm">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ $errors->first('login') }}</span>
                </div>
            @endif

            <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-5">
                @csrf

                <!-- Username or Email Field -->
                <div class="space-y-2">
                    <label for="login" class="block text-xs font-bold uppercase tracking-wider text-slate-300">
                        Username or Email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <input type="text" 
                               id="login" 
                               name="login" 
                               value="{{ old('login') }}" 
                               required 
                               autofocus
                               placeholder="admin" 
                               class="w-full bg-[#181828]/80 border border-white/10 rounded-2xl pl-12 pr-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:border-[#E50914] focus:ring-2 focus:ring-[#E50914]/20 transition text-sm">
                    </div>
                </div>

                <!-- Password Field -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-300">
                            Password
                        </label>
                        <span class="text-[11px] font-semibold text-emerald-400 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                            </svg>
                            Encrypted
                        </span>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               required 
                               placeholder="••••••••••••" 
                               class="w-full bg-[#181828]/80 border border-white/10 rounded-2xl pl-12 pr-12 py-3 text-white placeholder-slate-500 focus:outline-none focus:border-[#E50914] focus:ring-2 focus:ring-[#E50914]/20 transition text-sm">
                        <button type="button" 
                                onclick="togglePasswordVisibility()" 
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-white transition">
                            <svg id="eye-icon" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me & Security Status -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" 
                               name="remember" 
                               value="1" 
                               class="w-4 h-4 rounded bg-[#181828] border-white/20 text-[#E50914] focus:ring-0 focus:ring-offset-0">
                        <span class="text-xs text-slate-300 font-medium">Remember session</span>
                    </label>
                    <span class="text-[11px] text-slate-400 font-medium">SHA-256 / Bcrypt</span>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-[#E50914] via-[#B81D24] to-[#991218] hover:from-[#FF2E3D] hover:to-[#B81D24] text-white font-extrabold py-3.5 px-6 rounded-2xl transition duration-200 shadow-xl shadow-[#E50914]/25 flex items-center justify-center gap-2 text-sm">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    <span>Secure Sign In</span>
                </button>
            </form>

            <!-- Bottom Footnote -->
            <div class="pt-2 border-t border-white/5 text-center">
                <p class="text-[11px] text-slate-400">
                    Protected by Rate Limiting &amp; Encrypted Session Security.
                </p>
            </div>
        </div>

        <!-- Return to Public App -->
        <div class="text-center mt-6">
            <a href="{{ route('home') }}" class="text-xs font-semibold text-slate-400 hover:text-white transition flex items-center justify-center gap-1">
                ← Back to ENGORA Public App
            </a>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                `;
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                `;
            }
        }
    </script>
</body>
</html>
