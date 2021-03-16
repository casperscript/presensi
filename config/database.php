<?php

/* Database Configuration */
$tahun = isset($_POST['tahun']) ? $_POST['tahun'] : '';
return array(
    'db_pegawai' => array(
        'driver' => 'mysql',
//        'host' => '192.168.254.62',
        'host' => 'localhost',
        'port' => 3306,
        'user' => 'admin',
        'password' => '$absensi-db@simapp',
        'dbname' => 'db_e_pegawai_demo',
        'charset' => 'utf8',
        'collate' => 'utf8_general_ci',
        'persistent' => false,
        'errorMsg' => 'Maaf, Gagal terhubung dengan Database Main.',
    ),
    'db_presensi' => array(
        'driver' => 'mysql',
//        'host' => '192.168.254.62',
        'host' => 'localhost',
        'port' => 3306,
        'user' => 'admin',
        'password' => '$absensi-db@simapp',
        'dbname' => 'db_e_presensi',
        'charset' => 'utf8',
        'collate' => 'utf8_general_ci',
        'persistent' => false,
        'errorMsg' => 'Maaf, Gagal terhubung dengan Database Data.',
    ),
    'db_backup' => array(
        'driver' => 'mysql',
//        'host' => '192.168.254.62',
        'host' => 'localhost',
        'port' => 3306,
        'user' => 'admin',
        'password' => '$absensi-db@simapp',
        'dbname' => 'db_backup' . ($tahun >= 2021 ? '_dev' : '') . ($tahun != '' ? '_' . $tahun : ''),
        'charset' => 'utf8',
        'collate' => 'utf8_general_ci',
        'persistent' => false,
        'errorMsg' => 'Maaf, Gagal terhubung dengan Database Backup.',
    ),
);
/* ---------------------- */
?>
