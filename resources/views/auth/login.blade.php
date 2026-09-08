<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Haniballi</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-teal-50 to-emerald-50 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center space-x-2">
                <div class="w-10 h-10 bg-teal-600 rounded-xl flex items-center justify-center">
                    <span class="text-white text-2xl">🌿</span>
                </div>
                <span class="font-bold text-2xl text-gray-900">Haniballi</span>
            </a>
            <p class="text-gray-500 mt-2 text-sm">Patient Portal</p>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-1">Welcome back</h2>
            <p class="text-gray-500 text-sm mb-6">Sign in to access your health dashboard</p>

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 mb-4 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 focus:border-transparent transition"
                        placeholder="patient@example.com">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" id="password" name="password" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 focus:border-transparent transition"
                        placeholder="••••••••">
                </div>
                <div class="flex items-center justify-between">
                    <label class="flex items-center space-x-2 text-sm text-gray-600">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                        <span>Remember me</span>
                    </label>
                </div>
                <button type="submit"
                    class="w-full bg-teal-600 hover:bg-teal-700 text-white py-3 rounded-lg font-semibold text-sm transition shadow-sm hover:shadow">
                    Sign In
                </button>
            </form>

            {{-- Demo Credentials Quick-Fill for Client Review --}}
            <div class="mt-6 pt-6 border-t border-gray-100">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 text-center">Mockup Demo Access</p>
                <div class="space-y-2">
                    <button type="button" onclick="fillCredentials('doctor@haniballi.com', 'password')"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl border border-teal-200 bg-teal-50 hover:bg-teal-100 text-teal-800 text-xs font-semibold transition text-left">
                        <div class="flex items-center space-x-2">
                            <span>👨‍⚕️</span>
                            <span><strong>Dr. Mehdi Haniballi</strong> (Doctor / Admin)</span>
                        </div>
                        <span class="text-teal-600 underline">Autofill</span>
                    </button>
                    <button type="button" onclick="fillCredentials('patient1@test.com', 'password')"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl border border-gray-200 bg-gray-50 hover:bg-gray-100 text-gray-700 text-xs font-semibold transition text-left">
                        <div class="flex items-center space-x-2">
                            <span>👩</span>
                            <span><strong>Sarah Martinez</strong> (Patient Portal)</span>
                        </div>
                        <span class="text-gray-500 underline">Autofill</span>
                    </button>
                </div>
            </div>
        </div>

        <p class="text-center text-gray-500 text-sm mt-6">
            <a href="{{ route('home') }}" class="text-teal-600 hover:underline">← Back to homepage</a>
        </p>
    </div>

    <script>
        function fillCredentials(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }
    </script>
</body>
</html>
