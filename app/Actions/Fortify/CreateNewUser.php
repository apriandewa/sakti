<?php

namespace App\Actions\Fortify;

use App\Models\User;
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
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        $parts = explode(' ', trim($input['name']), 2);
        $firstName = $parts[0];
        $lastName = isset($parts[1]) ? $parts[1] : null;

        return User::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'level_id' => 3,
            'access_group_id' => '3',
        ]);
    }
}
