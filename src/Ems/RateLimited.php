<?php
declare(strict_types=1);

namespace App\Ems;

use Cake\Http\Exception\HttpException;

/**
 * The 429 raised by {@see RateLimiter}. It IS-A HttpException(429) — so every
 * existing catch and status assertion keeps working — but additionally carries
 * how many seconds until the window resets, which AppController turns into the
 * standard `Retry-After` response header. RateLimiter stays free of any header
 * or HTTP-transport knowledge; only the count-of-seconds crosses the seam.
 */
final class RateLimited extends HttpException
{
    /** @var int Seconds the caller should wait before retrying. */
    public int $retryAfter;

    public function __construct(int $retryAfter)
    {
        parent::__construct(Messages::RATE_LIMITED, 429);
        $this->retryAfter = max(1, $retryAfter);
    }
}
