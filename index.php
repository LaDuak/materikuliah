<?php
require __DIR__ . '/lib.php';
$c = cfg();
$csrf = csrf_token();

// Pilih tema (lihat config.php -> 'theme')
$theme = (string)($c['theme'] ?? 'klasik');
if (in_array($theme, ['koran', 'minang'], true)) {
    require __DIR__ . '/themes/' . $theme . '.php';
    exit;
}

// Nama tamu dari link: index.php?to=Budi+Santoso
$guest = trim((string)($_GET['to'] ?? ''));
$guest = mb_substr($guest, 0, 60);
if ($guest === '') {
    $guest = 'Tamu Undangan';
}

$hasCover = !empty($c['cover_photo']) && is_file(__DIR__ . '/' . $c['cover_photo']);
$hasMusic = !empty($c['music']) && is_file(__DIR__ . '/' . $c['music']);
$coverStyle = $hasCover ? "style=\"background-image:linear-gradient(rgba(30,40,34,.55),rgba(30,40,34,.75)),url('" . e($c['cover_photo']) . "')\"" : '';

function photo_or_initial(array $p): string
{
    if (!empty($p['photo']) && is_file(__DIR__ . '/' . $p['photo'])) {
        return '<img src="' . e($p['photo']) . '" alt="' . e($p['nickname']) . '" loading="lazy">';
    }
    return '<span class="initial">' . e(mb_substr($p['nickname'], 0, 1)) . '</span>';
}

$orn = '<div class="ornament" aria-hidden="true"><svg viewBox="0 0 200 20" width="160" height="16"><path d="M0 10h78M122 10h78" stroke="currentColor" stroke-width="1" opacity=".5"/><path d="M100 2c3 4 3 12 0 16-3-4-3-12 0-16zM88 10c4-3 8-3 12 0-4 3-8 3-12 0zM100 10c4-3 8-3 12 0-4 3-8 3-12 0z" fill="currentColor"/></svg></div>';
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
<meta name="theme-color" content="#4f6b55">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,500&family=Great+Vibes&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css?v=1">
</head>
<body class="locked" data-target="<?= e($c['countdown_target']) ?>">

<!-- ============ COVER ============ -->
<section id="cover" class="cover" <?= $coverStyle ?>>
    <div class="cover-inner">
        <p class="eyebrow">The Wedding of</p>
        <h1 class="names script"><?= e($c['groom']['nickname']) ?> <span class="amp">&amp;</span> <?= e($c['bride']['nickname']) ?></h1>
        <p class="cover-date"><?= e($c['date_text']) ?></p>
        <div class="guest-box">
            <p>Kepada Yth. Bapak/Ibu/Saudara/i</p>
            <h2><?= e($guest) ?></h2>
            <small>Mohon maaf apabila ada kesalahan penulisan nama / gelar</small>
        </div>
        <button id="openBtn" class="btn btn-gold" type="button">Buka Undangan</button>
    </div>
</section>

<main id="main">

<!-- ============ HERO ============ -->
<section id="home" class="hero" <?= $coverStyle ?>>
    <div class="hero-inner reveal">
        <p class="eyebrow">Kami Menikah</p>
        <h1 class="names script"><?= e($c['groom']['nickname']) ?> <span class="amp">&amp;</span> <?= e($c['bride']['nickname']) ?></h1>
        <p class="hero-date"><?= e($c['date_text']) ?></p>
        <?php if (!empty($c['hashtag'])): ?><p class="hashtag"><?= e($c['hashtag']) ?></p><?php endif; ?>
        <div class="scroll-hint" aria-hidden="true"></div>
    </div>
</section>

<!-- ============ PEMBUKA & AYAT ============ -->
<section class="section center">
    <div class="wrap narrow reveal">
        <?= $orn ?>
        <p class="salam" style="white-space: pre-line;"><?= e($c['greeting']) ?></p>
        <p class="lead" style="white-space: pre-line;"><?= e($c['intro']) ?></p>
        <?php if (!empty($c['quote']['text'])): ?>
        <blockquote class="quote">
            <p>&ldquo;<?= e($c['quote']['text']) ?>&rdquo;</p>
            <cite><?= e($c['quote']['source']) ?></cite>
        </blockquote>
        <?php endif; ?>
    </div>
</section>

<!-- ============ MEMPELAI ============ -->
<section id="mempelai" class="section alt center">
    <div class="wrap">
        <h2 class="title reveal">Mempelai</h2>
        <div class="couple">
            <?php foreach ([['groom', $c['groom']], ['bride', $c['bride']]] as $i => [$k, $p]): ?>
            <article class="person reveal">
                <div class="avatar"><?= photo_or_initial($p) ?></div>
                <h3 class="script"><?= e($p['fullname']) ?></h3>
                <p class="parents"><?= e($p['parents']) ?></p>
                <?php if (!empty($p['instagram'])): ?>
                    <a class="ig" href="https://instagram.com/<?= e(ltrim($p['instagram'], '@')) ?>" target="_blank" rel="noopener">@<?= e(ltrim($p['instagram'], '@')) ?></a>
                <?php endif; ?>
            </article>
            <?php if ($i === 0): ?><div class="couple-amp script reveal" aria-hidden="true">&amp;</div><?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============ HITUNG MUNDUR ============ -->
<section class="section countdown-sec center">
    <div class="wrap reveal">
        <h2 class="title light">Menghitung Hari</h2>
        <div class="countdown" id="countdown" aria-live="off">
            <div><b id="cd-d">00</b><span>Hari</span></div>
            <div><b id="cd-h">00</b><span>Jam</span></div>
            <div><b id="cd-m">00</b><span>Menit</span></div>
            <div><b id="cd-s">00</b><span>Detik</span></div>
        </div>
        <p id="cd-done" class="cd-done" hidden>Hari bahagia telah tiba. Terima kasih atas doa Anda.</p>
        <a class="btn btn-outline-light" id="saveDate" href="#" target="_blank" rel="noopener">Simpan di Google Calendar</a>
    </div>
</section>

<!-- ============ TURUT MENGUNDANG ============ -->
<?php if (!empty($c['invited_guests'])): ?>
<section class="section alt center">
    <div class="wrap">
        <h2 class="title reveal">Turut Mengundang</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; text-align: left; margin-top: 30px;">
            
            <?php foreach (['ninik_mamak', 'anduang_ungku', 'mande_etek', 'kakak_adiak'] as $key): ?>
                <?php if (isset($c['invited_guests'][$key])): ?>
                    <div class="card reveal" style="padding: 20px; border-radius: 12px;">
                        <h3 style="font-size: 0.95rem; color: var(--sage-dark); margin-bottom: 12px; border-bottom: 1px dashed var(--sage); padding-bottom: 6px; letter-spacing: 1px;">
                            <?= e($c['invited_guests'][$key]['title']) ?>
                        </h3>
                        <ul style="list-style: none; padding: 0; margin: 0; font-size: 0.88rem; color: var(--ink); line-height: 1.6;">
                            <?php foreach ($c['invited_guests'][$key]['list'] as $item): ?>
                                <li style="margin-bottom: 6px; position: relative; padding-left: 14px;">
                                    <span style="position: absolute; left: 0; color: var(--sage);">-</span>
                                    <?= e($item) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>

        </div>

        <?php if (!empty($c['invited_guests']['footer'])): ?>
            <div class="reveal" style="margin-top: 32px; font-style: italic; font-size: 0.95rem; color: var(--sage-dark);">
                <?= e($c['invited_guests']['footer']) ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<!-- ============ ACARA ============ -->
<section id="acara" class="section center">
    <div class="wrap">
        <h2 class="title reveal">Detail Acara</h2>
        <div class="events">
            <?php foreach ($c['events'] as $ev): ?>
            <article class="card event reveal">
                <h3 class="script"><?= e($ev['title']) ?></h3>
                <p class="ev-date"><?= e($ev['date']) ?></p>
                <p class="ev-time"><?= e($ev['time']) ?></p>
                <hr>
                <p class="ev-place"><?= e($ev['place']) ?></p>
                <p class="ev-addr"><?= e($ev['address']) ?></p>
            </article>
            <?php endforeach; ?>
        </div>
        <?php if (!empty($c['dresscode'])): ?>
            <p class="dresscode reveal"><b>Dress code:</b> <?= e($c['dresscode']) ?></p>
        <?php endif; ?>

        <?php if (!empty($c['maps_query'])): ?>
        <div class="map reveal">
            <iframe title="Peta lokasi acara" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                src="https://www.google.com/maps?q=<?= rawurlencode($c['maps_query']) ?>&amp;output=embed"></iframe>
        </div>
        <?php endif; ?>
        <?php if (!empty($c['maps_link'])): ?>
            <a class="btn btn-sage reveal" href="<?= e($c['maps_link']) ?>" target="_blank" rel="noopener">Buka di Google Maps</a>
        <?php endif; ?>
    </div>
</section>

<!-- ============ GALERI ============ -->
<?php if (!empty($c['gallery'])): ?>
<section id="galeri" class="section alt center">
    <div class="wrap">
        <h2 class="title reveal">Galeri</h2>
        <div class="gallery">
            <?php foreach ($c['gallery'] as $i => $f):
                $path = 'assets/img/gallery/' . $f;
                if (is_file(__DIR__ . '/' . $path)): ?>
                    <a href="<?= e($path) ?>" class="g-item reveal" data-lightbox><img src="<?= e($path) ?>" alt="Foto <?= $i + 1 ?>" loading="lazy"></a>
                <?php else: ?>
                    <div class="g-item g-ph reveal"><span><?= $i + 1 ?></span></div>
                <?php endif;
            endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============ HADIAH ============ -->
<section id="hadiah" class="section center">
    <div class="wrap narrow">
        <h2 class="title reveal">Tanda Kasih</h2>
        <p class="lead reveal"><?= e($c['gift_text']) ?></p>
        <div class="banks">
            <?php foreach ($c['bank_accounts'] as $b): ?>
            <div class="card bank reveal">
                <span class="bank-name"><?= e($b['bank']) ?></span>
                <b class="bank-no"><?= e($b['number']) ?></b>
                <span class="bank-owner">a.n. <?= e($b['name']) ?></span>
                <button type="button" class="btn btn-sage btn-sm copy" data-copy="<?= e($b['number']) ?>">Salin Nomor</button>
            </div>
            <?php endforeach; ?>
            <?php if (!empty($c['gift_address'])): ?>
            <div class="card bank reveal">
                <span class="bank-name">Kirim Hadiah</span>
                <span class="bank-addr"><?= e($c['gift_address']) ?></span>
                <button type="button" class="btn btn-sage btn-sm copy" data-copy="<?= e($c['gift_address']) ?>">Salin Alamat</button>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ============ RSVP & UCAPAN ============ -->
<section id="ucapan" class="section alt center">
    <div class="wrap narrow">
        <h2 class="title reveal">Konfirmasi Kehadiran</h2>
        <p class="lead reveal">Mohon konfirmasi kehadiran dan tinggalkan doa serta ucapan untuk kami.</p>

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

            <button type="submit" class="btn btn-gold" id="rsvpBtn">Kirim</button>
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
        <?= $orn ?>
        <p class="lead light"><?= e($c['closing']) ?></p>
        <p class="salam light"><?= e($c['closing_greeting']) ?></p>
        <h2 class="names script small"><?= e($c['groom']['nickname']) ?> <span class="amp">&amp;</span> <?= e($c['bride']['nickname']) ?></h2>
        
        <?php if (is_array($c['families'])): ?>
            <p style="margin-top:20px; font-weight: 600; color: #fff; font-size: 0.95rem;">
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
            <p class="light"><?= e($c['families']) ?></p>
        <?php endif; ?>
    </div>
    <footer class="foot">Sponsor by Laduak &middot; <?= date('Y') ?></footer>
</section>

</main>

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
    <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor" aria-hidden="true"><path d="M12 3v10.55A4 4 0 1 0 14 17V7h4V3h-6z"/></svg>
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