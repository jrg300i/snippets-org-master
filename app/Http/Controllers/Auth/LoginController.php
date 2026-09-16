<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Services\AuditoriaService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct(private readonly AuditoriaService $auditoria)
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        $this->auditoria->registrar('login', 'usuario', $user->id, "Inicio de sesión de {$user->email}.");
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        $this->auditoria->registrar(
            'logout',
            'usuario',
            $user?->id,
            $user ? "Cierre de sesión de {$user->email}." : 'Cierre de sesión.',
        );

        $this->guard()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}