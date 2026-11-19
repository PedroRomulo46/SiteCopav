<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $dados = $request->validated();

        if ($request->hasFile('imagem')) {
            $arquivo = $request->file('imagem');

            if (! $arquivo->isValid()) {
                return Redirect::route('profile.edit')
                    ->withErrors(['imagem' => 'O upload da imagem falhou (código: ' . $arquivo->getError() . '). Verifique o tamanho máximo no PHP.']);
            }

            $caminhoTemp = $arquivo->getRealPath();

            if (! $caminhoTemp || ! file_exists($caminhoTemp)) {
                return Redirect::route('profile.edit')
                    ->withErrors(['imagem' => 'Arquivo temporário não encontrado no servidor. Verifique upload_tmp_dir no php.ini.']);
            }

            $pastaDestino = storage_path('app/public/perfis');
            if (! is_dir($pastaDestino)) {
                mkdir($pastaDestino, 0775, true);
            }

            $extensao = $arquivo->guessExtension() ?: $arquivo->getClientOriginalExtension() ?: 'jpg';
            $nomeArquivo = 'perfil_' . uniqid('', true) . '.' . $extensao;

            if (! $arquivo->move($pastaDestino, $nomeArquivo)) {
                return Redirect::route('profile.edit')
                    ->withErrors(['imagem' => 'Não foi possível mover a imagem para a pasta de perfis.']);
            }

            if ($request->user()->imagem) {
                Storage::disk('public')->delete($request->user()->imagem);
            }

            $dados['imagem'] = 'perfis/' . $nomeArquivo;
        }

        $request->user()->fill($dados);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')
            ->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}