<?php
if (!function_exists('cfg')) { http_response_code(403); exit; }
/**
 * TEMA "KORAN" - Harian Cinta (edisi khusus pernikahan)
 * Dipanggil dari index.php bila config 'theme' => 'koran'.
 * ID & class elemen interaktif sengaja sama dengan tema klasik agar assets/js/app.js bisa dipakai bersama.
 */
$c = cfg();
$csrf = csrf_token();

$k = array_merge([
    'paper'    => 'Harian Cinta',
    'tagline'  => 'Terbit sekali seumur hidup',
    'edition'  => 'Edisi Khusus No. 001',
    'price'    => 'Harga: Doa Restu',
    'city'     => '',
    'headline' => '',
    'deck'     => 'Dua keluarga berbahagia, satu hari yang dinanti. Seluruh kerabat dan sahabat diundang untuk menjadi saksi.',
    'ticker'   => '',
    'footnote' => 'Redaksi tidak bertanggung jawab atas rasa bahagia yang berlebihan setelah membaca edisi ini.',
], $c['koran'] ?? []);

$guest = mb_substr(trim((string)($_GET['to'] ?? '')), 0, 60);
if ($guest === '') {
    $guest = 'Tamu Undangan';
}

$g = $c['groom'];
$b = $c['bride'];
$names = $g['nickname'] . ' & ' . $b['nickname'];
$headline = $k['headline'] !== '' ? $k['headline'] : mb_strtoupper($g['nickname'] . ' & ' . $b['nickname']) . ' RESMI MENIKAH';
$ticker = $k['ticker'] !== '' ? $k['ticker'] : ('KABAR BAHAGIA: ' . mb_strtoupper($names) . ' MENIKAH PADA ' . mb_strtoupper($c['date_text']) . ' // ' . ($c['hashtag'] ?? '') . ' // MOHON DOA RESTU ANDA');
$dateline = trim(($k['city'] !== '' ? mb_strtoupper($k['city']) . ', ' : '') . mb_strtoupper($c['date_text']));

$hasCover = !empty($c['cover_photo']) && is_file(__DIR__ . '/../' . $c['cover_photo']);
$hasMusic = !empty($c['music']) && is_file(__DIR__ . '/../' . $c['music']);

function kor_photo(array $p, string $label): string
{
    if (!empty($p['photo']) && is_file(__DIR__ . '/../' . $p['photo'])) {
        return '<img src="' . e($p['photo']) . '" alt="' . e($p['nickname']) . '" loading="lazy">';
    }
    return '<div class="ph"><span>' . e($label) . '</span></div>';
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
<meta name="theme-color" content="#1c1a17">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=UnifrakturCook:wght@700&family=Playfair+Display:ital,wght@0,700;0,900;1,500&family=Newsreader:ital,opsz,wght@0,6..72,400;0,6..72,600;1,6..72,400&family=Special+Elite&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/koran.css?v=1">
</head>
<body class="locked" data-target="<?= e($c['countdown_target']) ?>">

<!-- ============ SAMPUL (halaman depan terlipat) ============ -->
<section id="cover" class="cover">
    <div class="cover-sheet">
        <div class="mast-top"><span><?= e($k['edition']) ?></span><span><?= e($k['price']) ?></span></div>
        <div class="mast-title"><?= e($k['paper']) ?></div>
        <div class="mast-bar"><span><?= e($dateline) ?></span></div>

        <p class="cover-kicker">Eksemplar khusus untuk</p>
        <div class="stamp">
            <small>Kepada Yth. Bapak/Ibu/Saudara/i</small>
            <b><?= e($guest) ?></b>
        </div>
        <p class="cover-head"><?= e($headline) ?></p>
        <button id="openBtn" class="btn" type="button">Buka Edisi Ini</button>
        <p class="cover-note">Mohon maaf apabila ada kesalahan penulisan nama / gelar</p>
    </div>
</section>

<div class="paper">
<main id="main">

<!-- ============ HALAMAN DEPAN ============ -->
<header id="home" class="front">
    <div class="mast-top"><span><?= e($k['edition']) ?></span><span><?= e($k['tagline']) ?></span><span><?= e($k['price']) ?></span></div>
    <h2 class="mast-title"><?= e($k['paper']) ?></h2>
    <div class="mast-bar"><span>Tahun <?= date('Y') ?></span><span><?= e($dateline) ?></span><span>Halaman 1</span></div>

    <div class="ticker" aria-hidden="true"><div class="ticker-track"><span><?= e($ticker) ?></span><span><?= e($ticker) ?></span></div></div>

    <div class="reveal">
        <p class="kicker">Kabar Gembira &middot; Utama</p>
        <h1 class="headline"><?= e($headline) ?></h1>
        <p class="deck"><?= e($k['deck']) ?></p>
        <p class="byline">Oleh Redaksi &mdash; dengan restu kedua keluarga</p>
    </div>

    <figure class="lead-photo reveal">
        <div class="halftone">
        <?php if ($hasCover): ?>
            <img src="<?= e($c['cover_photo']) ?>" alt="Foto utama <?= e($names) ?>">
        <?php else: ?>
            <div class="ph ph-wide"><span>Foto Utama</span></div>
        <?php endif; ?>
        </div>
        <figcaption>Foto: <?= e($names) ?>, Dok. Pribadi.</figcaption>
    </figure>
</header>

<!-- ============ KABAR GEMBIRA ============ -->
<section class="sec">
    <div class="sec-head reveal"><span class="kicker">Liputan Utama</span><h2 class="sec-title">Kabar Gembira</h2></div>
    <div class="cols reveal">
        <p class="dropcap"><?= e($c['intro']) ?> <?= e($g['fullname']) ?> (<?= e($g['parents']) ?>) dan <?= e($b['fullname']) ?> (<?= e($b['parents']) ?>) akan melangsungkan pernikahan pada <?= e($c['date_text']) ?>.</p>
        <p><?= e($c['closing']) ?></p>
        <?php if (!empty($c['quote']['text'])): ?>
        <blockquote class="pull">
            &ldquo;<?= e($c['quote']['text']) ?>&rdquo;
            <cite>&mdash; <?= e($c['quote']['source']) ?></cite>
        </blockquote>
        <?php endif; ?>
    </div>
    <p class="salam reveal" style="white-space: pre-line; text-align: center; margin-top: 15px; font-weight: bold;"><?= e($c['greeting']) ?></p>
</section>

<!-- ============ TOKOH UTAMA ============ -->
<section id="mempelai" class="sec">
    <div class="sec-head reveal"><span class="kicker">Profil</span><h2 class="sec-title">Tokoh Utama</h2></div>
    <div class="profiles">
        <?php foreach ([[$g, 'Mempelai Pria'], [$b, 'Mempelai Wanita']] as [$p, $role]): ?>
        <article class="profile reveal">
            <div class="profile-photo halftone"><?= kor_photo($p, $p['nickname']) ?></div>
            <p class="role"><?= e($role) ?></p>
            <h3><?= e($p['fullname']) ?></h3>
            <p class="parents"><?= e($p['parents']) ?></p>
            <?php if (!empty($p['instagram'])): ?>
                <a class="ig" href="https://instagram.com/<?= e(ltrim($p['instagram'], '@')) ?>" target="_blank" rel="noopener">@<?= e(ltrim($p['instagram'], '@')) ?></a>
            <?php endif; ?>
        </article>
        <?php endforeach; ?>
    </div>
</section>

<!-- ============ HITUNG MUNDUR ============ -->
<section class="dark-block reveal">
    <p class="kicker light">Edisi Berikutnya (Hari H) Terbit Dalam</p>
    <div class="countdown" id="countdown" aria-live="off">
        <div><b id="cd-d">00</b><span>Hari</span></div>
        <div><b id="cd-h">00</b><span>Jam</span></div>
        <div><b id="cd-m">00</b><span>Menit</span></div>
        <div><b id="cd-s">00</b><span>Detik</span></div>
    </div>
    <p id="cd-done" class="cd-done" hidden>Hari yang dinanti telah terbit. Terima kasih atas doa Anda.</p>
    <a class="btn btn-light" id="saveDate" href="#" target="_blank" rel="noopener">Catat di Kalender</a>
</section>

<!-- ============ TURUT MENGUNDANG (Rubrik Liputan Khusus) ============ -->
<?php if (!empty($c['invited_guests'])): ?>
<section class="sec">
    <div class="sec-head reveal"><span class="kicker">Laporan Khusus</span><h2 class="sec-title">Turut Mengundang</h2></div>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; text-align: left; margin-top: 20px;">
        
        <?php foreach (['ninik_mamak', 'anduang_ungku', 'mande_etek', 'kakak_adiak'] as $key): ?>
            <?php if (isset($c['invited_guests'][$key])): ?>
                <div class="reveal" style="border: 1px solid #1c1a17; padding: 15px; background: #fffdf9;">
                    <h3 style="font-family: 'Playfair Display', serif; font-size: 0.95rem; font-weight: bold; border-bottom: 2px solid #1c1a17; padding-bottom: 5px; margin-bottom: 10px; text-transform: uppercase;">
                        <?= e($c['invited_guests'][$key]['title']) ?>
                    </h3>
                    <ul style="list-style: none; padding: 0; margin: 0; font-size: 0.85rem; line-height: 1.5;">
                        <?php foreach ($c['invited_guests'][$key]['list'] as $item): ?>
                            <li style="margin-bottom: 4px; padding-left: 12px; position: relative;">
                                <span style="position: absolute; left: 0;">&bull;</span>
                                <?= e($item) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

    </div>

    <?php if (!empty($c['invited_guests']['footer'])): ?>
        <p class="reveal" style="text-align: center; margin-top: 20px; font-weight: bold; font-size: 0.9rem; font-style: italic;">
            <?= e($c['invited_guests']['footer']) ?>
        </p>
    <?php endif; ?>
</section>
<?php endif; ?>

<!-- ============ PENGUMUMAN RESMI ============ -->
<section id="acara" class="sec">
    <div class="sec-head reveal"><span class="kicker">Iklan Layanan Keluarga</span><h2 class="sec-title">Pengumuman Resmi</h2></div>
    <div class="notices">
        <?php foreach ($c['events'] as $ev): ?>
        <article class="notice reveal">
            <h3><?= e($ev['title']) ?></h3>
            <dl>
                <div><dt>Hari/Tanggal</dt><dd><?= e($ev['date']) ?></dd></div>
                <div><dt>Pukul</dt><dd><?= e($ev['time']) ?></dd></div>
                <div><dt>Tempat</dt><dd><?= e($ev['place']) ?></dd></div>
                <div><dt>Alamat</dt><dd><?= e($ev['address']) ?></dd></div>
            </dl>
        </article>
        <?php endforeach; ?>
    </div>
    <?php if (!empty($c['dresscode'])): ?>
        <p class="dresscode reveal"><b>Catatan Redaksi &mdash; Busana:</b> <?= e($c['dresscode']) ?></p>
    <?php endif; ?>

    <?php if (!empty($c['maps_query'])): ?>
    <figure class="map reveal">
        <iframe title="Peta lokasi acara" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
            src="https://www.google.com/maps?q=<?= rawurlencode($c['maps_query']) ?>&amp;output=embed"></iframe>
        <figcaption>Peta lokasi acara.</figcaption>
    </figure>
    <?php endif; ?>
    <?php if (!empty($c['maps_link'])): ?>
        <p class="center"><a class="btn reveal" href="<?= e($c['maps_link']) ?>" target="_blank" rel="noopener">Buka Rute di Google Maps</a></p>
    <?php endif; ?>
</section>

<!-- ============ FOTO LIPUTAN ============ -->
<?php if (!empty($c['gallery'])): ?>
<section id="galeri" class="sec">
    <div class="sec-head reveal"><span class="kicker">Dokumentasi</span><h2 class="sec-title">Foto Liputan</h2></div>
    <div class="gallery">
        <?php foreach ($c['gallery'] as $i => $f):
            $path = 'assets/img/gallery/' . $f;
            $has = is_file(__DIR__ . '/../' . $path); ?>
            <figure class="g-item reveal">
                <?php if ($has): ?>
                    <a href="<?= e($path) ?>" class="halftone" data-lightbox><img src="<?= e($path) ?>" alt="Foto <?= $i + 1 ?>" loading="lazy"></a>
                <?php else: ?>
                    <div class="ph"><span>Foto <?= $i + 1 ?></span></div>
                <?php endif; ?>
                <figcaption>Gambar <?= $i + 1 ?>: Dok. <?= e($g['nickname']) ?> &amp; <?= e($b['nickname']) ?>.</figcaption>
            </figure>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- ============ IKLAN BARIS / TANDA KASIH ============ -->
<section id="hadiah" class="sec">
    <div class="sec-head reveal"><span class="kicker">Iklan Baris</span><h2 class="sec-title">Tanda Kasih</h2></div>
    <p class="lead reveal"><?= e($c['gift_text']) ?></p>
    <div class="ads">
        <?php foreach ($c['bank_accounts'] as $bk): ?>
        <div class="ad reveal">
            <span class="ad-tag"><?= e($bk['bank']) ?></span>
            <b class="ad-no"><?= e($bk['number']) ?></b>
            <span class="ad-owner">a.n. <?= e($bk['name']) ?></span>
            <button type="button" class="btn btn-sm copy" data-copy="<?= e($bk['number']) ?>">Salin Nomor</button>
        </div>
        <?php endforeach; ?>
        <?php if (!empty($c['gift_address'])): ?>
        <div class="ad reveal">
            <span class="ad-tag">Kirim Kado</span>
            <span class="ad-owner"><?= e($c['gift_address']) ?></span>
            <button type="button" class="btn btn-sm copy" data-copy="<?= e($c['gift_address']) ?>">Salin Alamat</button>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ============ KUPON KONFIRMASI & SURAT PEMBACA ============ -->
<section id="ucapan" class="sec">
    <div class="sec-head reveal"><span class="kicker">Partisipasi Pembaca</span><h2 class="sec-title">Kupon Konfirmasi</h2></div>
    <p class="lead reveal">Isi kupon di bawah ini, lalu kirim ke redaksi. Doa dan ucapan Anda akan dimuat di rubrik Surat Pembaca.</p>

    <div class="cut-line" aria-hidden="true"><span>- - - gunting di sini - - - - -</span></div>
    <form id="rsvpForm" class="coupon reveal" autocomplete="off" novalidate>
        <input type="hidden" name="action" value="rsvp">
        <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
        <input type="text" name="website" class="hp" tabindex="-1" autocomplete="off" aria-hidden="true">

        <label>Nama Lengkap
            <input type="text" name="name" maxlength="60" required value="<?= $guest !== 'Tamu Undangan' ? e($guest) : '' ?>" placeholder="Tulis nama Anda">
        </label>

        <fieldset>
            <legend>Kehadiran (pilih satu)</legend>
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
            <textarea name="message" rows="4" maxlength="500" placeholder="Tulis surat untuk kedua mempelai..."></textarea>
        </label>

        <button type="submit" class="btn" id="rsvpBtn">Kirim ke Redaksi</button>
        <p id="rsvpMsg" class="form-msg" role="status" aria-live="polite"></p>
    </form>

    <div class="stats reveal" id="stats" hidden>
        <div><b id="st-hadir">0</b><span>Hadir</span></div>
        <div><b id="st-ragu">0</b><span>Ragu</span></div>
        <div><b id="st-tidak">0</b><span>Berhalangan</span></div>
    </div>

    <div class="sec-head sub reveal"><span class="kicker">Rubrik</span><h2 class="sec-title small">Surat Pembaca</h2></div>
    <div class="wishes" id="wishes" aria-live="polite">
        <p class="muted">Memuat surat...</p>
    </div>
</section>

<!-- ============ REDAKSI ============ -->
<footer class="redaksi reveal">
    <h2 class="mast-title small"><?= e($k['paper']) ?></h2>
    <p class="salam"><?= e($c['closing_greeting']) ?></p>
    
    <?php if (is_array($c['families'])): ?>
        <p style="margin-top: 10px; font-weight: bold; font-size: 0.95rem;">
            <?= e($c['families']['header'] ?? 'Hormat Kami Yang Menanti') ?>
        </p>
        <div style="display: flex; justify-content: space-around; gap: 20px; margin: 15px 0; flex-wrap: wrap; text-align: center;">
            <div>
                <p style="white-space: pre-line; font-size: 0.85rem; margin: 0;"><?= e($c['families']['groom_family'] ?? '') ?></p>
            </div>
            <div>
                <p style="white-space: pre-line; font-size: 0.85rem; margin: 0;"><?= e($c['families']['bride_family'] ?? '') ?></p>
            </div>
        </div>
    <?php else: ?>
        <p><?= e($c['families']) ?></p>
    <?php endif; ?>

    <p class="fine"><?= e($k['footnote']) ?></p>
    <p class="fine">&copy; <?= date('Y') ?> Redaksi <?= e($k['paper']) ?> &middot; <?= e($c['hashtag'] ?? '') ?></p>
</footer>

</main>
</div>

<!-- ============ NAV BAWAH ============ -->
<nav class="bottom-nav" id="bottomNav" aria-label="Navigasi">
    <a href="#home">Depan</a>
    <a href="#mempelai">Tokoh</a>
    <a href="#acara">Acara</a>
    <a href="#galeri">Foto</a>
    <a href="#ucapan">Surat</a>
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