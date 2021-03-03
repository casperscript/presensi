<?php

/* Website Configuration */
return array(
    // Set default main controller
    'defaultController' => array(
        'project' => 'admin'
    ),
    // Set default main templalte
    'defaultTemplate' => array(
        'template' => 'theme_admin',
    ),
    // Load automatically view in index.php 
    'setting' => array(
        'web_title' => 'e-Presensi',
        'web_author' => 'Dinas Komunikasi dan Informatika Kota Pekalongan',
        'web_description' => 'e-Presensi',
        'web_keywords' => 'e-Presensi',
        'web_header' => 'e-Presensi',
        'web_footer' => 'Copyright 2017',
        'dir_arsip' => UPLOAD . 'archive/',
        'dir_panduan' => UPLOAD . 'panduan',
        "husnanw_moderasi_upload_path" => UPLOAD . "moderasi/dokumen", // added by husnanw
        "simpeg_url" => "http://simpeg.pekalongankota.go.id", // added by husnanw
        "new_simpeg_url" => "http://192.168.254.226", // added by husnanw
        'file_type' => 'jpeg|jpg|png|pdf',
        'max_size' => 104857600, // 100mb
    ),
    /* Config Project */
    'project' => array(
        // Project admin
        'anggaran' => array(
            'session' => 'PRESENSIANGG',
            'cookie' => 'CKANGGPRESENSI',
            'path' => 'anggaran',
            'controller' => 'main',
            'method' => 'index',
        ),
        'admin' => array(
            'session' => 'PRESENSI',
            'cookie' => 'CKADMPRESENSI',
            'path' => 'admin',
            'controller' => 'main',
            'method' => 'index',
        ),
        'pns' => array(
            'session' => 'PRESENSIPNS',
            'cookie' => 'CKPNSPRESENSI',
            'path' => 'pns',
            'controller' => 'main',
            'method' => 'index',
        ),
        'adminopd' => array(
            'session' => 'PRESENSIADMINOPD',
            'cookie' => 'CKADMINOPDPRESENSI',
            'path' => 'adminopd',
            'controller' => 'main',
            'method' => 'index',
        ),
        'kepalaopd' => array(
            'session' => 'PRESENSIKEPALAOPD',
            'cookie' => 'CKKEPALAOPDPRESENSI',
            'path' => 'kepalaopd',
            'controller' => 'main',
            'method' => 'index',
        ),
        'kepalabkppd' => array(
            'session' => 'PRESENSIKEPALABKPPD',
            'cookie' => 'CKKEPALABKPPDPRESENSI',
            'path' => 'kepalabkppd',
            'controller' => 'main',
            'method' => 'index',
        ),
        'adminsistem' => array(
            'session' => 'PRESENSIADMINSISTEM',
            'cookie' => 'CKADMINSISTEMPRESENSI',
            'path' => 'adminsistem',
            'controller' => 'main',
            'method' => 'index',
        ),
        'pengawas' => array(
            'session' => 'PRESENSIPENGAWAS',
            'cookie' => 'CKPENGAWASPRESENSI',
            'path' => 'pengawas',
            'controller' => 'main',
            'method' => 'index',
        ),
        'sync' => array(
            'session' => 'SYNC',
            'cookie' => 'CKSYNC',
            'path' => 'sync',
            'controller' => 'cronjob',
            'method' => 'index',
        ),
    ),
    /* Config Template */
    'template' => array(
        // Path template admin
        'theme_admin' => array(
            'basePath' => 'theme_admin/',
        )
    ),
);
/* ---------------------- */
?>
