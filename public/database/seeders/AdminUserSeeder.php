<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = 'dev_user@jobifull.com';
        if(User::where('email', $email)->first())
            return;
        User::create([
            'name' => 'dev user',
            'email' => $email,
            'role' => User::ADMIN_ROLE,
            'password' => Hash::make('ROOT12$_431%&v')
        ]);
    }
}
