<?php

namespace A17\CmsToolkit\Commands;

use A17\CmsToolkit\Models\User;
use Illuminate\Console\Command;
use Validator;

class CreateSuperAdmin extends Command
{
    protected $signature = 'user:superadmin';

    protected $description = "Create the superadmin account";

    public function handle()
    {
        $email = $this->ask('Enter an email :');
        if ($this->validateEmail($email)) {
            $password = $this->ask('Enter a password :');
            if ($this->validatePassword($password)) {
                return User::create([
                    'name' => "Admin",
                    'email' => $email,
                    'password' => bcrypt($password),
                    'role' => 'SUPERADMIN',
                    'published' => true,
                ]);
            } else {
                $this->error("Your password is not valid");
            }
        } else {
            $this->error("Your email is not valid");
        }

        $this->info("Your account has been created");
    }

    private function validateEmail($email)
    {
        return Validator::make(['email' => $email], [
            'email' => 'required|email|max:255|unique:users',
        ]);
    }

    private function validatePassword($password)
    {
        return Validator::make(['password' => $password], [
            'password' => 'required|confirmed|min:6',
        ]);
    }
}
