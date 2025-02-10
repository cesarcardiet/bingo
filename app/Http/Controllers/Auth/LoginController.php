<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Agent;
use App\Models\Player;

class LoginController extends Controller
{
    // Mostrar formulario de login para Agentes
// Mostrar formulario de login para Agentes
public function showAgentLoginForm()
{
    if (Auth::guard('agent')->check()) {
        return redirect()->route('agent.dashboard', ['id' => Auth::guard('agent')->user()->id]);
    }
    return view('auth.agent-login');
}

// Mostrar formulario de login para Jugadores
public function showPlayerLoginForm()
{
    if (Auth::guard('player')->check()) {
        return redirect()->route('player.dashboard', ['id' => Auth::guard('player')->user()->id]);
    }
    return view('auth.player-login');
}


    // Autenticar Agente
    public function agentLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if (Auth::guard('agent')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('agent.dashboard', ['id' => Auth::guard('agent')->user()->id]);
        }

        return back()->withErrors(['email' => 'Credenciales incorrectas.']);
    }

    // supuestamente borra el cahce y detruye la seseion

    public function agentLogout(Request $request)
{
    Auth::guard('agent')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/login/agent')->with('status', 'Sesión cerrada correctamente.');
}

public function playerLogout(Request $request)
{
    Auth::guard('player')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/login/player')->with('status', 'Sesión cerrada correctamente.');
}

    // Autenticar Jugador
    public function playerLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if (Auth::guard('player')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('player.dashboard', ['id' => Auth::guard('player')->user()->id]);
        }

        return back()->withErrors(['email' => 'Credenciales incorrectas.']);
    }

    // Cerrar sesión (Función ÚNICA para agentes y jugadores)
    public function logout(Request $request)
    {
        $guard = $request->session()->get('auth_guard', 'web');

        if ($guard === 'agent') {
            Auth::guard('agent')->logout();
        } elseif ($guard === 'player') {
            Auth::guard('player')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'Sesión cerrada correctamente.');
    }
}
