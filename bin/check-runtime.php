<?php
declare(strict_types=1);

use App\Api\Jwt;
use Cake\Datasource\ConnectionManager;
use Pdo\Mysql as PdoMysql;

$caPath = getenv('DATABASE_SSL_CA') ?: '/etc/secrets/ca.pem';

if (!is_file($caPath)) {
    fwrite(STDERR, sprintf("[runtime] Database CA file is missing at %s\n", $caPath));
    exit(1);
}

if (!is_readable($caPath)) {
    fwrite(STDERR, sprintf("[runtime] Database CA file is not readable at %s\n", $caPath));
    exit(1);
}

$contents = file_get_contents($caPath);
$certificate = is_string($contents) ? openssl_x509_read($contents) : false;
if ($certificate === false) {
    fwrite(STDERR, sprintf("[runtime] Database CA file is not a valid PEM certificate at %s\n", $caPath));
    exit(1);
}

fwrite(STDOUT, sprintf(
    "[runtime] Database CA ready at %s, %d bytes, SHA256 %s\n",
    $caPath,
    strlen($contents),
    strtoupper(hash('sha256', $contents)),
));

$databaseUrl = getenv('DATABASE_URL');
if (!is_string($databaseUrl) || $databaseUrl === '') {
    fwrite(STDERR, "[runtime] DATABASE_URL is missing\n");
    exit(1);
}

$database = parse_url($databaseUrl);
if (
    $database === false
    || !isset($database['host'], $database['port'], $database['path'], $database['user'], $database['pass'])
) {
    fwrite(STDERR, "[runtime] DATABASE_URL is incomplete or invalid\n");
    exit(1);
}

$dsn = sprintf(
    'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
    $database['host'],
    $database['port'],
    ltrim($database['path'], '/'),
);
$sslCaAttribute = PHP_VERSION_ID < 80400 ? PDO::MYSQL_ATTR_SSL_CA : PdoMysql::ATTR_SSL_CA;
$verifyAttribute = PHP_VERSION_ID < 80400
    ? PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT
    : PdoMysql::ATTR_SSL_VERIFY_SERVER_CERT;

try {
    $pdo = new PDO(
        $dsn,
        rawurldecode($database['user']),
        rawurldecode($database['pass']),
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            $sslCaAttribute => $caPath,
            $verifyAttribute => true,
        ],
    );
    $pdo->query('SELECT 1');
    fwrite(STDOUT, sprintf(
        "[runtime] Verified MySQL TLS connection ready with PHP %s and %s\n",
        PHP_VERSION,
        (string)$pdo->getAttribute(PDO::ATTR_CLIENT_VERSION),
    ));
} catch (Throwable $verifiedError) {
    fwrite(STDERR, sprintf(
        "[runtime] Verified MySQL TLS probe failed: %s\n",
        $verifiedError->getMessage(),
    ));

    try {
        $pdo = new PDO(
            $dsn,
            rawurldecode($database['user']),
            rawurldecode($database['pass']),
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                $sslCaAttribute => $caPath,
                $verifyAttribute => false,
            ],
        );
        $pdo->query('SELECT 1');
        fwrite(STDERR, "[runtime] MySQL TLS works only when server certificate verification is disabled\n");
    } catch (Throwable $compatibleError) {
        fwrite(STDERR, sprintf(
            "[runtime] Unverified MySQL TLS probe also failed: %s\n",
            $compatibleError->getMessage(),
        ));
    }

    exit(1);
}

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/config/bootstrap.php';

$cakeConnection = ConnectionManager::get('default');
$cakeConfig = $cakeConnection->getDriver()->config();
$rawUsername = rawurldecode($database['user']);
$rawPassword = rawurldecode($database['pass']);
$cakeUsername = (string)($cakeConfig['username'] ?? '');
$cakePassword = (string)($cakeConfig['password'] ?? '');
fwrite(STDOUT, sprintf(
    '[runtime] CakePHP database config host_match=%s port_match=%s database_match=%s '
        . "username_match=%s password_match=%s ca=%s verify=%s app_local=%s\n",
    ($cakeConfig['host'] ?? null) === $database['host'] ? 'yes' : 'no',
    (int)($cakeConfig['port'] ?? 0) === (int)$database['port'] ? 'yes' : 'no',
    ($cakeConfig['database'] ?? null) === ltrim($database['path'], '/') ? 'yes' : 'no',
    hash_equals(
        hash('sha256', $rawUsername),
        hash('sha256', $cakeUsername),
    ) ? 'yes' : 'no',
    hash_equals(
        hash('sha256', $rawPassword),
        hash('sha256', $cakePassword),
    ) ? 'yes' : 'no',
    (string)($cakeConfig['flags'][$sslCaAttribute] ?? 'missing'),
    ($cakeConfig['flags'][$verifyAttribute] ?? null) === true ? 'true' : 'missing_or_false',
    is_file(dirname(__DIR__) . '/config/app_local.php') ? 'present' : 'absent',
));

try {
    $cakeConnection->execute('SELECT 1');
    fwrite(STDOUT, "[runtime] CakePHP MySQL TLS connection ready\n");
} catch (Throwable $cakeError) {
    fwrite(STDERR, sprintf(
        "[runtime] CakePHP MySQL TLS probe failed: %s: %s\n",
        get_class($cakeError),
        $cakeError->getMessage(),
    ));
    exit(1);
}

// Fail closed on a missing/placeholder/too-weak JWT signing secret. encode() and
// decode() both route through the same guarded App\Api\Jwt::secret(), so an
// unusable key means the app can neither mint nor verify a token. Refusing to
// boot with a clear message beats serving 500s at runtime — or, if a real-looking
// default is left in place, silently running on a forgeable key.
try {
    Jwt::encode(['sub' => 'runtime-probe', 'type' => 'ems'], 60, time());
    fwrite(STDOUT, "[runtime] JWT signing secret is configured\n");
} catch (Throwable $jwtError) {
    fwrite(STDERR, sprintf("[runtime] %s\n", $jwtError->getMessage()));
    exit(1);
}
