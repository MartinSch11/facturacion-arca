<?php

namespace App\Filament\Responses;

use App\Models\User;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Livewire\Features\SupportRedirects\Redirector;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse | Redirector
    {
        $user = Auth::user();

        if ($user->role == 'admin') {
            return redirect()->to('/admin');
        } elseif ($user->role == 'alumno') {
            return redirect()->to('/alumno');
        } elseif ($user->role == 'profesor') {
            return redirect()->to('/profesor');
        }

        return redirect()->to('/app');
    }
}
