<?php
require __DIR__ . '/lib.php';
start_session();
header('X-Frame-Options: DENY');
header('X-Robots-Tag: noindex, nofollow');

$c = cfg();
$error = '';

// ---------- Login / logout ----------
if (isset($_POST['login'])) {
    if (csrf_ok($_POST['csrf'] ?? null) && hash_equals((string)$c['admin_password'], (string)($_POST['password'] ?? ''))) {
        session_regenerate_id(true);
        $_SESSION['admin'] = true;
        header('Location: admin.php');
        exit;
    }
    sleep(1); // perlambat tebak password
    $error = 'Password salah.';
}
if (isset($_POST['logout']) && csrf_ok($_POST['csrf'] ?? null)) {
    $_SESSION = [];
    session_destroy();
    header('Location: admin.php');
    exit;
}

$isAdmin = !empty($_SESSION['admin']);

// ---------- Aksi admin ----------
if ($isAdmin) {
    if (isset($_POST['delete']) && csrf_ok($_POST['csrf'] ?? null)) {
        $id = (string)$_POST['delete'];
        db_mutate(function (array $d) use ($id) {
            return array_values(array_filter($d, function ($r) use ($id) { return ($r['id'] ?? '') !== $id; }));
        });
        header('Location: admin.php');
        exit;
    }
    if (isset($_GET['export'])) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="rsvp-' . date('Ymd-His') . '.csv"');
        $out = fopen('php://output', 'w');
        fwrite($out, "\xEF\xBB\xBF"); // BOM agar Excel membaca UTF-8
        fputcsv($out, ['Waktu', 'Nama', 'Kehadiran', 'Jumlah Tamu', 'Ucapan']);
        foreach (db_read() as $r) {
            // Cegah formula injection di Excel
            $safe = function ($v) { $v = (string)$v; return preg_match('/^[=+\-@\t\r]/', $v) ? "'" . $v : $v; };
            fputcsv($out, [
                date('d/m/Y H:i', strtotime($r['time'] ?? 'now')),
                $safe($r['name'] ?? ''),
                attendance_label($r['attendance'] ?? ''),
                $r['guests'] ?? 1,
                $safe($r['message'] ?? ''),
            ]);
        }
        fclose($out);
        exit;
    }
}

$csrf = csrf_token();
$rows = $isAdmin ? array_reverse(db_read()) : [];
$stat = ['hadir' => 0, 'ragu' => 0, 'tidak' => 0, 'tamu' => 0];
foreach ($rows as $r) {
    $a = $r['attendance'] ?? '';
    if (isset($stat[$a])) { $stat[$a]++; }
    if ($a === 'hadir') { $stat['tamu'] += (int)($r['guests'] ?? 1); }
}
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$base = $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/index.php';
$defaultPass = ($c['admin_password'] === 'GantiPasswordIni123');
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>Admin Undangan</title>
<style>
    *{box-sizing:border-box}
    body{margin:0;font:15px/1.5 system-ui,-apple-system,'Segoe UI',sans-serif;background:#f3efe6;color:#2f3a33}
    .wrap{max-width:1000px;margin:0 auto;padding:20px}
    h1{font-size:1.4rem;margin:0}
    h2{font-size:1.1rem;margin:0 0 12px}
    .top{display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:18px}
    .card{background:#fff;border-radius:14px;box-shadow:0 4px 18px rgba(0,0,0,.06);padding:18px;margin-bottom:18px}
    .stats{display:grid;grid-template-columns:repeat(4,1fr);gap:10px}
    .stats div{background:#fff;border-radius:12px;padding:14px;text-align:center;box-shadow:0 4px 18px rgba(0,0,0,.06)}
    .stats b{display:block;font-size:1.8rem;color:#3f5846}
    .stats span{font-size:.75rem;color:#6d776f;text-transform:uppercase;letter-spacing:1px}
    input,textarea,button{font:inherit}
    input[type=password],textarea{width:100%;padding:10px 12px;border:1px solid #d9d2c3;border-radius:10px}
    textarea{min-height:110px}
    .btn{display:inline-block;background:#5f7f66;color:#fff;border:0;border-radius:10px;padding:9px 16px;cursor:pointer;text-decoration:none;font-size:.9rem}
    .btn.alt{background:#b8924a}.btn.red{background:#b3402f;padding:5px 10px;font-size:.8rem}.btn.ghost{background:#e4ede5;color:#3f5846}
    table{width:100%;border-collapse:collapse;font-size:.88rem}
    th,td{text-align:left;padding:9px 8px;border-bottom:1px solid #eee;vertical-align:top}
    th{font-size:.72rem;text-transform:uppercase;letter-spacing:1px;color:#6d776f}
    .tbl{overflow-x:auto}
    .badge{font-size:.72rem;padding:2px 9px;border-radius:99px;background:#e4ede5;color:#3f5846;white-space:nowrap}
    .badge.tidak{background:#f3dfda;color:#8a3a2c}.badge.ragu{background:#f4ecd2;color:#8a6a1e}
    .warn{background:#fff4d6;border:1px solid #e8cf86;border-radius:10px;padding:10px 14px;margin-bottom:18px;font-size:.88rem}
    .err{color:#b3402f;margin:8px 0 0}
    .login{max-width:360px;margin:12vh auto}
    .links li{margin-bottom:8px;word-break:break-all}
    .links a{color:#3f5846}
    .row{display:flex;gap:8px;flex-wrap:wrap;align-items:center}
    small{color:#6d776f}
    @media(max-width:600px){.stats{grid-template-columns:repeat(2,1fr)}}
</style>
</head>
<body>
<div class="wrap">

<?php if (!$isAdmin): ?>
    <div class="card login">
        <h1>Admin Undangan</h1>
        <p><small>Masukkan password dari <code>config.php</code>.</small></p>
        <form method="post">
            <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
            <input type="password" name="password" placeholder="Password" autofocus required>
            <?php if ($error): ?><p class="err"><?= e($error) ?></p><?php endif; ?>
            <p><button class="btn" name="login" value="1">Masuk</button></p>
        </form>
    </div>

<?php else: ?>
    <div class="top">
        <h1>Dashboard RSVP</h1>
        <div class="row">
            <a class="btn ghost" href="index.php" target="_blank">Lihat Undangan</a>
            <a class="btn alt" href="admin.php?export=1">Unduh CSV</a>
            <form method="post" style="margin:0"><input type="hidden" name="csrf" value="<?= e($csrf) ?>"><button class="btn" name="logout" value="1">Keluar</button></form>
        </div>
    </div>

    <?php if ($defaultPass): ?>
        <div class="warn"><b>Penting:</b> Anda masih memakai password bawaan. Ubah <code>admin_password</code> di <code>config.php</code> lalu upload ulang.</div>
    <?php endif; ?>

    <div class="stats" style="margin-bottom:18px">
        <div><b><?= $stat['hadir'] ?></b><span>Konfirmasi Hadir</span></div>
        <div><b><?= $stat['tamu'] ?></b><span>Total Tamu Hadir</span></div>
        <div><b><?= $stat['ragu'] ?></b><span>Masih Ragu</span></div>
        <div><b><?= $stat['tidak'] ?></b><span>Tidak Hadir</span></div>
    </div>

    <div class="card">
        <h2>Pembuat Link Tamu</h2>
        <p><small>Tulis satu nama per baris. Tiap tamu mendapat link pribadi sehingga namanya muncul di sampul undangan.</small></p>
        <textarea id="names" placeholder="Budi Santoso&#10;Keluarga Bapak Ahmad&#10;Siti Rahmah"></textarea>
        <p class="row"><button class="btn" type="button" id="gen">Buat Link</button></p>
        <ul class="links" id="out" style="list-style:none;padding:0"></ul>
    </div>

    <div class="card">
        <h2>Daftar Konfirmasi (<?= count($rows) ?>)</h2>
        <div class="tbl">
        <?php if (!$rows): ?>
            <p><small>Belum ada yang mengisi.</small></p>
        <?php else: ?>
            <table>
                <thead><tr><th>Waktu</th><th>Nama</th><th>Status</th><th>Tamu</th><th>Ucapan</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($rows as $r): ?>
                    <tr>
                        <td><?= e(date('d/m/y H:i', strtotime($r['time'] ?? 'now'))) ?></td>
                        <td><b><?= e($r['name'] ?? '') ?></b></td>
                        <td><span class="badge <?= e($r['attendance'] ?? '') ?>"><?= e(attendance_label($r['attendance'] ?? '')) ?></span></td>
                        <td><?= (int)($r['guests'] ?? 1) ?></td>
                        <td><?= nl2br(e($r['message'] ?? '')) ?></td>
                        <td>
                            <form method="post" onsubmit="return confirm('Hapus data ini?')" style="margin:0">
                                <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
                                <button class="btn red" name="delete" value="<?= e($r['id'] ?? '') ?>">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        </div>
    </div>

    <script>
    (function () {
        var base = <?= json_encode($base) ?>;
        var title = <?= json_encode($c['title'], JSON_UNESCAPED_UNICODE) ?>;
        document.getElementById('gen').addEventListener('click', function () {
            var out = document.getElementById('out');
            out.innerHTML = '';
            document.getElementById('names').value.split('\n').forEach(function (n) {
                n = n.trim();
                if (!n) { return; }
                var link = base + '?to=' + encodeURIComponent(n).replace(/%20/g, '+');
                var wa = 'https://wa.me/?text=' + encodeURIComponent(
                    'Kepada Yth. ' + n + '\n\nDengan hormat, kami mengundang Anda ke acara ' + title + '. Detail undangan:\n' + link + '\n\nTerima kasih.');
                var li = document.createElement('li');
                var b = document.createElement('b'); b.textContent = n + ': ';
                var a = document.createElement('a'); a.href = link; a.target = '_blank'; a.textContent = link;
                var w = document.createElement('a'); w.href = wa; w.target = '_blank'; w.textContent = ' [kirim WhatsApp]';
                var cp = document.createElement('button'); cp.className = 'btn ghost'; cp.type = 'button'; cp.textContent = 'Salin'; cp.style.marginLeft = '8px';
                cp.onclick = function () { (navigator.clipboard ? navigator.clipboard.writeText(link) : Promise.reject()).then(function () { cp.textContent = 'Tersalin'; }, function () { prompt('Salin link:', link); }); };
                li.appendChild(b); li.appendChild(a); li.appendChild(w); li.appendChild(cp);
                out.appendChild(li);
            });
        });
    })();
    </script>
<?php endif; ?>

</div>
</body>
</html>
