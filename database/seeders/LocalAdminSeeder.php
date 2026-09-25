<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Local development login only (never run in production):
 *   php artisan db:seed --class=LocalAdminSeeder
 * Email: dev@ismail.test · Password: dev-secret-2026
 */
class LocalAdminSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            return;
        }

        User::updateOrCreate(['email' => 'dev@ismail.test'], ['name' => 'Dev Admin', 'password' => 'dev-secret-2026']);
    }
}
