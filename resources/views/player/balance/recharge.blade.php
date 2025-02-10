@extends('layouts.player')

@section('title', 'Mis Recargas')

@section('content')
    <div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-center text-green-500 mb-6">💳 Historial de Recargas</h2>

        <p class="text-gray-600 text-center mb-4">
            📢 Las recargas pueden tardar en aprobarse según la disponibilidad del agente.<br>
            Si tu recarga aún está **pendiente**, por favor espera la confirmación.
        </p>

        <div class="overflow-x-auto">
            <table class="w-full bg-white shadow-md rounded-lg overflow-hidden">
                <thead class="bg-green-500 text-white">
                    <tr>
                        <th class="p-3">Fecha</th>
                        <th class="p-3">Monto (Bs)</th>
                        <th class="p-3">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recharges as $recharge)
                        <tr class="border-b">
                            <td class="p-3">{{ $recharge->created_at->format('d/m/Y H:i') }}</td>
                            <td class="p-3">{{ number_format($recharge->amount, 2) }} Bs</td>
                            <td class="p-3">
                                @if($recharge->status === 'Pendiente')
                                    <span class="bg-yellow-400 text-white px-2 py-1 rounded-md">⏳ Pendiente</span>
                                @elseif($recharge->status === 'Aprobado')
                                    <span class="bg-green-500 text-white px-2 py-1 rounded-md">✅ Aprobado</span>
                                @else
                                    <span class="bg-red-500 text-white px-2 py-1 rounded-md">❌ Rechazado</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="p-3 text-center text-gray-500">No has realizado recargas aún.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
