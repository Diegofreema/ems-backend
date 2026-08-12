<?php
declare(strict_types=1);

namespace App\Ems;

use RuntimeException;

/** Loads active and retained finance signing keys from process secrets. */
final class FinanceKeys
{
    /**
     * @return array{0: string, 1: string} Active key identifier and secret.
     */
    public static function active(): array
    {
        $keys = self::verificationKeys();
        $id = trim((string)getenv('EMS_FINANCE_AUDIT_HMAC_ACTIVE_KEY_ID'));
        if ($id === '') {
            $id = trim((string)getenv('EMS_FINANCE_HMAC_KEY_ID'));
        }
        $key = $keys[$id] ?? '';
        if ($id === '' || strlen($key) < 32) {
            throw new RuntimeException(
                'Finance audit signing is not configured with an active key of at least 32 bytes.'
            );
        }

        return [$id, $key];
    }

    /**
     * @return array<string, string> Keys indexed by immutable key identifier.
     */
    public static function verificationKeys(): array
    {
        $keys = [];
        foreach (['EMS_FINANCE_HMAC_KEYS', 'EMS_FINANCE_AUDIT_HMAC_KEYS_JSON'] as $name) {
            $json = trim((string)getenv($name));
            if ($json === '') {
                continue;
            }
            $decoded = json_decode($json, true);
            if (!is_array($decoded)) {
                throw new RuntimeException($name . ' must be a JSON object of key identifiers and secrets.');
            }
            foreach ($decoded as $id => $key) {
                if (!is_string($id) || $id === '' || !is_string($key) || $key === '') {
                    throw new RuntimeException($name . ' contains an invalid key entry.');
                }
                $keys[$id] = $key;
            }
        }

        $legacyId = trim((string)getenv('EMS_FINANCE_HMAC_KEY_ID'));
        $legacyKey = (string)getenv('EMS_FINANCE_HMAC_KEY');
        if ($legacyId !== '' && $legacyKey !== '') {
            $keys[$legacyId] = $legacyKey;
        }

        return $keys;
    }
}
