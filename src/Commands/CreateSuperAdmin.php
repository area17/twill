<?php

namespace Sb4yd3e\Twill\Commands;

use Sb4yd3e\Twill\Models\User;
use Illuminate\Console\Command;
use Validator;

class CreateSuperAdmin extends Command
{
    protected $signature = 'twill:superadmin';

    protected $description = "Create the superadmin account";

    /**
     * Create super admin account.
     *
     * @return void
     */
    public function handle()
    {
        $this->info("Let's create a superadmin account!");
        $email = $this->setEmail();
        $password = $this->setPassword();
        User::create([
            'name' => "Admin",
            'email' => $email,
            'password' => bcrypt($password),
            'role' => 'SUPERADMIN',
            'published' => true,
        ]);
        $this->info("Your account has been created");
    }

    /**
     * Prompt user to enter email and validate it.
     *
     * @return string $email
     */
    private function setEmail()
    {
        $email = $this->ask('Enter an email');
        if ($this->validateEmail($email)) {
            return $email;
        } else {
            $this->error("Your email is not valid");
            $this->setEmail();
        }
    }

    /**
     * Prompt user to enter password, confirm and validate it.
     *
     * @return string $password
     */
    private function setPassword()
    {
        $password = $this->secret('Enter a password');
        if ($this->validatePassword($password)) {
            $confirmPassword = $this->secret('Confirm the password');
            if ($password === $confirmPassword) {
                return $password;
            } else {
                $this->error('Password does not match the confirm password');
                $this->setPassword();
            }
        } else {
            $this->error("Your password is not valid, at least 6 characters");
            $this->setPassword();
        }
    }

    /**
     * Determine if the email address given valid.
     *
     * @param  string  $email
     * @return boolean
     */
    private function validateEmail($email)
    {
        return Validator::make(['email' => $email], [
            'email' => 'required|email|max:255|unique:' . config('twill.users_table'),
        ])->passes();
    }

    /**
     * Determine if the password given valid.
     *
     * @param  string  $password
     * @return boolean
     */
    private function validatePassword($password)
    {
        return Validator::make(['password' => $password], [
            'password' => 'required|min:6',
        ])->passes();
    }
}
