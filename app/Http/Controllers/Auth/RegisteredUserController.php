<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Mandal;
use App\Models\User;
use App\Models\WorkingOffice;
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
        $workingOffices = WorkingOffice::all();
        return view('auth.register', compact('workingOffices'));
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
            'working_office_id' => ['required', 'integer', 'exists:working_offices,id'],
        ]);
 
        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'working_office_id' => (int) $request->working_office_id,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 0, // Set default status to inactive (0) for new users
        ]);
 
        event(new Registered($user));
 
        // Don't login immediately - wait for admin approval
        return redirect(route('login', absolute: false))
            ->with('success', 'Registration Successful! Please wait for Admin to Activate your Account.');
    }
}
