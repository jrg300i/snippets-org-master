<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use App\Models\User;
use Illuminate\Http\Request;

class AuditoriaController extends Controller
{
    public function index(Request $request)
    {
        $query = Auditoria::with('user')->latest();

        if ($request->filled('accion')) {
            $query->where('accion', $request->input('accion'));
        }

        if ($request->filled('entidad')) {
            $query->where('entidad', $request->input('entidad'));
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        if ($request->filled('ip')) {
            $query->where('ip', 'like', '%' . trim($request->input('ip')) . '%');
        }

        if ($request->filled('desde')) {
            $query->whereDate('created_at', '>=', $request->input('desde'));
        }

        if ($request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $request->input('hasta'));
        }

        $auditorias = $query->paginate(25)->withQueryString();
        $usuarios = User::orderBy('name')->get();

        return view('auditorias.index', compact('auditorias', 'usuarios'));
    }
}