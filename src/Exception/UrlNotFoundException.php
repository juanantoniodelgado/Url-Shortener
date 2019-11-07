<?php

declare(strict_types=1);

namespace App\Exception;

use \Exception;

class UrlNotFoundException extends Exception
{
    protected $message = 'URL NOT FOUND';
}