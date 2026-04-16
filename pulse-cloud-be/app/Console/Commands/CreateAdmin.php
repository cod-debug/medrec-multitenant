<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Create Admin
        $name = $this->ask('Full Name');
        $email = $this->ask('Email Address');
        $username = $this->ask('Username');
        $password_match = false;

        while (!$password_match) {
            $password = $this->secret('Provide a password');
            $confirmation = $this->secret('Enter the password again');

            $password_match = ($password == $confirmation);
            if (!$password_match) {
                $this->error('Passwords do not match. Try again.');
                $this->newLine();
            }
        }
        $attributes = [
            'email' => $email,
        ];

        $data = [
            'name' => $name,
            'username' => $username,
            'password' => bcrypt($password),
            'level_of_authorization' => 1
        ];
        
        User::firstOrCreate($attributes, $data);

        $this->info('User created');
    }
}
