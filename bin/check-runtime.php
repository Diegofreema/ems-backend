<?php
declare(strict_types=1);

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
