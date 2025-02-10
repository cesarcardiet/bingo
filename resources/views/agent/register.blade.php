@extends('layouts.agent')

@section('title', 'Registro de Agente')

@section('content')
    <div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-md mt-6">
        <h2 class="text-2xl font-bold text-center text-green-500 mb-6">Registro de Agente</h2>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                <strong class="font-bold">¡Registro exitoso!</strong>
                <p>Tu link de referido: 
                    <span class="text-blue-500 font-semibold">
                        {{ url('/register/player?ref=' . session('referral_id')) }}
                    </span>
                </p>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <strong class="font-bold">Errores en el formulario:</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('agent.register') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-gray-700">Nombre Completo</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Ej: Juan Pérez"
                    class="w-full border border-gray-300 p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" required>
            </div>

            <div>
                <label class="block text-gray-700">Cédula de Identidad</label>
                <input type="text" name="id_number" value="{{ old('id_number') }}" placeholder="Ej: 12345678"
                    class="w-full border border-gray-300 p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" required>
            </div>

            <div>
                <label class="block text-gray-700">Correo Electrónico</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="Ej: agente@gmail.com"
                    class="w-full border border-gray-300 p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" required>
            </div>

            <div>
                <label class="block text-gray-700">Contraseña</label>
                <input type="password" name="password" placeholder="Mínimo 6 caracteres"
                    class="w-full border border-gray-300 p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" required>
            </div>

            <div>
                <label class="block text-gray-700">Confirmar Contraseña</label>
                <input type="password" name="password_confirmation" placeholder="Repite la contraseña"
                    class="w-full border border-gray-300 p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" required>
            </div>

            <div>
                <label class="block text-gray-700">Teléfono</label>
                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Ej: 04141234567"
                    class="w-full border border-gray-300 p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" required>
            </div>

            <div>
                <label class="block text-gray-700">Banco</label>
                <select name="bank_name" class="w-full border border-gray-300 p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" required>
                    <option value="">Selecciona un banco</option>
                    <option value="Banco de Venezuela">Banco de Venezuela</option>
                    <option value="Banesco">Banesco</option>
                    <option value="Banco Mercantil">Banco Mercantil</option>
                </select>
            </div>

            <button type="submit"
                class="w-full bg-green-500 text-white p-3 rounded-md hover:bg-green-600 transition">
                Registrar Agente
            </button>
        </form> 
    </div>
@endsection
