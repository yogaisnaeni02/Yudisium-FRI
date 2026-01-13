<?php

namespace App\Exceptions;

use Exception;

class AccountNotRegisteredException extends Exception
{
    /**
     * The exception description.
     *
     * @var string
     */
    protected $message = 'Akun tidak terdaftar di sistem.';

    /**
     * Report the exception.
     */
    public function report(): bool
    {
        return false;
    }

    /**
     * Render the exception into an HTTP response.
     */
    public function render()
    {
        return response()->json([
            'message' => $this->message,
        ], 401);
    }
}
