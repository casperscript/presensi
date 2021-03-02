<?php
$host = "192.168.254.216";
$user = "epresensi";
$pass = "epresensi123ok";
$db   = "sikd_pkl_2018";

mysql_connect($host, $user, $pass)
       or die (mysql_error());
mysql_select_db($db)
       or die(mysql_error()." Database Not Found!");

$sql = "SELECT
     apbd_pjbt_keuangan.sikd_satker_id AS kode_skpd,
     apbd_pjbt_keuangan.sikd_jab_keuangan_id AS kode_bendahara,
     apbd_pjbt_keuangan.nip_personil AS nip_bendahara,
     apbd_pjbt_keuangan.nm_personil AS nama_bendahara,
     apbd_pjbt_keuangan.sikd_sub_skpd_id AS kodesub_skpd,
     sikd_satker.nama AS nama_skpd,
     sikd_sub_skpd.nama AS nama_subskpd
FROM
     apbd_pjbt_keuangan
     INNER JOIN sikd_satker ON apbd_pjbt_keuangan.sikd_satker_id = sikd_satker.id_sikd_satker
     INNER JOIN  sikd_sub_skpd ON apbd_pjbt_keuangan.sikd_sub_skpd_id = sikd_sub_skpd.id_sikd_sub_skpd
WHERE
     kode_bendahara = '005'";

?>
