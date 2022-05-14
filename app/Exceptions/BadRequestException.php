<?php

namespace App\Exceptions;

use Exception;

class BadRequestException extends Exception
{
    public const BAD_REQ_EXC_CODE = 'BAD_REQUEST';

    public function register(): void {

    }

}
