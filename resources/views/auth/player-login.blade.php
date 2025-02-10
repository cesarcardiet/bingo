@extends('layouts.auth')

@section('content')
<div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold text-center text-purple-500 mb-6">🎲 Login de Jugador</h2>

    @if(session('status'))
        <div class="bg-green-100 text-green-700 p-3 rounded-md mb-4">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded-md mb-4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('player.login') }}">
        @csrf
        <div>
            <label class="block text-gray-700 font-semibold">Correo:</label>
            <input type="email" name="email" class="w-full px-3 py-2 border rounded-md focus:ring focus:ring-purple-300" required>
        </div>

        <div class="mt-3">
            <label class="block text-gray-700 font-semibold">Contraseña:</label>
            <input type="password" name="password" class="w-full px-3 py-2 border rounded-md focus:ring focus:ring-purple-300" required>
        </div>

        <button type="submit" class="w-full bg-purple-500 text-white py-2 rounded-md hover:bg-purple-600 transition mt-4">
            🚀 Iniciar Sesión
        </button>
    </form>
</div>
@endsection
