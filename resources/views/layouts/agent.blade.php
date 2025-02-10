<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Mi Bingo')</title>
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex h-screen">

    <!-- Menú lateral (PC) -->
    <aside class="w-64 bg-gradient-to-b from-green-400 to-purple-500 text-white p-5 hidden md:block">
        <h2 class="text-2xl font-bold text-center">🎲 Mi Bingo</h2>
        <ul class="mt-5 space-y-4">
            <li class="hover:bg-white hover:text-green-500 px-3 py-2 rounded-md transition">
                <a href="{{ route('agent.dashboard') }}">📊 Panel de Agente</a>
            </li>
            <li class="hover:bg-white hover:text-green-500 px-3 py-2 rounded-md transition">
                <a href="{{ route('rooms.index') }}">🏠 Gestionar Salas</a>
            </li>
            <li class="hover:bg-white hover:text-green-500 px-3 py-2 rounded-md transition">
                <a href="{{ route('agent.raffles.index') }}">🎟️ Gestionar Sorteos</a>
            </li>
            <li class="hover:bg-white hover:text-green-500 px-3 py-2 rounded-md transition">
                <a href="#">💰 Ver Pagos</a>
            </li>
            <li class="hover:bg-white hover:text-green-500 px-3 py-2 rounded-md transition">
    <a href="{{ route('agent.recharges.index') }}">💳 Gestionar Recargas</a>
</li>

            <li class="hover:bg-white hover:text-green-500 px-3 py-2 rounded-md transition">
                <a href="#">📋 Historial de Ganadores</a>
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
            <div>
                @if(auth()->check())
                    <p class="font-semibold text-gray-700">Bienvenido, {{ auth()->user()->name }}</p>
                @else
                    <p class="font-semibold text-gray-700">Bienvenido, Invitado</p>
                @endif
            </div>
            
            <button onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
                    class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 transition">
                🚪 Cerrar Sesión
            </button>
        </nav>

        <!-- Mostrar el Link de Referido -->
        <div class="p-6">
            <h3 class="text-lg font-bold text-gray-700 mb-2">🔗 Tu Link de Referido</h3>
            <div class="bg-gray-100 p-4 rounded-md flex justify-between items-center">
                <input type="text" id="referral-link" 
                value="{{ url('/register/player?ref=' . (auth()->check() ? auth()->user()->id : '')) }}"

                       class="w-full px-3 py-2 bg-white border rounded-md" readonly>
                <button onclick="copyReferralLink()" class="ml-2 bg-blue-500 text-white px-3 py-2 rounded-md hover:bg-blue-600 transition">
                    📋 Copiar
                </button>
            </div>
        </div>

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
                <li class="hover:text-green-500"><a href="{{ route('agent.dashboard') }}">📊 Panel de Agente</a></li>
                <li class="hover:text-green-500"><a href="{{ route('rooms.index') }}">🏠 Gestionar Salas</a></li>
                <li class="hover:text-green-500"><a href="{{ route('agent.raffles.index') }}">🎟️ Gestionar Sorteos</a></li>
                <li class="hover:text-green-500"><a href="#">💰 Ver Pagos</a></li>
                <li class="hover:text-green-500"><a href="#">📋 Historial de Ganadores</a></li>
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

        function copyReferralLink() {
            var copyText = document.getElementById("referral-link");
            copyText.select();
            document.execCommand("copy");
            alert("¡Link de referido copiado!");
        }
    </script>

</body>
</html>
