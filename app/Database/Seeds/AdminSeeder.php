<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;
use RuntimeException;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $users = model(UserModel::class);
        $email = 'kantor@ndtaxandlaw.local';

        if ($users->findByCredentials(['email' => $email])) {
            return;
        }

        $user = new User([
            'username' => 'pengelola',
            'email'    => $email,
            'password' => 'Pelindung#Kartu88',
        ]);

        if (! $users->save($user)) {
            throw new RuntimeException(implode(' ', $users->errors()));
        }

        $user = $users->findById($users->getInsertID());
        $user->activate();
        $user->addGroup('admin');
    }
}
