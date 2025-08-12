<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $adminExists = User::where('utype', 'ADM')->exists();

        if (!$adminExists) {
            User::create([
                'first_name' => 'Admin', 
                'last_name' => 'Admin',
                'email' => 'admin@phinmaed.com',
                'password' => bcrypt('admin'),
                'utype' => 'ADM',
            ]);
        }
}
}