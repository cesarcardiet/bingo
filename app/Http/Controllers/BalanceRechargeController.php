<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BalanceRecharge;
use Illuminate\Support\Facades\Auth;

class BalanceRechargeController extends Controller
{
    // Mostrar formulario de recarga
    public function create()
    {
        return view('player.recharges.create');
    }

    // Procesar la solicitud de recarga
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'reference_number' => 'required|string|max:255',
            'receipt_image' => 'required|image|max:2048',
        ]);

        // Guardar la imagen de la captura de pantalla
        $receiptPath = $request->file('receipt_image')->store('receipts', 'public');

        // Crear la solicitud de recarga
        BalanceRecharge::create([
            'player_id' => Auth::id(),
            'amount' => $request->amount,
            'reference_number' => $request->reference_number,
            'receipt_image' => $receiptPath,
            'status' => 'Pendiente',
        ]);

        return redirect()->route('player.dashboard')->with('success', 'Tu solicitud de recarga ha sido enviada. Espera la aprobación del agente.');
    }

    public function showPendingRecharges()
    {
        $agent = Auth::guard('agent')->user();
    
        if (!$agent) {
            return redirect()->route('agent.login.form')->withErrors('Debes iniciar sesión.');
        }
    
        // 🔹 Obtener IDs de jugadores del agente
        $playersIds = \App\Models\Player::where('agent_id', $agent->id)->pluck('id');
    
        // 🔹 Obtener recargas de esos jugadores
        $recharges = BalanceRecharge::whereIn('player_id', $playersIds)
                                    ->where('status', 'Pendiente')
                                    ->get();
    
        return view('agent.recharges.index', compact('recharges'));
    }
    
    
    public function updateRechargeStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Aprobado,Rechazado',
        ]);

        $recharge = BalanceRecharge::findOrFail($id);

        // Verificar si el agente es el dueño del jugador
        if ($recharge->player->agent_id !== Auth::guard('agent')->id()) {
            return redirect()->route('agent.recharges.index')->withErrors('No tienes permiso para gestionar esta recarga.');
        }

        // Actualizar el estado de la recarga
        $recharge->update(['status' => $request->status]);

        // Si se aprueba, aumentar el saldo del jugador
        if ($request->status === 'Aprobado') {
            $player = $recharge->player;
            $player->increment('balance', $recharge->amount);
        }

        return redirect()->route('agent.recharges.index')->with('success', 'Recarga actualizada correctamente.');
    }
}
