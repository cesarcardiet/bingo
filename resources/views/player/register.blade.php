@extends('layouts.agent')

@section('title', 'Registro de Jugador')

@section('content')
    <div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-center text-purple-500 mb-6">Registro de Jugador</h2>

        <form action="{{ route('player.register') }}" method="POST" class="space-y-4">
            @csrf   

            <div>
                <label class="block text-gray-700 font-semibold">Nombre Completo:</label>
                <input type="text" name="name" class="w-full px-3 py-2 border rounded-md focus:ring focus:ring-purple-300" required>
            </div>

            <div>
                <label class="block text-gray-700 font-semibold">Correo Electrónico:</label>
                <input type="email" name="email" class="w-full px-3 py-2 border rounded-md focus:ring focus:ring-purple-300" required>
            </div>

            <div>
                <label class="block text-gray-700 font-semibold">Contraseña:</label>
                <input type="password" name="password" class="w-full px-3 py-2 border rounded-md focus:ring focus:ring-purple-300" required>
            </div>

            <div>
                <label class="block text-gray-700 font-semibold">Confirmar Contraseña:</label>
                <input type="password" name="password_confirmation" class="w-full px-3 py-2 border rounded-md focus:ring focus:ring-purple-300" required>
            </div>

            <div>
                <label class="block text-gray-700 font-semibold">ID de Referido:</label>
                <input type="text" name="referral_id" value="{{ request()->query('ref') }}" class="w-full px-3 py-2 border rounded-md bg-gray-200" readonly required>
            </div>

            <button type="submit" class="w-full bg-purple-500 text-white py-2 rounded-md hover:bg-purple-600 transition">
                Registrarse
            </button>
        </form>
    </div>
@endsection
