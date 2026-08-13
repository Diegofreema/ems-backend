<?php
declare(strict_types=1);

namespace App\Ems;

use RuntimeException;

/**
 * A refusal raised while rotating or validating a refresh token
 * (App\Ems\RefreshTokens). Mirrors ViewerDenied: it carries the HTTP status the
 * auth action should answer with, so RefreshTokens can stay a pure engine —
 * accept the presented token, or throw — while AuthController owns clearing the
 * cookie and rendering the contract's {message} body.
 */
final class RefreshDenied extends RuntimeException
{
    /**
     * @var int The HTTP status AuthController should respond with.
     */
    public int $statusCode;

    private function __construct(int $statusCode, string $message)
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
    }

    /**
     * The presented refresh token is unknown, expired, already used/revoked, or
     * a replay of a rotated-away token (its family is revoked before this throws).
     * Every case means the session is dead — answered 401 so the SPA tears it
     * down through the same path as an expired access token (security candidate #5).
     */
    public static function dead(): self
    {
        return new self(401, 'Your session has expired. Please sign in again.');
    }
}
