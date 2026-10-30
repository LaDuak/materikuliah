<?php
if (!function_exists('cfg')) { http_response_code(403); exit; }
/**
 * TEMA "MINANG" - Baralek Minangkabau, biru & emas
 * Motif: siluet Rumah Gadang (atap gonjong), pucuk rebung, belah ketupat songket.
 * Dipanggil dari index.php bila config 'theme' => 'minang'.
 * ID & class elemen interaktif sama dengan tema lain agar assets/js/app.js dipakai bersama.
 */
$c = cfg();
$csrf = csrf_token();

$m = array_merge([
    'blue'   => '#0d2b66',
    'gold'   => '#d9a93f',
    'pituah' => 'Adat basandi syarak, syarak basandi Kitabullah',
    'pantun' => [
        'Pucuk rebung ukiran emas,',
        'Terpahat indah di Rumah Gadang.',
        'Doa restu kami pinta ikhlas,',
        'Sudi kiranya Bapak/Ibu datang.',
    ],
], $c['minang'] ?? []);

function mn_hex($v, string $d): string
{
    return preg_match('/^#[0-9a-fA-F]{6}$/', (string)$v) ? (string)$v : $d;
}
function mn_shade(string $hex, float $f): string
{
    $r = (int)round(hexdec(substr($hex, 1, 2)) * $f);
    $g = (int)round(hexdec(substr($hex, 3, 2)) * $f);
    $b = (int)round(hexdec(substr($hex, 5, 2)) * $f);
    return sprintf('#%02x%02x%02x', min(255, $r), min(255, $g), min(255, $b));
}
function mn_tint(string $hex, float $t): string
{
    $r = hexdec(substr($hex, 1, 2));
    $g = hexdec(substr($hex, 3, 2));
    $b = hexdec(substr($hex, 5, 2));
    return sprintf('#%02x%02x%02x', (int)round($r + (255 - $r) * $t), (int)round($g + (255 - $g) * $t), (int)round($b + (255 - $b) * $t));
}
/** Siluet Rumah Gadang beratap gonjong (warna mengikuti currentColor). */
function mn_roof(string $cls = ''): string
{
    return '<svg class="roof ' . $cls . '" viewBox="0 0 360 150" aria-hidden="true" focusable="false"><g fill="currentColor">'
        . '<path d="M40 20C57 70 73 78 90 78C107 78 123 70 140 20C123 54 107 60 90 60C73 60 57 54 40 20Z"/>'
        . '<path d="M220 20C237 70 253 78 270 78C287 78 303 70 320 20C303 54 287 60 270 60C253 60 237 54 220 20Z"/>'
        . '<path d="M112 4C136 58 158 68 180 68C202 68 224 58 248 4C224 42 202 50 180 50C158 50 136 42 112 4Z"/>'
        . '<path d="M30 76C120 98 240 98 330 76L316 92C240 110 120 110 44 92Z"/>'
        . '<path d="M60 98H300L288 130H72Z"/>'
        . '<path d="M80 130h6v16h-6zM120 130h6v16h-6zM177 130h6v16h-6zM234 130h6v16h-6zM274 130h6v16h-6z"/>'
        . '</g><g fill="var(--blue)"><rect x="86" y="106" width="16" height="14" rx="2"/><rect x="120" y="106" width="16" height="14" rx="2"/>'
        . '<rect x="172" y="106" width="16" height="24" rx="8"/><rect x="224" y="106" width="16" height="14" rx="2"/><rect x="258" y="106" width="16" height="14" rx="2"/></g></svg>';
}

$blue = mn_hex($m['blue'], '#0d2b66');
$gold = mn_hex($m['gold'], '#d9a93f');
$vars = '--blue:' . $blue . ';--blue-d:' . mn_shade($blue, .62) . ';--blue-l:' . mn_tint($blue, .18) . ';--gold:' . $gold . ';--gold-l:' . mn_tint($gold, .38);

$guest = mb_substr(trim((string)($_GET['to'] ?? '')), 0, 60);
if ($guest === '') {
    $guest = 'Tamu Undangan';
}
$g = $c['groom'];
$b = $c['bride'];
$names = $g['nickname'] . ' & ' . $b['nickname'];

$hasCover = !empty($c['cover_photo']) && is_file(__DIR__ . '/../' . $c['cover_photo']);
$hasMusic = !empty($c['music']) && is_file(__DIR__ . '/../' . $c['music']);
$heroStyle = $hasCover ? "style=\"background-image:linear-gradient(rgba(8,28,71,.78),rgba(8,28,71,.92)),url('" . e($c['cover_photo']) . "')\"" : '';

function mn_photo(array $p): string
{
    if (!empty($p['photo']) && is_file(__DIR__ . '/../' . $p['photo'])) {
        return '<img src="' . e($p['photo']) . '" alt="' . e($p['nickname']) . '" loading="lazy">';
    }
    return '<span class="initial">' . e(mb_substr($p['nickname'], 0, 1)) . '</span>';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title><?= e($c['title']) ?></title>
<meta name="description" content="<?= e($c['description']) ?>">
<meta property="og:title" content="<?= e($c['title']) ?>">
<meta property="og:description" content="<?= e($c['description']) ?>">
<meta property="og:type" content="website">
<?php if ($hasCover): ?><meta property="og:image" content="<?= e($c['cover_photo']) ?>"><?php endif; ?>
<meta name="csrf" content="<?= e($csrf) ?>">
<meta name="theme-color" content="<?= e($blue) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700&family=Cinzel+Decorative:wght@700&family=Lora:ital,wght@0,400;0,500;1,400&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/minang.css?v=1">
<style>:root{<?= $vars ?>}</style>
</head>
<body class="locked" data-target="<?= e($c['countdown_target']) ?>">

<!-- ============ SAMPUL ============ -->
<section id="cover" class="cover">
    <div class="cover-frame">
        <?= mn_roof('cover-roof') ?>
        <p class="eyebrow">Salamaik Datang</p>
        <div class="baralek">Baralek</div>
        <div class="names"><?= e($g['nickname']) ?> <span class="amp">&amp;</span> <?= e($b['nickname']) ?></div>
        <p class="cover-date"><?= e($c['date_text']) ?></p>
        <div class="tumpal" aria-hidden="true"></div>
        <div class="guest-card">
            <p>Kepada Yth. Bapak/Ibu/Saudara/i</p>
            <h2><?= e($guest) ?></h2>
            <small>Mohon maaf apabila ada kesalahan penulisan nama / gelar</small>
        </div>
        <button id="openBtn" class="btn" type="button">Buka Undangan</button>
    </div>
</section>

<div class="page">
<main id="main">

<!-- ============ HERO ============ -->
<header id="home" class="hero" <?= $heroStyle ?>>
    <div class="hero-inner reveal">
        <?= mn_roof('hero-roof') ?>
        <p class="eyebrow">Undangan Baralek</p>
        <h1 class="names"><?= e($g['nickname']) ?> <span class="amp">&amp;</span> <?= e($b['nickname']) ?></h1>
        <p class="hero-date"><?= e($c['date_text']) ?></p>
        <?php if (!empty($c['hashtag'])): ?><p class="hashtag"><?= e($c['hashtag']) ?></p><?php endif; ?>
    </div>
    <div class="tumpal tumpal-edge" aria-hidden="true"></div>
</header>

<!-- ============ PEMBUKA ============ -->
<section class="section center">
    <div class="wrap narrow reveal">
        <p class="salam" style="white-space: pre-line;"><?= e($c['greeting']) ?></p>
        <p class="lead" style="white-space: pre-line;"><?= e($c['intro']) ?></p>

        <?php if (!empty($c['quote']['text'])): ?>
        <blockquote class="quote">
            <p>&ldquo;<?= e($c['quote']['text']) ?>&rdquo;</p>
            <cite><?= e($c['quote']['source']) ?></cite>
        </blockquote>
        <?php endif; ?>

        <?php if (!empty($m['pituah'])): ?>
        <div class="pituah"><span class="diamond"></span><p><?= e($m['pituah']) ?></p><span class="diamond"></span></div>
        <?php endif; ?>

        <?php if (!empty($m['pantun']) && is_array($m['pantun'])): ?>
        <div class="pantun">
            <?php foreach ($m['pantun'] as $line): ?><p><?= e($line) ?></p><?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ============ MARAPULAI & ANAK DARO ============ -->
<section id="mempelai" class="section blue center">
    <div class="wrap">
        <div class="tumpal" aria-hidden="true"></div>
        <h2 class="title reveal">Marapulai &amp; Anak Daro</h2>
        <div class="couple">
            <?php foreach ([[$g, 'Marapulai', 'Mempelai Pria'], [$b, 'Anak Daro', 'Mempelai Wanita']] as $i => [$p, $role, $sub]): ?>
            <article class="person reveal">
                <div class="arch"><?= mn_photo($p) ?></div>
                <p class="role"><?= e($role) ?> <small>(<?= e($sub) ?>)</small></p>
                <h3><?= e($p['fullname']) ?></h3>
                <p class="parents"><?= e($p['parents']) ?></p>
                <?php if (!empty($p['instagram'])): ?>
                    <a class="ig" href="https://instagram.com/<?= e(ltrim($p['instagram'], '@')) ?>" target="_blank" rel="noopener">@<?= e(ltrim($p['instagram'], '@')) ?></a>
                <?php endif; ?>
            </article>
            <?php if ($i === 0): ?><div class="couple-amp reveal" aria-hidden="true">&amp;</div><?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============ HITUNG MUNDUR ============ -->
<section class="section center">
    <div class="wrap reveal">
        <h2 class="title">Manunggu Hari Baralek</h2>
        <div class="countdown" id="countdown" aria-live="off">
            <div><b id="cd-d">00</b><span>Hari</span></div>
            <div><b id="cd-h">00</b><span>Jam</span></div>
            <div><b id="cd-m">00</b><span>Menit</span></div>
            <div><b id="cd-s">00</b><span>Detik</span></div>
        </div>
        <p id="cd-done" class="cd-done" hidden>Hari baralek telah tiba. Tarimo kasih atas doa Anda.</p>
        <a class="btn btn-outline" id="saveDate" href="#" target="_blank" rel="noopener">Simpan di Kalender</a>
    </div>
</section>

<!-- ============ TURUT MENGUNDANG (Menggantikan Kisah) ============ -->
<?php if (!empty($c['invited_guests'])): ?>
<section class="section blue center">
    <div class="wrap">
        <div class="tumpal" aria-hidden="true"></div>
        <h2 class="title reveal">Turut Mengundang</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; text-align: left; margin-top: 30px;">
            
            <?php foreach (['ninik_mamak', 'anduang_ungku', 'mande_etek', 'kakak_adiak'] as $key): ?>
                <?php if (isset($c['invited_guests'][$key])): ?>
                    <div class="card reveal" style="padding: 20px; background: rgba(255, 255, 255, 0.08); border: 1px solid var(--gold); border-radius: 12px; backdrop-filter: blur(4px);">
                        <h3 style="font-size: 0.95rem; color: var(--gold-l); font-family: 'Cinzel', serif; margin-bottom: 12px; border-bottom: 1px dashed var(--gold); padding-bottom: 6px; letter-spacing: 1px;">
                            <?= e($c['invited_guests'][$key]['title']) ?>
                        </h3>
                        <ul style="list-style: none; padding: 0; margin: 0; font-size: 0.88rem; color: #fff; line-height: 1.6;">
                            <?php foreach ($c['invited_guests'][$key]['list'] as $item): ?>
                                <li style="margin-bottom: 6px; position: relative; padding-left: 14px;">
                                    <span style="position: absolute; left: 0; color: var(--gold);">-</span>
                                    <?= e($item) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>

        </div>

        <?php if (!empty($c['invited_guests']['footer'])): ?>
            <div class="reveal" style="margin-top: 32px; font-family: 'Lora', serif; font-style: italic; font-size: 0.95rem; color: var(--gold-l);">
                <?= e($c['invited_guests']['footer']) ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<!-- ============ ACARA ============ -->
<section id="acara" class="section center">
    <div class="wrap">
        <h2 class="title reveal">Acara Baralek</h2>
        <div class="events">
            <?php foreach ($c['events'] as $ev): ?>
            <article class="card event reveal">
                <div class="tumpal small" aria-hidden="true"></div>
                <h3><?= e($ev['title']) ?></h3>
                <p class="ev-date"><?= e($ev['date']) ?></p>
                <p class="ev-time"><?= e($ev['time']) ?></p>
                <span class="diamond"></span>
                <p class="ev-place"><?= e($ev['place']) ?></p>
                <p class="ev-addr"><?= e($ev['address']) ?></p>
            </article>
            <?php endforeach; ?>
        </div>
        <?php if (!empty($c['dresscode'])): ?>
            <p class="dresscode reveal"><b>Busana:</b> <?= e($c['dresscode']) ?></p>
        <?php endif; ?>

        <?php if (!empty($c['maps_query'])): ?>
        <div class="map reveal">
            <iframe title="Peta lokasi acara" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                src="https://www.google.com/maps?q=<?= rawurlencode($c['maps_query']) ?>&amp;output=embed"></iframe>
        </div>
        <?php endif; ?>
        <?php if (!empty($c['maps_link'])): ?>
            <a class="btn reveal" href="<?= e($c['maps_link']) ?>" target="_blank" rel="noopener">Buka di Google Maps</a>
        <?php endif; ?>
    </div>
</section>

<!-- ============ GALERI ============ -->
<?php if (!empty($c['gallery'])): ?>
<section id="galeri" class="section blue center">
    <div class="wrap">
        <h2 class="title reveal">Galeri</h2>
        <div class="gallery">
            <?php foreach ($c['gallery'] as $i => $f):
                $path = 'assets/img/gallery/' . $f;
                if (is_file(__DIR__ . '/../' . $path)): ?>
                    <a href="<?= e($path) ?>" class="g-item reveal" data-lightbox><img src="<?= e($path) ?>" alt="Foto <?= $i + 1 ?>" loading="lazy"></a>
                <?php else: ?>
                    <div class="g-item g-ph reveal"><span><?= $i + 1 ?></span></div>
                <?php endif;
            endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============ TANDO MATO ============ -->
<section id="hadiah" class="section center">
    <div class="wrap narrow">
        <h2 class="title reveal">Tando Mato</h2>
        <p class="lead reveal"><?= e($c['gift_text']) ?></p>
        <div class="banks">
            <?php foreach ($c['bank_accounts'] as $bk): ?>
            <div class="card bank reveal">
                <span class="bank-name"><?= e($bk['bank']) ?></span>
                <b class="bank-no"><?= e($bk['number']) ?></b>
                <span class="bank-owner">a.n. <?= e($bk['name']) ?></span>
                <button type="button" class="btn btn-sm copy" data-copy="<?= e($bk['number']) ?>">Salin Nomor</button>
            </div>
            <?php endforeach; ?>
            <?php if (!empty($c['gift_address'])): ?>
            <div class="card bank reveal">
                <span class="bank-name">Kirim Hadiah</span>
                <span class="bank-owner"><?= e($c['gift_address']) ?></span>
                <button type="button" class="btn btn-sm copy" data-copy="<?= e($c['gift_address']) ?>">Salin Alamat</button>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ============ RSVP & UCAPAN ============ -->
<section id="ucapan" class="section blue center">
    <div class="wrap narrow">
        <h2 class="title reveal">Konfirmasi Kehadiran</h2>
        <p class="lead light reveal">Mohon konfirmasi kehadiran dan tinggalkan doa serta ucapan untuk kedua mempelai.</p>

        <form id="rsvpForm" class="card form reveal" autocomplete="off" novalidate>
            <input type="hidden" name="action" value="rsvp">
            <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
            <input type="text" name="website" class="hp" tabindex="-1" autocomplete="off" aria-hidden="true">

            <label>Nama
                <input type="text" name="name" maxlength="60" required value="<?= $guest !== 'Tamu Undangan' ? e($guest) : '' ?>" placeholder="Nama Anda">
            </label>

            <fieldset>
                <legend>Kehadiran</legend>
                <label class="radio"><input type="radio" name="attendance" value="hadir" checked><span>Hadir</span></label>
                <label class="radio"><input type="radio" name="attendance" value="ragu"><span>Masih Ragu</span></label>
                <label class="radio"><input type="radio" name="attendance" value="tidak"><span>Tidak Hadir</span></label>
            </fieldset>

            <label id="guestsWrap">Jumlah Tamu
                <select name="guests">
                    <?php for ($i = 1; $i <= (int)$c['max_guests']; $i++): ?>
                        <option value="<?= $i ?>"><?= $i ?> orang</option>
                    <?php endfor; ?>
                </select>
            </label>

            <label>Ucapan &amp; Doa
                <textarea name="message" rows="4" maxlength="500" placeholder="Tulis ucapan untuk kedua mempelai..."></textarea>
            </label>

            <button type="submit" class="btn" id="rsvpBtn">Kirim</button>
            <p id="rsvpMsg" class="form-msg" role="status" aria-live="polite"></p>
        </form>

        <div class="stats reveal" id="stats" hidden>
            <div><b id="st-hadir">0</b><span>Hadir</span></div>
            <div><b id="st-ragu">0</b><span>Ragu</span></div>
            <div><b id="st-tidak">0</b><span>Berhalangan</span></div>
        </div>

        <div class="wishes" id="wishes" aria-live="polite">
            <p class="muted">Memuat ucapan...</p>
        </div>
    </div>
</section>

<!-- ============ PENUTUP ============ -->
<section class="closing center">
    <div class="wrap narrow reveal">
        <?= mn_roof('closing-roof') ?>
        <h2 class="title">Tarimo Kasih</h2>
        <p class="lead light"><?= e($c['closing']) ?></p>
        <p class="salam light"><?= e($c['closing_greeting']) ?></p>

        <?php if (is_array($c['families'])): ?>
            <p style="margin-top:20px; font-weight: 600; color: var(--gold-l); font-size: 0.95rem;">
                <?= e($c['families']['header'] ?? 'Hormat Kami Yang Menanti') ?>
            </p>
            <div style="display: flex; justify-content: space-around; gap: 20px; margin-top: 15px; flex-wrap: wrap;">
                <div>
                    <p style="white-space: pre-line; font-size: 0.88rem; color: rgba(255,255,255,0.9);"><?= e($c['families']['groom_family'] ?? '') ?></p>
                </div>
                <div>
                    <p style="white-space: pre-line; font-size: 0.88rem; color: rgba(255,255,255,0.9);"><?= e($c['families']['bride_family'] ?? '') ?></p>
                </div>
            </div>
        <?php else: ?>
            <p class="families"><?= e($c['families']) ?></p>
        <?php endif; ?>

        <div class="names small" style="margin-top: 25px;"><?= e($g['nickname']) ?> <span class="amp">&amp;</span> <?= e($b['nickname']) ?></div>
    </div>
    <div class="tumpal" aria-hidden="true"></div>
    <footer class="foot">Sponsor by Laduak &middot; <?= date('Y') ?></footer>
</section>

</main>
</div>

<!-- ============ NAV BAWAH ============ -->
<nav class="bottom-nav" id="bottomNav" aria-label="Navigasi">
    <a href="#home">Home</a>
    <a href="#mempelai">Mempelai</a>
    <a href="#acara">Acara</a>
    <a href="#galeri">Galeri</a>
    <a href="#ucapan">Ucapan</a>
</nav>

<?php if ($hasMusic): ?>
<audio id="bgm" src="<?= e($c['music']) ?>" loop preload="none"></audio>
<button id="musicBtn" class="music-btn" type="button" aria-label="Putar / jeda musik" hidden>
    <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true"><path d="M12 3v10.55A4 4 0 1 0 14 17V7h4V3h-6z"/></svg>
</button>
<?php endif; ?>

<div class="lightbox" id="lightbox" hidden><img alt=""><button type="button" aria-label="Tutup">&times;</button></div>
<div class="toast" id="toast" role="status"></div>

<script>
window.INV = {
    title: <?= json_encode($c['title'], JSON_UNESCAPED_UNICODE) ?>,
    place: <?= json_encode($c['events'][0]['place'] ?? '', JSON_UNESCAPED_UNICODE) ?>,
    address: <?= json_encode($c['events'][0]['address'] ?? '', JSON_UNESCAPED_UNICODE) ?>
};
</script>
<script src="assets/js/app.js?v=1" defer></script>
</body>
</html>