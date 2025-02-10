@extends('layouts.player')

@section('title', 'Mis Recargas')

@section('content')
    <h2 class="text-2xl font-bold text-gray-700 mb-4">💰 Mis Recargas</h2>

    @if($recharges->isEmpty())
        <p class="text-gray-600">No tienes recargas registradas.</p>
    @else
        <table class="w-full bg-white shadow-md rounded-lg overflow-hidden">
            <thead class="bg-green-500 text-white">
                <tr>
                    <th class="p-3">Fecha</th>
                    <th class="p-3">Monto</th>
                    <th class="p-3">Banco</th>
                    <th class="p-3">Referencia</th>
                    <th class="p-3">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recharges as $recharge)
                    <tr class="border-b">
                        <td class="p-3">{{ $recharge->created_at->format('d/m/Y') }}</td>
                        <td class="p-3">{{ $recharge->amount }} Bs</td>
                        <td class="p-3">{{ $recharge->bank }}</td>
                        <td class="p-3">{{ $recharge->reference_number }}</td>
                        <td class="p-3">
                            @if($recharge->status == 'Pendiente')
                                <span class="text-yellow-500">⏳ En revisión</span>
                            @elseif($recharge->status == 'Aprobado')
                                <span class="text-green-500">✅ Aprobado</span>
                            @else
                                <span class="text-red-500">❌ Rechazado</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
