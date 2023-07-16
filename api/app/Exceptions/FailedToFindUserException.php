<?php

declare(strict_types=1);

namespace App\Exceptions;

use Throwable;

final class FailedToFindUserException extends BaseException
{
    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct(__('auth.failed_to_find_user'), 400, $previous);
    }
}
