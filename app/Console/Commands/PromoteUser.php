<?php

namespace App\Console\Commands;

use App\Models\User;
use App\UserRole;
use Illuminate\Console\Command;

class PromoteUser extends Command
{
    /** @var string */
    protected $signature = 'qrbita:make-admin {email : Correo de la cuenta registrada}';

    /** @var string */
    protected $description = 'Promueve una cuenta existente al rol de administrador';

    public function handle(): int
    {
        $user = User::query()
            ->where('email', $this->argument('email'))
            ->first();

        if (! $user) {
            $this->error('No existe una cuenta con ese correo electrónico.');

            return self::FAILURE;
        }

        $user->update(['role' => UserRole::Administrator]);
        $this->info("{$user->email} ahora es una cuenta administradora.");

        return self::SUCCESS;
    }
}
