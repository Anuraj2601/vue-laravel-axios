<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\App;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Throwable;

class UnauthorizedAccessException extends Exception
{
    /**

    * Register the exception handling callbacks for the application.

    *

    * @return void

    */
    public function register() {
        
    }
}
