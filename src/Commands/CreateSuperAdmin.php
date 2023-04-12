<?php

namespace A17\Twill\Commands;

use Illuminate\Config\Repository as Config;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Factory as ValidatorFactory;
use Carbon\Carbon;

class CreateSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twill:superadmin {email?} {password?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Create the superadmin account";

    /**
     * @var ValidatorFactory
     */
    protected $validatorFactory;

    /**
     * @var Config
     */
    protected $config;

    public function __construct(ValidatorFactory $validatorFactory, Config $config)
    {
        parent::__construct();

        $this->validatorFactory = $validatorFactory;
        $this->config = $config;
    }

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

        $userModel = twillModel('user');
        $user = new $userModel();
        $user->fill([
            'name' => "Admin",
            'email' => $email,
            'published' => true,
        ]);

        if (config('twill.enabled.permissions-management')) {
            $user->is_superadmin = true;
        } else {
            $user->role = 'SUPERADMIN';
        }

        $user->registered_at = Carbon::now();
        $user->password = Hash::make($password);
        if ($user->save()) {
            $this->info('Your account has been created');
            return;
        }

        $this->error('Failed creating user. Things you can check: Database permissions, run migrations');
    }

    /**
     * Prompt user to enter email and validate it.
     *
     * @return string $email
     */
    private function setEmail()
    {
        if (filled($email = $this->argument('email'))) {
            return $email;
        }

        $email = $this->ask('Enter an email');
        if ($this->validateEmail($email)) {
            return $email;
        } else {
            $this->error("Your email is not valid");
            return $this->setEmail();
        }
    }

    /**
     * Prompt user to enter password, confirm and validate it.
     *
     * @return string $password
     */
    private function setPassword()
    {
        if (filled($password = $this->argument('password'))) {
            return $password;
        }

        $password = $this->secret('Enter a password');
        if ($this->validatePassword($password)) {
            $confirmPassword = $this->secret('Confirm the password');
            if ($password === $confirmPassword) {
                return $password;
            } else {
                $this->error('Password does not match the confirm password');
                return $this->setPassword();
            }
        } else {
            $this->error("Your password is not valid, at least 6 characters");
            return $this->setPassword();
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
        return $this->validatorFactory->make(['email' => $email], [
            'email' => 'required|email|max:255|unique:' . $this->config->get('twill.users_table'),
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
        return $this->validatorFactory->make(['password' => $password], [
            'password' => 'required|min:6',
        ])->passes();
    }
}
