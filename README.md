# Undangan Pernikahan Online (PHP)

Undangan digital responsif: sampul "Buka Undangan" dengan nama tamu, hitung mundur,
profil mempelai, kisah cinta, detail acara + peta, galeri, amplop digital (salin nomor rekening),
RSVP + buku ucapan, musik latar, dan halaman admin.

**Tanpa database.** Data RSVP disimpan di file `data/rsvp.php`, jadi bisa jalan di hosting gratis
apa pun yang mendukung PHP 7.4+ (disarankan PHP 8).

## 1. Ubah data undangan
Buka **`config.php`** dan ganti semuanya: nama mempelai, tanggal, lokasi, rekening, kisah, dll.
Yang wajib diganti:
- `countdown_target` (tanggal & jam akad/resepsi)
- `admin_password` (JANGAN dibiarkan bawaan)

## 2. Tambah foto & musik (opsional)
| File | Fungsi |
|---|---|
| `assets/img/cover.jpg` | Foto latar sampul & hero (disarankan portrait, 1080x1920) |
| `assets/img/groom.jpg`, `bride.jpg` | Foto mempelai (persegi, 600x600) |
| `assets/img/gallery/1.jpg ... 6.jpg` | Foto galeri (nama bisa diubah di `config.php`) |
| `assets/audio/music.mp3` | Musik latar (buat folder `assets/audio/`) |

Kompres foto dulu (misalnya lewat squoosh.app) agar cepat dibuka, target < 300 KB per foto.
Kalau file tidak ada, undangan tetap tampil dengan placeholder.

## 3. Hosting gratis (contoh: InfinityFree)
1. Daftar di infinityfree.com lalu buat akun hosting (pilih subdomain gratis).
2. Buka **File Manager** (atau pakai FTP/FileZilla), masuk ke folder **`htdocs`**.
3. Upload **semua isi** folder `undangan` ke `htdocs` (bukan folder `undangan`-nya).
4. Buka `https://namaanda.rf.gd` (atau subdomain Anda). Undangan langsung tampil.
5. Pastikan folder **`data/`** dapat ditulis (permission 755; kalau gagal simpan, coba 775/777).

Alternatif lain: Hostinger (paket berbayar murah) atau
hosting cPanel biasa. Netlify, GitHub Pages, dan Vercel **tidak** bisa menjalankan PHP.

## Pilihan tema
Atur di `config.php` pada baris `'theme'`:
- `'minang'` : **tradisional Minangkabau biru & emas** (siluet Rumah Gadang bergonjong, pucuk rebung, belah ketupat songket,
  istilah Baralek / Marapulai / Anak Daro). Warna biru & emas, pituah, dan pantun diatur di blok `'minang'` pada `config.php`.
- `'koran'`  : koran edisi khusus **"Harian Cinta"** (masthead gaya Gotik, headline, kolom berita, foto halftone hitam-putih,
  Pengumuman Resmi, Kupon Konfirmasi, Surat Pembaca). Teks khusus tema ini ada di blok `'koran'` pada `config.php`.
- `'klasik'` : elegan hijau sage & emas.

Foto terbaik untuk tema koran: potret/foto yang kontras. Foto otomatis dibuat hitam-putih bergaya cetak koran.

## 4. Membagikan ke tamu
1. Buka `https://domain-anda/admin.php`, login dengan password di `config.php`.
2. Pada **Pembuat Link Tamu**, tempel daftar nama (satu per baris), klik **Buat Link**.
3. Klik **[kirim WhatsApp]** pada tiap nama. Nama tamu akan tampil di sampul undangan.

Contoh link manual: `https://domain-anda/index.php?to=Budi+Santoso`

## 5. Melihat RSVP
Di `admin.php` ada statistik, daftar konfirmasi + ucapan, tombol hapus, dan **Unduh CSV** (bisa dibuka di Excel).

## Struktur file
```
index.php        halaman undangan (memilih tema)
themes/minang.php tampilan tema Minang
themes/koran.php tampilan tema koran
config.php       SEMUA pengaturan (edit ini)
lib.php          fungsi bantu
api.php          simpan & ambil RSVP/ucapan
admin.php        dashboard admin
data/            tempat data RSVP (jangan dihapus)
assets/css, js   tampilan (style.css = klasik, koran.css, minang.css) & skrip
```

## Keamanan
- Password admin ada di `config.php`: ganti sebelum upload dan jangan bagikan.
- Data RSVP dilindungi dengan `.htaccess` dan header PHP, jadi tidak bisa dibuka langsung lewat browser.
- Form sudah memakai token CSRF, honeypot anti-bot, dan batas kirim; ucapan ditampilkan dengan aman (anti-XSS).
- Cadangkan `data/rsvp.php` secara berkala (unduh lewat File Manager/FTP) atau pakai tombol **Unduh CSV**.
- Gratis hosting kadang menampilkan iklan/pembatasan dari penyedia; itu di luar kendali kode ini.

## Menjalankan di komputer sendiri
```
php -S localhost:8000
```
lalu buka http://localhost:8000
