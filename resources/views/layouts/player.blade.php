<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Mi Bingo')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex h-screen">

    <!-- Menú lateral (PC) -->
    <aside class="w-64 bg-gradient-to-b from-green-400 to-purple-500 text-white p-5 hidden md:block">
        <h2 class="text-2xl font-bold text-center">🎲 Mi Bingo</h2>
        <ul class="mt-5 space-y-4">
            <li class="hover:bg-white hover:text-green-500 px-3 py-2 rounded-md transition">
                <a href="{{ route('player.dashboard') }}">📊 Dashboard</a>
            </li>
            <li class="hover:bg-white hover:text-green-500 px-3 py-2 rounded-md transition">
                <a href="{{ route('player.recharges.create') }}">💳 Recargar Saldo</a>
            </li>
            <a href="{{ url('/player/my-cards?room_id=' . ($room->id ?? 0)) }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-200">
    🎟️ Mis Cartones
</a>

            <li class="hover:bg-white hover:text-green-500 px-3 py-2 rounded-md transition">
                <a href="#">🏆 Historial de Juegos</a>
            </li>
            <li class="hover:bg-white hover:text-green-500 px-3 py-2 rounded-md transition">
                <a href="{{ route('player.recharges.index') }}">💳 Ver Mis Recargas</a>
            </li>
            <li class="hover:bg-red-500 px-3 py-2 rounded-md transition">
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">🚪 Cerrar Sesión</a>
            </li>
        </ul>
    </aside>

    <!-- Contenedor principal -->
    <div class="flex-1 flex flex-col">
        
        <!-- Encabezado superior -->
        <nav class="bg-white shadow-md p-4 flex justify-between items-center">
            <p>Bienvenido, {{ auth()->user()->name ?? 'Jugador' }}</p>
            <button onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
                    class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 transition">
                🚪 Cerrar Sesión
            </button>
        </nav>

        <!-- Menú hamburguesa para móvil -->
        <nav class="md:hidden bg-green-500 text-white p-4 flex justify-between items-center">
            <h2 class="text-xl font-bold">🎲 Mi Bingo</h2>
            <button id="menu-toggle" class="focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" 
                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7"></path>
                </svg>
            </button>
        </nav>

        <div id="mobile-menu" class="md:hidden bg-white shadow-md absolute top-16 left-0 w-full hidden">
            <ul class="space-y-4 p-4">
                <li class="hover:text-green-500">
                    <a href="{{ route('player.dashboard') }}">📊 Dashboard</a>
                </li>
                <li class="hover:text-green-500">
                    <a href="{{ route('player.recharges.create') }}">💳 Recargar Saldo</a>
                </li>
                <li class="hover:text-green-500">
                    <a href="{{ route('player.my-cards', ['room_id' => session('room_id') ?? 1]) }}">🎟️ Mis Cartones</a>
                </li>
                <li class="hover:text-green-500">
                    <a href="#">🏆 Historial de Juegos</a>
                </li>
                <li class="hover:text-green-500">
                    <a href="{{ route('player.recharges.index') }}">💳 Ver Mis Recargas</a>
                </li>
                <li class="hover:text-red-500">
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">🚪 Cerrar Sesión</a>
                </li>
            </ul>
        </div>

        <!-- Contenido principal -->
        <main class="p-6">
            @yield('content')
        </main>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>

    <script>
        document.getElementById('menu-toggle').addEventListener('click', function () {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
    </script>

</body>
</html>
