<?php

namespace App\Http\Controllers\Auth\Trait;

use App\Events\Auth\UserLoginSuccess;
use App\Events\Frontend\UserRegistered;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

trait AuthTrait
{
    protected function loginTrait($request)
    {
        $email = $request->email;
        $password = $request->password;
        $remember = $request->remember_me;

        if (Auth::attempt(['email' => $email, 'password' => $password, 'status' => 1], $remember)) {
            event(new UserLoginSuccess($request, auth()->user()));

            return true;
        }

        return false;
    }

    protected function registerTrait($request, $model = null)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:191'],
            'last_name' => ['required', 'string', 'max:191'],
            'email' => ['required', 'string', 'email', 'max:191', 'unique:users'],
            'password' => ['required', Rules\Password::defaults()],
            'gender' => ['required'],
        ]);

        $arr = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->first_name.' '.$request->last_name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'gender' => $request->gender,
            'password' => Hash::make($request->password),
        ];

        $user = User::create($arr);

        $user->syncRoles(['user']);

        \Artisan::call('cache:clear');

        $user->save();

        event(new Registered($user));
        event(new UserRegistered($user));

        return $user;
    }
}
