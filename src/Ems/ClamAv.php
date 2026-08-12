<?php
declare(strict_types=1);

namespace App\Ems;

/** Minimal ClamAV INSTREAM client. Unavailable scanners fail closed. */
final class ClamAv
{
    /**
     * Scan one private evidence payload and fail closed when unavailable.
     *
     * @param string $bytes Untrusted uploaded evidence bytes.
     * @return string One of clean, rejected, or quarantined.
     */
    public function scan(string $bytes): string
    {
        $host = (string)getenv('EMS_CLAMAV_HOST');
        $port = (int)(getenv('EMS_CLAMAV_PORT') ?: 3310);
        if ($host === '') {
            return 'quarantined';
        }
        set_error_handler(static function (): bool {
            return true;
        });
        try {
            $socket = stream_socket_client("tcp://{$host}:{$port}", $errno, $error, 3);
        } finally {
            restore_error_handler();
        }
        if (!$socket) {
            return 'quarantined';
        }
        stream_set_timeout($socket, 5);
        fwrite($socket, "zINSTREAM\0");
        foreach (str_split($bytes, 8192) as $chunk) {
            fwrite($socket, pack('N', strlen($chunk)) . $chunk);
        }
        fwrite($socket, pack('N', 0));
        $reply = (string)stream_get_contents($socket);
        fclose($socket);
        if (str_contains($reply, 'FOUND')) {
            return 'rejected';
        }

        return str_contains($reply, 'OK') ? 'clean' : 'quarantined';
    }
}
