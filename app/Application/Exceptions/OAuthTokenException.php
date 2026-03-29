<?php

namespace App\Application\Exceptions;

use RuntimeException;

class OAuthTokenException extends RuntimeException
{
    public function __construct(
        string $message,
        private readonly int $status = 400,
        private readonly ?string $oauthError = null,
        private readonly array $context = [],
    ) {
        parent::__construct($message);
    }

    public function status(): int
    {
        return $this->status;
    }

    public function oauthError(): ?string
    {
        return $this->oauthError;
    }

    public function context(): array
    {
        return $this->context;
    }
}
