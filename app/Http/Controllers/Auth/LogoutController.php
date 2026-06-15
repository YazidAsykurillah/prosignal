<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    /**
     * Handle a logout request.
     */
    public function destroy(Request $request)
    {
        // Log activity before logout (while user is still authenticated)
        activity('auth')
            ->performedOn(Auth::user())
            ->causedBy(Auth::user())
            ->log('User Logged Out');

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
