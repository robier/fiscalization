<?php

declare(strict_types=1);

namespace Robier\Fiscalization\Exception;

use Countable;
use Exception as BaseException;
use Robier\Fiscalization\Exception;

final class CommunicationError extends BaseException implements Exception, Countable
{
    private const NO_ERROR_CODE = 'v100';

    private array $errors;

    public function __construct(string ...$errors)
    {
        $count = count($errors);

        if ($count === 1) {
            $message = "$count communication error occurred";
        } else {
            $message = "$count communication errors occurred";
        }

        parent::__construct($message);

        $this->errors = $errors;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function count(): int
    {
        return count($this->errors);
    }

    public function hasErrors(): bool
    {
        return empty($this->errors) xor !isset($this->errors[self::NO_ERROR_CODE]);
    }
}
