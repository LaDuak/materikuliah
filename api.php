<?php
/**
 * API sederhana:
 *   GET  api.php?action=list   -> daftar ucapan + statistik (publik)
 *   POST api.php  action=rsvp  -> kirim konfirmasi kehadiran + ucapan
 */
require __DIR__ . '/lib.php';
start_session();
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

function out(array $d, int $code = 200): void
{
    http_response_code($code);
    echo json_encode($d, JSON_UNESCAPED_UNICODE);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'list' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $all = db_read();
    $stats = ['hadir' => 0, 'tidak' => 0, 'ragu' => 0, 'tamu' => 0];
    foreach ($all as $r) {
        $a = $r['attendance'] ?? '';
        if (isset($stats[$a])) {
            $stats[$a]++;
        }
        if ($a === 'hadir') {
            $stats['tamu'] += (int)($r['guests'] ?? 1);
        }
    }
    $wishes = [];
    foreach (array_reverse($all) as $r) {
        if (trim($r['message'] ?? '') === '') {
            continue;
        }
        $wishes[] = [
            'name'       => $r['name'],
            'attendance' => $r['attendance'],
            'message'    => $r['message'],
            'time'       => $r['time'],
        ];
        if (count($wishes) >= 50) {
            break;
        }
    }
    out(['ok' => true, 'stats' => $stats, 'wishes' => $wishes]);
}

if ($action === 'rsvp' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_ok($_POST['csrf'] ?? null)) {
        out(['ok' => false, 'error' => 'Sesi kedaluwarsa, muat ulang halaman lalu coba lagi.'], 403);
    }
    // Honeypot anti-bot: field "website" harus kosong
    if (!empty($_POST['website'])) {
        out(['ok' => true]);
    }
    // Batas kirim: 1x per 10 detik per sesi
    if (!empty($_SESSION['last_post']) && time() - $_SESSION['last_post'] < 10) {
        out(['ok' => false, 'error' => 'Terlalu cepat, tunggu beberapa detik lalu coba lagi.'], 429);
    }

    $name = trim(preg_replace('/\s+/u', ' ', (string)($_POST['name'] ?? '')));
    $att  = (string)($_POST['attendance'] ?? '');
    $max  = (int)(cfg()['max_guests'] ?? 5);
    $guests  = max(1, min($max, (int)($_POST['guests'] ?? 1)));
    $message = trim((string)($_POST['message'] ?? ''));

    if (mb_strlen($name) < 2 || mb_strlen($name) > 60) {
        out(['ok' => false, 'error' => 'Nama harus 2 - 60 karakter.'], 422);
    }
    if (!in_array($att, ['hadir', 'tidak', 'ragu'], true)) {
        out(['ok' => false, 'error' => 'Pilih konfirmasi kehadiran.'], 422);
    }
    if (mb_strlen($message) > 500) {
        out(['ok' => false, 'error' => 'Ucapan maksimal 500 karakter.'], 422);
    }
    if ($att !== 'hadir') {
        $guests = 1;
    }

    $entry = [
        'id'         => bin2hex(random_bytes(6)),
        'name'       => $name,
        'attendance' => $att,
        'guests'     => $guests,
        'message'    => $message,
        'time'       => date('c'),
    ];
    $ok = db_mutate(function (array $d) use ($entry) {
        $d[] = $entry;
        return $d;
    });
    if (!$ok) {
        out(['ok' => false, 'error' => 'Gagal menyimpan. Pastikan folder data/ bisa ditulis (chmod 755/775).'], 500);
    }
    $_SESSION['last_post'] = time();
    out(['ok' => true, 'entry' => ['name' => $name, 'attendance' => $att, 'message' => $message, 'time' => $entry['time']]]);
}

out(['ok' => false, 'error' => 'Permintaan tidak valid.'], 400);
