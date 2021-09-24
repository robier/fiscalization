<?php

declare(strict_types=1);

namespace Robier\Fiscalization;

interface Tax
{
    public function base();

    public function rate();

    public function amount();

    public function sum();
}
