<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'name'  => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email,' . $user->id],
        ];

        // Only validate password fields if user wants to change it
        if ($request->filled('new_password')) {
            $rules['current_password'] = ['required', 'string'];
            $rules['new_password']     = ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()];
        }

        $request->validate($rules);

        // Verify current password if changing
        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.'])->withInput();
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->name  = $request->name;
        $user->email = $request->email;
        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }
}
