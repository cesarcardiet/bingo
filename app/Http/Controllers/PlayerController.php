<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;
use App\Models\Agent;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\BingoCard;
use App\Models\Room;

class PlayerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:player')->except(['register', 'showRegistrationForm']);
        $this->middleware(function ($request, $next) {
            header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
            header("Pragma: no-cache");
            header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");
            return $next($request);
        });
    }

    /**
     * Muestra el formulario de registro para jugadores con código de referido.
     */
    public function showRegistrationForm(Request $request)
    {
        $referralCode = $request->query('ref');
    
        // Buscar al agente usando `referral_id`
        $agent = Agent::where('referral_id', $referralCode)->first();
    
        if (!$agent) {
            return redirect('/register/player')->withErrors('El código de referido no es válido.');
        }
    
        return view('auth.player-register', compact('referralCode'));
    }

    /**
     * Registra un nuevo jugador con código de referido válido.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:players',
            'password' => 'required|string|min:6|confirmed',
        ]);
    
        // Buscar al agente usando el código de referido
        $agent = Agent::where('referral_id', $request->referral_id)->first();
    
        if (!$agent) {
            return redirect()->back()->withErrors('El código de referido no es válido.');
        }
    
        // Crear el jugador con el agent_id
        Player::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'agent_id' => $agent->id,
            'referral_id' => $request->referral_id,
        ]);
    
        return redirect('/login/player')->with('success', 'Registro exitoso. Ahora puedes iniciar sesión.');
    }

    /**
     * Muestra el dashboard del jugador autenticado.
     */
    public function dashboard()
    {
        $player = Auth::guard('player')->user();
    
        // Obtener solo las salas activas del agente asignado al jugador
        $rooms = Room::where('agent_id', $player->agent_id)->get();
    
        return view('player.dashboard', compact('player', 'rooms'));
    }
    
    /**
     * Permite a un jugador comprar cartones.
     */
    public function buyCard(Request $request)
    {
        $player = Auth::guard('player')->user();
        $selectedCards = json_decode($request->selected_cards, true);

        if (!$selectedCards || count($selectedCards) == 0) {
            return back()->withErrors('No has seleccionado ningún cartón.');
        }

        $cards = BingoCard::whereIn('id', $selectedCards)
                          ->where('status', 'Disponible')
                          ->get();

        if ($cards->count() !== count($selectedCards)) {
            return back()->withErrors('Uno o más cartones ya han sido comprados.');
        }

        $totalPrice = $cards->sum(fn($card) => $card->room->card_price);

        if ($player->balance < $totalPrice) {
            return back()->withErrors('Saldo insuficiente. Por favor, recarga tu cuenta.');
        }

        foreach ($cards as $card) {
            $card->update([
                'player_id' => $player->id,
                'status' => 'Comprado'
            ]);
        }

        $roomId = $cards->first()->room_id;

        return redirect()->route('player.my-cards', ['room_id' => $roomId])
            ->with('success', 'Cartones comprados con éxito.');
    }

    /**
     * Muestra los sorteos de una sala específica donde el jugador ha comprado cartones.
     */
    public function viewSorteo($roomId)
    {
        $player = Auth::guard('player')->user();
        $room = Room::findOrFail($roomId);

        $playerCards = BingoCard::where('player_id', $player->id)
            ->where('room_id', $roomId)
            ->get();

        if ($playerCards->isEmpty()) {
            return redirect()->route('player.dashboard')->withErrors('No tienes cartones en esta sala.');
        }

        return view('player.sorteo', compact('room', 'playerCards'));
    }

    /**
     * Muestra las salas en las que el jugador ha comprado cartones.
     */
    public function myCards(Request $request)
    {
        $player = Auth::guard('player')->user();
        
        // Verificar si el room_id se está enviando correctamente
        $roomId = $request->query('room_id');
    
        if (!$roomId) {
            return redirect()->route('player.dashboard')->withErrors('No se ha seleccionado una sala.');
        }
    
        // Verificar si la sala realmente existe
        $room = Room::find($roomId);
        if (!$room) {
            return redirect()->route('player.dashboard')->withErrors('La sala no existe.');
        }
    
        // Obtener los cartones comprados por el jugador en esta sala
        $playerCards = BingoCard::where('player_id', $player->id)
                                ->where('room_id', $roomId)
                                ->get();
    
        return view('player.my-cards', compact('room', 'playerCards'));
    }
    
    

    /**
     * Muestra los cartones comprados en una sala específica.
     */
    public function myCardsByRoom($room_id)
    {
        $player = Auth::guard('player')->user();
        $room = Room::findOrFail($room_id);

        $cards = BingoCard::where('room_id', $room->id)
                          ->where('player_id', $player->id)
                          ->get();

        return view('player.my-cards-by-room', compact('room', 'cards'));
    }
}
