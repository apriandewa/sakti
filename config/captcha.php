<?php

return [
    'disable' => env('CAPTCHA_DISABLE', false),
    'characters' => ['2','3','4','5','6','7','8','9'], // menghilangkan karakter yang membingungkan seperti 0, O, 1, I
    'default' => [
    'length' => 4,                // hanya 4 karakter supaya singkat
    'width' => 120,
    'height' => 40,
    'quality' => 90,
    'lines' => 0,                 // tidak ada garis pengganggu
    'bgImage' => false,
    'bgColor' => '#ffffff',       // background putih polos
    'fontColors' => ['#000000'],  // warna hitam solid
    'contrast' => 0,               // tidak ada efek kontras
    'angle' => 0,                  // huruf tidak diputar
    'sharpen' => 0,                 // tidak diasah
    'blur' => 0,                    // tidak diburamkan
    'sensitive' => false,
    ],
    'math' => [
        'length' => 9,
        'width' => 120,
        'height' => 36,
        'quality' => 90,
        'math' => true,
    ],

    'flat' => [
        'length' => 6,
        'width' => 160,
        'height' => 46,
        'quality' => 90,
        // 'lines' => 6,
        'bgImage' => false,
        'bgColor' => '#ecf2f4',
        'fontColors' => ['#2c3e50', '#c0392b', '#16a085', '#c0392b', '#8e44ad', '#303f9f', '#f57c00', '#795548'],
        'contrast' => -5,
    ],
    'mini' => [
        'length' => 3,
        'width' => 60,
        'height' => 32,
    ],
    'inverse' => [
        'length' => 5,
        'width' => 120,
        'height' => 36,
        'quality' => 90,
        'sensitive' => true,
        'angle' => 12,
        // 'sharpen' => 10,
        // 'blur' => 2,
        'invert' => true,
        'contrast' => -5,
    ],
    'simple' => [
    'characters' => ['2','3','4','5','6','7','8','9'], // menghilangkan karakter yang membingungkan seperti 0, O, 1, I
    'length' => 4,                // hanya 4 karakter supaya singkat
    'width' => 120,
    'height' => 40,
    'quality' => 90,
    'lines' => 0,                 // tidak ada garis pengganggu
    'bgImage' => false,
    'bgColor' => '#ffffff',       // background putih polos
    'fontColors' => ['#000000'],  // warna hitam solid
    'contrast' => 0,               // tidak ada efek kontras
    'angle' => 0,                  // huruf tidak diputar
    'sharpen' => 0,                 // tidak diasah
    'blur' => 0,                    // tidak diburamkan
    'sensitive' => false,
],

];
