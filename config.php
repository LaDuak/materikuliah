<?php
/**
 * ====================================================
 *  PENGATURAN UNDANGAN  -  EDIT FILE INI SAJA
 * ====================================================
 * Semua teks, tanggal, lokasi, dan rekening ada di sini.
 * Tidak perlu mengubah index.php.
 */
return [

    // ---------- Umum ----------
    'title'       => 'The Wedding of Jenggo & Aura',
    'description' => 'Kami mengundang Anda untuk hadir di hari bahagia kami.',
    'hashtag'     => '#JenggoAuraForever',

    // ---------- Pilihan tema ----------
    //  'minang' = tradisional Minangkabau, biru & emas (Rumah Gadang, pucuk rebung)
    //  'koran'  = koran edisi khusus "Harian Cinta" (tampilan unik)
    //  'klasik' = elegan hijau sage & emas
    'theme' => 'minang',

    // Pengaturan khusus tema minang
    'minang' => [
        'blue'   => '#0d2b66',   // warna biru utama
        'gold'   => '#d9a93f',   // warna emas
        'pituah' => 'Adat basandi syarak, syarak basandi Kitabullah',
        'pantun' => [
            'Pucuk rebung ukiran emas,',
            'Terpahat indah di Rumah Gadang.',
            'Doa restu kami pinta ikhlas,',
            'Sudi kiranya Bapak/Ibu datang.',
        ],
    ],

    // Pengaturan khusus tema koran
    'koran' => [
        'paper'    => 'Harian Cinta',
        'tagline'  => 'Terbit sekali seumur hidup',
        'edition'  => 'Edisi Khusus No. 001',
        'price'    => 'Harga: Doa Restu',
        'city'     => 'Padang',
        'headline' => '',
        'deck'     => 'Dua keluarga berbahagia, satu hari yang dinanti. Seluruh kerabat dan sahabat diundang untuk menjadi saksi.',
        'ticker'   => '',
        'footnote' => 'Redaksi tidak bertanggung jawab atas rasa bahagia yang berlebihan setelah membaca edisi ini.',
    ],

    // Tanggal & jam acara utama (untuk hitung mundur).
    'countdown_target' => '2026-11-07T09:00:00+07:00',
    'date_text'        => 'sabtu, 07 November 2026',

    // ---------- Kata pembuka ----------
    'greeting' => "Bismillahirrahmanirrahim\nAssalamu'alaikum Warahmatullahi Wabarakatuh",
    'intro'    => "Maha Suci Allah SWT Yang Telah Menciptakan Makhluk-nya Berpasang-pasangan\nYa Allah, Perkenankan Kami Menikahkan Putra-Putri Kami:",

    // ---------- Mempelai ----------
    'groom' => [
        'nickname' => 'Jenggo',
        'fullname' => 'Jenggo Rezeki',
        'parents'  => 'Putra Ke-2 dari 4 Saudara dr Bapak Ujang tanjung (Ujang)/ novenrizal (pen) & Ibu Yulimarni (Unyil)',
        'photo'    => 'assets/img/groom.jpeg',
        'instagram'=> '',                        
    ],
    'bride' => [
        'nickname' => 'Aura',
        'fullname' => 'Aura Rahmadani',
        'parents'  => 'Putri Ke-2 dari 3 Saudara dr Bapak musliadi (Mus) & Ibu Afriza Yunita (af)',
        'photo'    => 'assets/img/bride.jpeg',
        'instagram'=> '',
    ],

    // ---------- Ayat / kutipan ----------
    'quote' => [
        'text'   => 'Dan di antara tanda-tanda (kebesaran)-Nya ialah Dia menciptakan pasangan-pasangan untukmu dari jenismu sendiri, agar kamu cenderung dan merasa tenteram kepadanya, dan Dia menjadikan di antaramu rasa kasih dan sayang.',
        'source' => 'QS. Ar-Rum : 21',
    ],

    // ---------- Acara ----------
    'events' => [
        [
            'title'   => 'Akad Nikah',
            'date'    => 'Jumat, 06 November 2026',
            'time'    => '09.00 WIB S/D SELESAI',
            'place'   => 'Pasa Gadang',
            'address' => 'Kec. Padang Selatan, Kota Padang',
        ],
        [
            'title'   => 'Resepsi',
            'date'    => 'Sabtu, 07 November 2026',
            'time'    => '08:00 WIB S/D SELESAI',
            'place'   => 'Paguah Duku',
            'address' => 'Kurai Taji, Kecamatan Nan Sabaris, Kab. Padang Pariaman',
        ],
    ],

    // Peta lokasi (Google Maps)
    'maps_query' => 'Kurai Taji, Nan Sabaris, Padang Pariaman',
    'maps_link'  => 'https://www.google.com/maps/search/?api=1&query=Kurai+Taji+Nan+Sabaris+Padang+Pariaman',

    'dresscode' => 'Busana bebas sopan',

    // ---------- Menggantikan Kisah Cinta (Story) dengan Turut Mengundang ----------
    'story' => [], // Dikosongkan agar bagian timeline/kisah cinta disembunyikan

    'invited_guests' => [
        'ninik_mamak' => [
            'title' => 'NINIAK MAMAK / SUMANDO',
            'list'  => [
                'Syamsir alam (samsia) / Beyar',
                'Mhd. Rasyid (Kaciak)',
                'Irwan Amir tu (WAN) / WAT',
                'Syahrial (yal) / Agustina',
                'Zulnadi (kaliang)',
                'Kambarudin (Kamba) / Novi',
                'Irwan (enek)',
            ],
        ],
        'anduang_ungku' => [
            'title' => 'ANDUANG / UNGKU',
            'list'  => [
                'Nurmin Caniago (Jo min)',
                'Mande (kampuang)',
            ],
        ],
        'mande_etek' => [
            'title' => 'MANDE / ETEK',
            'list'  => [
                'Nurmi yanti',
            ],
        ],
        'kakak_adiak' => [
            'title' => 'KAKAK / ADIAK / SEPUPU',
            'list'  => [
                'Maimar rina (Rina) / Sefri Kurniawan (Asep)',
                'Indah permata sari (indah)',
                'Putri safeni (putri)',
                'Fitri yeni (yen)',
                'Bunga Yolanda (bunga)',
                'Siska Susanti S.E( Siska ) / Abdurrahman alfauzan D.',
                'Zulkifli (Zul cani milau)',
                'Cut Sahara (cut)',
            ],
        ],
        'footer' => 'Ninik Mamak Urang Sumando Dan Seluruh Sanak Famili Serta Kedua Mempelai',
    ],

    // ---------- Galeri ----------
    'gallery' => ['1.jpeg', '2.jpeg', '3.jpeg', '4.jpeg'],

    // Foto sampul & Musik
    'cover_photo' => 'assets/img/cover.jpg',
    'music'       => 'assets/audio/musik.mp3',

    // ---------- Amplop digital / hadiah ----------
    'gift_text' => 'Doa restu Anda adalah hadiah terindah bagi kami. Namun jika ingin memberikan tanda kasih, dapat melalui:',
    'bank_accounts' => [
        ['bank' => 'BRI',     'number' => '001501111171507',   'name' => 'Jenggo Rezeki'],
        ['bank' => 'DANA',    'number' => '082119618802', 'name' => 'Jenggo Rezeki'],
    ],
    'gift_address' => 'Paguah Duku, Kurai Taji, Kec. Nan Sabaris, Kab. Padang Pariaman (a.n. Jenggo)', 

    // ---------- Penutup ----------
    'closing' => 'Merupakan Suatu Kehormatan Dan Kebahagiaan Bagi Kami Apabila Bapak/Ibu/Saudara/i Berkenan Hadir Untuk Memberikan Do\'a Restu Dari Bapak/ Ibu/ Saudara/i Kami Ucapkan Terima Kasih.',
    'closing_greeting' => "Wassalamu'alaikum Warahmatullahi Wabarakatuh",
    'families' => [
        'header' => 'Hormat Kami Yang Menanti / Keluarga Besar',
        'groom_family' => "Bapak Ujang tanjung (Ujang)/ novenrizal (pen)\nIbu Yulimarni (Unyil)",
        'bride_family' => "Bapak musliadi (Mus)\nIbu Afriza Yunita (af)",
    ],

    // ---------- RSVP ----------
    'max_guests' => 5,

    // ---------- Admin ----------
    'admin_password' => 'laduak13',
];