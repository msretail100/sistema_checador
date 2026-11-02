<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $username = config('attendance.root_username');
        $password = config('attendance.root_password');

        if (! $username || ! $password) {
            throw new RuntimeException('Define ROOT_USERNAME y ROOT_PASSWORD antes de ejecutar db:seed.');
        }

        if (! preg_match('/\A[a-zA-Z0-9_-]{1,80}\z/', $username)) {
            throw new RuntimeException('ROOT_USERNAME debe usar solo letras, números, guiones y guiones bajos.');
        }

        if (Str::length($password) < 12) {
            throw new RuntimeException('ROOT_PASSWORD debe tener al menos 12 caracteres.');
        }

        User::query()->updateOrCreate(
            ['username' => $username],
            [
                'name' => config('attendance.root_name'),
                'role' => UserRole::Root->value,
                'is_active' => true,
                'password' => $password,
            ],
        );
    }
}
