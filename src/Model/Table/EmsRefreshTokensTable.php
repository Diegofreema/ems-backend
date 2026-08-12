<?php
declare(strict_types=1);

namespace App\Model\Table;

/**
 * EMS contract table `ems_refresh_tokens` (token-at-rest hardening). Rows are
 * opaque-token HASHES, never the token itself. Timestamps are written explicitly
 * by App\Ems\RefreshTokens from an injected clock (so the module stays a pure
 * function of `now` and is testable), hence no Timestamp behavior here.
 */
class EmsRefreshTokensTable extends EmsTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('ems_refresh_tokens');
        $this->setPrimaryKey('id');
    }
}
