<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\Serial;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
            'serial' => ['required']
        ])->validate();

        // checking whether the new user will be an admin
        if($input['serial'] == 000000000){
            $input['serial'] == null;
            $input['is_admin'] = null;
        }else{
            $serial = new Serial();
            $serialObj = $serial->where('email_using', $input['email']);
            if(!$serialObj){
                $input['is_admin'] = null;
            }else{
                $input['is_admin'] = true;
            }
        }

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'is_admin' => $input['is_admin'],
            'license_number' => $input['serial']
        ]);
    }
}
