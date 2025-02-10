@extends('layouts.auth')

@section('title', 'Registro de Jugador')

@section('content')
    <div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-center text-green-600 mb-8">📝 Registro de Jugador</h2>

        <form action="{{ route('player.register') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="referral_id" value="{{ $referralCode }}">

            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-semibold mb-2">Nombre:</label>
                <input type="text" name="name" id="name" class="w-full px-4 py-3 border rounded-md focus:ring focus:ring-green-300" required>
            </div>

            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-semibold mb-2">Correo:</label>
                <input type="email" name="email" id="email" class="w-full px-4 py-3 border rounded-md focus:ring focus:ring-green-300" required>
            </div>

            <div class="mb-4">
                <label for="password" class="block text-gray-700 font-semibold mb-2">Contraseña:</label>
                <input type="password" name="password" id="password" class="w-full px-4 py-3 border rounded-md focus:ring focus:ring-green-300" required>
            </div>

            <div class="mb-6">
                <label for="password_confirmation" class="block text-gray-700 font-semibold mb-2">Confirmar Contraseña:</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="w-full px-4 py-3 border rounded-md focus:ring focus:ring-green-300" required>
            </div>

            <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-md hover:bg-green-700 transition font-semibold">
                Registrarse
            </button>
        </form>
    </div>
@endsection