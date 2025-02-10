<?php

namespace App\Http\Controllers;



use Illuminate\Http\Request;
use App\Models\BalanceRecharge;
use App\Models\Player;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BalanceController extends Controller
{
    /**
     * Mostrar el formulario de recarga de saldo.
     */
    public function showRechargeForm()
    {
        return view('player.balance.recharge');
    }

    /**
     * Guardar la solicitud de recarga en la base de datos.
     */
    public function storeRecharge(Request $request)
    {
        $request->validate([
            'bank' => 'required|string|max:255',
            'reference_number' => 'required|string|max:50|unique:balance_recharges',
            'amount' => 'required|numeric|min:1',
            'receipt' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Subir la imagen de la captura de pantalla
        $receiptPath = $request->file('receipt')->store('receipts', 'public');

        // Guardar en la base de datos
        BalanceRecharge::create([
            'player_id' => Auth::id(),
            'bank' => $request->bank,
            'reference_number' => $request->reference_number,
            'amount' => $request->amount,
            'receipt' => $receiptPath,
            'status' => 'Pendiente', // Queda en estado pendiente hasta ser aprobado
        ]);

        return redirect()->route('player.dashboard')->with('success', 'Solicitud de recarga enviada. Espera la aprobación del agente.');
    }

    /**
     * Mostrar recargas pendientes para el agente.
     */
    public function showPendingRecharges()
    {
        $agent = Auth::guard('agent')->user();
    
        // Obtener los jugadores que pertenecen a este agente
        $players = Player::where('referral_id', $agent->referral_id)->pluck('id');
    
        // Obtener recargas pendientes de estos jugadores
        $recharges = BalanceRecharge::whereIn('player_id', $players)
                                    ->where('status', 'Pendiente')
                                    ->get();
    
        return view('agent.recharges.index', compact('recharges'));
    }
    
    

    /**
 * Aprobar o rechazar una recarga.
 */
public function updateRechargeStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:Aprobado,Rechazado',
    ]);

    $recharge = BalanceRecharge::findOrFail($id);

    // Verificar si el agente es el dueño del jugador
    if ($recharge->player->referral_id !== Auth::guard('agent')->user()->referral_id) {
        return redirect()->route('agent.recharges.index')->withErrors('No tienes permiso para gestionar esta recarga.');
    }

    // Actualizar el estado de la recarga
    $recharge->update(['status' => $request->status]);

    // Si se aprueba, aumentar el saldo del jugador
    if ($request->status === 'Aprobado') {
        $player = Player::find($recharge->player_id);
        $player->increment('balance', $recharge->amount);
    }

    return redirect()->route('agent.recharges.index')->with('success', 'Recarga actualizada correctamente.');
}
    public function showPlayerRecharges()
{
    $player = Auth::guard('player')->user();

    $recharges = $player->recharges()->latest()->get(); // Obtener recargas más recientes primero

    return view('player.recharges.index', compact('recharges'));
}

}
