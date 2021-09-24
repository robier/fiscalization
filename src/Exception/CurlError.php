<?php

declare(strict_types=1);

namespace Robier\Fiscalization\Exception;

use Exception as BaseException;
use Robier\Fiscalization\Exception;

class CurlError extends BaseException implements Exception
{
    public function __construct($message = '', $code = 0)
    {
        parent::__construct($message, $code);
    }
}
