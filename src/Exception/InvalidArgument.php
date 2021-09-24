<?php

declare(strict_types=1);

namespace Robier\Fiscalization\Exception;

use InvalidArgumentException;
use Robier\Fiscalization\Exception;
use Throwable;

final class InvalidArgument extends InvalidArgumentException implements Exception
{
    public function __construct($message = '', Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
