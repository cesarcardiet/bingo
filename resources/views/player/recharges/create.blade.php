@extends('layouts.player')

@section('title', 'Solicitar Recarga')

@section('content')
<div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold text-center text-green-500 mb-6">💰 Solicitar Recarga</h2>

    <form action="{{ route('player.recharges.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div>
            <label class="block text-gray-700 font-semibold">Monto a Recargar (Bs):</label>
            <input type="number" name="amount" step="0.01" class="w-full px-3 py-2 border rounded-md focus:ring focus:ring-green-300" required>
        </div>

        <div>
            <label class="block text-gray-700 font-semibold">Número de Referencia:</label>
            <input type="text" name="reference_number" class="w-full px-3 py-2 border rounded-md focus:ring focus:ring-green-300" required>
        </div>

        <div>
            <label class="block text-gray-700 font-semibold">Captura de Pantalla del Pago:</label>
            <input type="file" name="receipt_image" class="w-full px-3 py-2 border rounded-md focus:ring focus:ring-green-300" accept="image/*" required>
        </div>

        <button type="submit" class="w-full bg-green-500 text-white py-2 rounded-md mt-4 hover:bg-green-600 transition">
            ✅ Enviar Solicitud
        </button>
    </form>

    <a href="{{ route('player.dashboard') }}" class="block text-center text-gray-500 mt-4 hover:underline">⬅️ Volver al Panel</a>
</div>
@endsection
