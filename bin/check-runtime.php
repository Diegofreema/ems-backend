<?php
declare(strict_types=1);

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
