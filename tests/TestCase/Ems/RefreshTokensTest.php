<?php
declare(strict_types=1);

namespace App\Test\TestCase\Ems;

use App\Ems\RefreshDenied;
use App\Ems\RefreshTokens;
use App\Model\Table\EmsRefreshTokensTable;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionInterface;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\TestSuite\TestCase;
use Cake\Utility\Text;

/**
 * The rotating, reuse-detecting refresh-token engine (§ token-at-rest
 * hardening). Every test drives the module through its 3-method interface with
 * an injected clock, proving the invariants that make a stolen token worthless:
 * the DB never holds the raw token, every use rotates, and a replay burns the
 * whole family.
 */
class RefreshTokensTest extends TestCase
{
    use LocatorAwareTrait;

    private const NOW = 1_700_000_000;

    private EmsRefreshTokensTable $tokens;
    private ConnectionInterface $db;
    private string $userId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = ConnectionManager::get('test');
        $this->db->execute('DELETE FROM ems_refresh_tokens');
        /** @var \App\Model\Table\EmsRefreshTokensTable $tokens */
        $tokens = $this->getTableLocator()->get('EmsRefreshTokens');
        $this->tokens = $tokens;
        $this->userId = Text::uuid();
    }

    protected function tearDown(): void
    {
        $this->db->execute('DELETE FROM ems_refresh_tokens');
        parent::tearDown();
    }

    public function testIssueStoresOnlyTheHashAndReturnsRawTokenAndExpiry(): void
    {
        $issued = RefreshTokens::issue($this->tokens, $this->userId, self::NOW);

        // A 256-bit opaque token, hex-encoded.
        $this->assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $issued['token']);

        $ttl = (int)Configure::read('Jwt.refreshTtl', 60 * 60 * 24 * 14);
        $this->assertSame(self::NOW + $ttl, $issued['expiresAt']);

        // The DB holds the SHA-256 hash — never the raw token.
        $rows = $this->allRows();
        $this->assertCount(1, $rows);
        $this->assertSame(hash('sha256', $issued['token']), $rows[0]['token_hash']);
        $this->assertNotSame($issued['token'], $rows[0]['token_hash']);
        $this->assertNull($rows[0]['used_at']);
        $this->assertNull($rows[0]['revoked_at']);
    }

    public function testRotateBurnsThePresentedTokenAndMintsASuccessorInTheSameFamily(): void
    {
        $first = RefreshTokens::issue($this->tokens, $this->userId, self::NOW);
        $firstFamily = $this->allRows()[0]['family_id'];

        $rotated = RefreshTokens::rotate($this->tokens, $first['token'], self::NOW + 10);

        $this->assertSame($this->userId, $rotated['userId']);
        $this->assertNotSame($first['token'], $rotated['token']);

        // The old row is now spent; a fresh row exists in the SAME family.
        $rows = $this->rowsByHash();
        $this->assertNotNull($rows[hash('sha256', $first['token'])]['used_at']);
        $newRow = $rows[hash('sha256', $rotated['token'])];
        $this->assertNull($newRow['used_at']);
        $this->assertSame($firstFamily, $newRow['family_id']);
    }

    public function testExpiredTokenIsRefused(): void
    {
        $issued = RefreshTokens::issue($this->tokens, $this->userId, self::NOW);
        $ttl = (int)Configure::read('Jwt.refreshTtl', 60 * 60 * 24 * 14);

        $this->expectException(RefreshDenied::class);
        RefreshTokens::rotate($this->tokens, $issued['token'], self::NOW + $ttl + 1);
    }

    public function testReplayingARotatedTokenRevokesTheWholeFamily(): void
    {
        $first = RefreshTokens::issue($this->tokens, $this->userId, self::NOW);
        $second = RefreshTokens::rotate($this->tokens, $first['token'], self::NOW + 10);

        // The attacker replays the already-rotated first token.
        $denied = null;
        try {
            RefreshTokens::rotate($this->tokens, $first['token'], self::NOW + 20);
        } catch (RefreshDenied $e) {
            $denied = $e;
        }
        $this->assertNotNull($denied);
        $this->assertSame(401, $denied->statusCode);

        // Every token in the family is now revoked — including the legit successor.
        foreach ($this->allRows() as $row) {
            $this->assertNotNull($row['revoked_at'], 'family member should be revoked');
        }

        // So the victim's live token no longer works either.
        $this->expectException(RefreshDenied::class);
        RefreshTokens::rotate($this->tokens, $second['token'], self::NOW + 30);
    }

    public function testRevokeKillsTheFamilySoLogoutEndsEveryRotatedToken(): void
    {
        $first = RefreshTokens::issue($this->tokens, $this->userId, self::NOW);
        $second = RefreshTokens::rotate($this->tokens, $first['token'], self::NOW + 10);

        RefreshTokens::revoke($this->tokens, $second['token'], self::NOW + 20);

        foreach ($this->allRows() as $row) {
            $this->assertNotNull($row['revoked_at']);
        }

        $this->expectException(RefreshDenied::class);
        RefreshTokens::rotate($this->tokens, $second['token'], self::NOW + 30);
    }

    public function testUnknownTokenIsRefusedButRevokeIsSilent(): void
    {
        // revoke() of an unknown token is idempotent — logging out is never an error.
        RefreshTokens::revoke($this->tokens, 'not-a-real-token', self::NOW);
        $this->assertCount(0, $this->allRows());

        $this->expectException(RefreshDenied::class);
        RefreshTokens::rotate($this->tokens, 'not-a-real-token', self::NOW);
    }

    // --- helpers ------------------------------------------------------------

    private function allRows(): array
    {
        return $this->db->selectQuery()->select('*')->from('ems_refresh_tokens')->execute()->fetchAll('assoc');
    }

    /** Rows keyed by token_hash for direct lookup. */
    private function rowsByHash(): array
    {
        $out = [];
        foreach ($this->allRows() as $row) {
            $out[$row['token_hash']] = $row;
        }

        return $out;
    }
}
