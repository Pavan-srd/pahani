<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Mandal;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $mandals = Mandal::all();
        return view('auth.register', compact('mandals'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'mandal_id' => ['required', 'integer', 'exists:mandals,id'],
        ]);
 
        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'mandal_id' => (int) $request->mandal_id,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
 
        /**
         * NEW: Save the selected mandal to user_mandals pivot table
         * This establishes the many-to-many relationship between user and mandal
         */
        $user->mandals()->attach((int) $request->mandal_id);
 
        event(new Registered($user));
 
        Auth::login($user);
 
        return redirect(route('dashboard', absolute: false));
    }
}
