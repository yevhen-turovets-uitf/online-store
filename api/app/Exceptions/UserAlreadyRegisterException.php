<?php

declare(strict_types=1);

namespace App\Exceptions;

use Throwable;

final class UserAlreadyRegisterException extends BaseException
{
    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct(__('register.user_already_registered'), 400, $previous);
    }
}
