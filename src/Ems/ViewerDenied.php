<?php
declare(strict_types=1);

namespace App\Ems;

use RuntimeException;

/**
 * A refusal raised while resolving the live Viewer (App\Ems\ViewerResolver).
 * It carries the HTTP status the spine should answer with so the resolver can
 * stay a pure function — decode the caller, or throw — while AppController owns
 * turning the refusal into the contract's {message} error body.
 *
 * The named constructors are the whole vocabulary of ways a validly-signed
 * token can still be refused once we consult live server state.
 */
final class ViewerDenied extends RuntimeException
{
    /**
     * @var int The HTTP status AppController should respond with.
     */
    public int $statusCode;

    private function __construct(int $statusCode, string $message)
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
    }

    /**
     * The token is well-formed but names a principal that no longer resolves to
     * a live user row (deleted/unknown `sub`). Treat as unauthenticated.
     */
    public static function unknownPrincipal(): self
    {
        return new self(401, 'Your session has expired. Please sign in again.');
    }

    /**
     * The live user row exists but its account is not active (invited/disabled).
     * Answered as 401, not 403: a disabled credential is a dead session, so the
     * SPA tears it down through the same 401 path as an expired token (security
     * candidate #5) rather than leaving the user stuck re-sending it.
     */
    public static function accountDisabled(): self
    {
        return new self(401, Messages::ACCOUNT_DISABLED);
    }

    /** The requested tenant is not the school the live user belongs to. */
    public static function schoolForbidden(): self
    {
        return new self(403, Messages::SCHOOL_FORBIDDEN);
    }
}
