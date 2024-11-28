<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Online Shop')</title>
    @vite('resources/css/app.css') <!-- Ensure Tailwind is included -->
</head>

<body class="bg-gray-100 text-gray-900">
    <div class="min-h-screen flex flex-col">
        <!-- Navigation -->
        <nav class="bg-blue-600 text-white py-4">
            <div class="container mx-auto flex justify-between items-center px-6">
                <a href="{{ route('home') }}" class="text-lg font-bold">Online Shop</a>
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('cart.index') }}" class="hover:underline">Cart</a>
                        <a href="{{ route('transactions.index') }}" class="hover:underline">Transactions</a>
                        <a href="{{ route('logout') }}" class="hover:underline"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="hover:underline">Login</a>
                        <a href="{{ route('register') }}" class="hover:underline">Register</a>
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-grow container mx-auto p-6">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white py-4">
            <div class="container mx-auto text-center">
                © 2024 Online Shop. All rights reserved.
            </div>
        </footer>
    </div>
</body>

</html>
