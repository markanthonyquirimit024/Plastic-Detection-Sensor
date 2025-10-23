<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $adminExists = User::where('utype', 'Admin')->exists();

        if (!$adminExists) {
            User::create([
                'first_name' => 'Admin', 
                'last_name' => 'Admin',
                'email' => 'admin@phinmaed.com',
                'password' => Hash::make('admin'),
                'utype' => 'Admin',
                'email_verified_at' => Carbon::now(),
            ]);
        }
    }
}