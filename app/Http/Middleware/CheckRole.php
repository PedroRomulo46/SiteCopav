<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Aceita papéis separados por vírgula: role:admin, role:fornecedor, role:admin,fornecedor
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        foreach ($roles as $role) {
            $autorizado = match (trim($role)) {
                'admin'      => $user->isAdmin(),
                'fornecedor' => $user->isFornecedor(),
                'cliente'    => $user->isCliente(),
                default      => false,
            };

            if ($autorizado) {
                return $next($request);
            }
        }

        abort(403, 'Você não tem permissão para acessar esta área.');
    }
}