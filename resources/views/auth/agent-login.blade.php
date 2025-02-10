@extends('layouts.auth')

@section('content')
<div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold text-center text-green-500 mb-6">🎟️ Login de Agente</h2>

    <form method="POST" action="{{ route('agent.login') }}">
        @csrf
        <div>
            <label class="block text-gray-700 font-semibold">Correo:</label>
            <input type="email" name="email" class="w-full px-3 py-2 border rounded-md focus:ring focus:ring-green-300" required>
        </div>

        <div class="mt-3">
            <label class="block text-gray-700 font-semibold">Contraseña:</label>
            <input type="password" name="password" class="w-full px-3 py-2 border rounded-md focus:ring focus:ring-green-300" required>
        </div>

        <button type="submit" class="w-full bg-green-500 text-white py-2 rounded-md hover:bg-green-600 transition mt-4">
            🚀 Iniciar Sesión
        </button>
    </form>
</div>
@endsection
