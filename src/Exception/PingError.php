<?php

declare(strict_types=1);

namespace Robier\Fiscalization\Exception;

use Exception as BaseException;
use Robier\Fiscalization\Exception;

final class PingError extends BaseException implements Exception
{
    public function __construct()
    {
        parent::__construct('Server could not be reached');
    }
}
