<?php
/**
 * Fungsi bantu: konfigurasi, escape HTML, penyimpanan data (file), CSRF.
 * Data disimpan di data/rsvp.php (diawali kode PHP penutup supaya tidak
 * bisa dibaca lewat browser, walau .htaccess tidak aktif).
 */

date_default_timezone_set('Asia/Jakarta');

function cfg(): array
{
    static $c = null;
    if ($c === null) {
        $c = require __DIR__ . '/config.php';
    }
    return $c;
}

function e($s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

const DB_FILE   = __DIR__ . '/data/rsvp.php';
const DB_HEADER = "<?php http_response_code(403); exit; ?>\n";

function db_parse(string $raw): array
{
    if ($raw === '') {
        return [];
    }
    $json = preg_replace('/^<\?php.*?\?>\s*/s', '', $raw, 1);
    $d = json_decode((string)$json, true);
    return is_array($d) ? $d : [];
}

function db_read(): array
{
    if (!is_file(DB_FILE)) {
        return [];
    }
    return db_parse((string)file_get_contents(DB_FILE));
}

/** Ubah data dengan aman (dikunci agar tidak bentrok saat banyak yang kirim). */
function db_mutate(callable $fn): bool
{
    $dir = dirname(DB_FILE);
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    $fp = @fopen(DB_FILE, 'c+');
    if (!$fp) {
        return false;
    }
    flock($fp, LOCK_EX);
    $data = db_parse((string)stream_get_contents($fp));
    $data = $fn($data);
    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, DB_HEADER . json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
    return true;
}

function start_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
        session_start();
    }
}

function csrf_token(): string
{
    start_session();
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function csrf_ok(?string $t): bool
{
    start_session();
    return !empty($_SESSION['csrf']) && is_string($t) && hash_equals($_SESSION['csrf'], $t);
}

function attendance_label(string $a): string
{
    return ['hadir' => 'Hadir', 'tidak' => 'Tidak Hadir', 'ragu' => 'Masih Ragu'][$a] ?? $a;
}
