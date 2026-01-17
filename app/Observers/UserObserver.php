<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    public function saved(User $user): void
    {
        // pastikan user punya student
        if ($user->student) {
            // sinkron foto
            if ($user->isDirty('foto')) {
                $user->student->update([
                    'foto' => $user->foto,
                ]);
            }
        }
    }
}
