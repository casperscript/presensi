<?php

namespace comp;

class FUNC {

    public static $namabulan = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
    public static $namabulan1 = array(
        '1' => 'Januari',
        '2' => 'Februari',
        '3' => 'Maret',
        '4' => 'April',
        '5' => 'Mei',
        '6' => 'Juni',
        '7' => 'Juli',
        '8' => 'Agustus',
        '9' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember'
    );
    public static $namahari = array('Sun' => 'Minggu', 'Mon' => 'Senin', 'Tue' => 'Selasa', 'Wed' => 'Rabu', 'Thu' => 'Kamis', 'Fri' => 'Jumat', 'Sat' => 'Sabtu');

    public static function rupiah($number) {
        return 'Rp. ' . number_format($number, 0, ',', '.');
    }
    public static function tanggal($tgl, $opt) {
        $D = date('D', strtotime($tgl));
        $d = date('d', strtotime($tgl));
        $m = date('m', strtotime($tgl));
        $M = date('M', strtotime($tgl));
        $y = date('Y', strtotime($tgl));
        $w = date('H:i:s', strtotime($tgl));
        $t = date('H:i a', strtotime($tgl));
        switch ($opt) {
            case 'time' : return $t;
                break;
            case 'day' : return self::$namahari[$D];
                break;
            case 'short_date' : return date('d/m/Y', strtotime($tgl));
                break;
            case 'long_date' : return intval($d) . ' ' . self::$namabulan[$m - 1] . ' ' . $y;
                break;
            case 'short_date_time' : return date('d/m/Y H:i:s', strtotime($tgl));
                break;
            case 'long_date_time' : return intval($d) . ' ' . self::$namabulan[$m - 1] . ' ' . $y . ' [' . $w . ']';
                break;
            case 'date_month' : return intval($d) . ' ' . $M;
                break;
        }
    }

    public static function moments($session_time) {
        $session_time = strtotime($session_time);
        $time_difference = time() - $session_time;
        $seconds = $time_difference;
        $minutes = round($time_difference / 60);
        $hours = round($time_difference / 3600);
        $days = round($time_difference / 86400);
        $weeks = round($time_difference / 604800);
        $months = round($time_difference / 2419200);
        $years = round($time_difference / 29030400);

        if ($seconds <= 60) {
            echo 'Baru saja';
        } else if ($minutes <= 60) {
            if ($minutes == 1)
                echo 'Satu menit yang lalu';
            else
                echo $minutes . ' menit yang lalu';
        }
        else if ($hours <= 24) {
            if ($hours == 1)
                echo 'Satu jam yang lalu';
            else
                echo $hours . ' jam yang lalu';
        }
        else if ($days <= 7) {
            if ($days == 1)
                echo 'Satu hari yang lalu';
            else
                echo $days . ' hari yang lalu';
        }
        else if ($weeks <= 4) {
            if ($weeks == 1)
                echo 'Satu minggu yang lalu';
            else
                echo $weeks . ' minggu yang lalu';
        }
        else if ($months <= 12) {
            if ($months == 1)
                echo 'Satu bulan yang lalu';
            else
                echo $months . ' bulan yang lalu';
        }
        else {
            if ($years == 1)
                echo 'Satu tahun yang lalu';
            else
                echo $years . ' tahun yang lalu';
        }
    }

    public static function thumbsImage($nw, $nh, $source, $stype, $dest) {
        $size = getimagesize($source); // ukuran gambar
        $w = $size[0];
        $h = $size[1];
        switch ($stype) { // format gambar
            case 'gif':
                $simg = imagecreatefromgif($source);
                break;
            case 'jpg':
                $simg = imagecreatefromjpeg($source);
                break;
            case 'png':
                $simg = imagecreatefrompng($source);
                break;
        }

        $dimg = imagecreatetruecolor($nw, $nh); // menciptakan image baru
        $wm = $w / $nw;
        $hm = $h / $nh;

        $h_height = $nh / 2;
        $w_height = $nw / 2;

        if ($w > $h) {
            $adjusted_width = $w / $hm;
            $half_width = $adjusted_width / 2;
            $int_width = $half_width - $w_height;
            imagecopyresampled($dimg, $simg, -$int_width, 0, 0, 0, $adjusted_width, $nh, $w, $h);
        } elseif (($w < $h) || ($w == $h)) {
            $adjusted_height = $h / $wm;
            $half_height = $adjusted_height / 2;
            $int_height = $half_height - $h_height;
            imagecopyresampled($dimg, $simg, 0, -$int_height, 0, 0, $nw, $adjusted_height, $w, $h);
        } else {
            imagecopyresampled($dimg, $simg, 0, 0, 0, 0, $nw, $nh, $w, $h);
        }

        imagejpeg($dimg, $dest, 100);
        imagedestroy($simg);
        imagedestroy($dimg);
    }

    public static function resizeImage($dw, $source, $stype, $dest) {
        $size = getimagesize($source); // ukuran gambar
        $sw = $size[0];
        $sh = $size[1];
        switch ($stype) { // format gambar
            case 'gif':
                $simg = imagecreatefromgif($source);
                break;
            case 'jpg':
                $simg = imagecreatefromjpeg($source);
                break;
            case 'png':
                $simg = imagecreatefrompng($source);
                break;
        }

        // $dw = 800;
        $dh = ($dw / $sw) * $sh;
        $dimg = imagecreatetruecolor($dw, $dh);
        imagecopyresampled($dimg, $simg, 0, 0, 0, 0, $dw, $dh, $sw, $sh);
        imagejpeg($dimg, $dest);

        imagedestroy($simg);
        imagedestroy($dimg);
        unlink($source);
    }

    public static function encodeImage($img_file, $mimeType) {
        $img_bin = base64_encode(fread(fopen($img_file, 'r'), filesize($img_file)));
        return 'data:' . $mimeType . ';base64,' . $img_bin;
    }

    public static function encryptor($string) {
        $output = false;
        $encrypt_method = 'AES-256-CBC';
        $secret_key1 = 'jendralhans@gmail.com';
        $secret_key2 = 'anggoro.triantoko@gmail.com';
        $key1 = hash('sha256', $secret_key1);
        $key2 = substr(hash('sha256', $secret_key2), 0, 16);
        $output = base64_encode(openssl_encrypt(($string), $encrypt_method, $key1, 0, $key2));
        return $output;
    }
    
    public static function decryptor($string) {
        $output = false;
        $encrypt_method = 'AES-256-CBC';
        $secret_key1 = 'jendralhans@gmail.com';
        $secret_key2 = 'anggoro.triantoko@gmail.com';
        $key1 = hash('sha256', $secret_key1);
        $key2 = substr(hash('sha256', $secret_key2), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key1, 0, $key2);
        return $output;
    }
    
    public static function showPre($data) {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }

    public static function fixedInt($int, $len = '2', $val = '0') {
        return str_pad($int, $len, $val, STR_PAD_LEFT);
    }

    public static function getUserIp() {
        $client = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote = $_SERVER['REMOTE_ADDR'];
        if (filter_var($client, FILTER_VALIDATE_IP)) {
            $ip = $client;
        } else if (filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip = $forward;
        } else {
            $ip = $remote;
        }
        return $ip;
    }

    public static function autonumber($id_terakhir, $panjang_kode, $panjang_angka) {

        // link : https://arifnd.wordpress.com/2014/12/31/membuat-fungsi-auto-increment-bertipe-string-dengan-php/
        // mengambil nilai kode ex: KNS0015 hasil KNS
        $kode = substr($id_terakhir, 0, $panjang_kode);

        // mengambil nilai angka
        // ex: KNS0015 hasilnya 0015
        $angka = substr($id_terakhir, $panjang_kode, $panjang_angka);

        // menambahkan nilai angka dengan 1
        // kemudian memberikan string 0 agar panjang string angka menjadi 4
        // ex: angka baru = 6 maka ditambahkan strig 0 tiga kali
        // sehingga menjadi 0006
        $angka_baru = str_repeat("0", $panjang_angka - strlen($angka + 1)) . ($angka + 1);

        // menggabungkan kode dengan nilang angka baru
        $id_baru = $kode . $angka_baru;

        return $id_baru;
    }

    protected static $angka_urutan = array('1' => 'Pertama', '2' => 'Kedua', '3' => 'Ketiga', '4' => 'Keempat', '5' => 'Kelima', '6' => 'Keenam', '7' => 'Ketujuh', '8' => 'Kedelapan', '9' => 'Kesembilan', '10' => 'Kesepuluh');

    public static function terbilang($n) {
        $number = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
        if ($n < 12) {
            return " " . $number[$n];
        } elseif ($n < 20) {
            return self::terbilang($n - 10) . " Belas";
        } elseif ($n < 100) {
            return self::terbilang($n / 10) . " Puluh" . self::terbilang($n % 10);
        } elseif ($n < 200) {
            return " seratus" . self::terbilang($n - 100);
        } elseif ($n < 1000) {
            return self::terbilang($n / 100) . " Ratus" . self::terbilang($n % 100);
        } elseif ($n < 2000) {
            return " seribu" . self::terbilang($n - 1000);
        } elseif ($n < 1000000) {
            return self::terbilang($n / 1000) . " Ribu" . self::terbilang($n % 1000);
        } elseif ($n < 1000000000) {
            return self::terbilang($n / 1000000) . " Juta" . self::terbilang($n % 1000000);
        }
    }
    
    public static function numbSeries($sNumb, $eNumb, $key = true) {
        $arr = range($sNumb, $eNumb);
        if ($key) :
            return array_combine($arr, $arr);
        else:
            return $arr;
        endif;
    }
	
	/**************** Tambahan **********************/

    public static function Parse_Data($data, $p1, $p2) {
        $data = " " . $data;

        $awal = strpos($data, $p1);

        if ($awal != "") {
            $awal = strpos($data, $p1) + strlen($p1);
            $akhir = strpos($data, $p2);

            $panjang = $akhir - $awal;

            $hasil = substr($data, $awal, $panjang);
            return $hasil;
        }
    }
    
    public static function modSymbol($kode, $note = '') {
        if ($kode === '0') {
            $valNote = empty($note) || $note == '-' ? 'Ditolak' : 'Catatan: ' . $note;
            return '<a class="btn btn-floating tooltipped red" data-position="top" delay="50" data-tooltip="' . $valNote . '"><i class="material-icons">close</i></a>';

        } elseif ($kode === '1') {
            $valNote = empty($note) || $note == '-' ? 'Diterima' : 'Catatan: ' . $note;
            return '<a class="btn btn-floating tooltipped green" data-position="top" delay="50" data-tooltip="' . $valNote . '"><i class="material-icons">done</i></a>';

        } elseif ($kode === '2') {
            $valNote = empty($note) || $note == '-' ? 'Disahkan' : 'Catatan: ' . $note;
            return '<a class="btn btn-floating tooltipped yellow darken-4" data-position="top" delay="50" data-tooltip="' . $valNote . '"><i class="material-icons">assignment_turned_in</i></a>';

        } elseif ($kode === '3') {
            $valNote = empty($note) || $note == '-' ? 'Dibatalkan' : 'Catatan: ' . $note;
            return '<a class="btn btn-floating tooltipped black" data-position="top" delay="50" data-tooltip="' . $valNote . '"><i class="material-icons">do_not_disturb</i></a>';

        }else {
            return "-";
        }
    }

    public static function modStatus($kode, $lock = false) {
        $lockStatus = ($lock == true) ? '<i class="material-icons tiny" style="padding-right: 5px">lock</i>' : '';
        if ($kode == "0") {
            return '<span class="new badge red" data-badge-caption="Tolak">' . $lockStatus . '</span>';
        } elseif ($kode == '1') {
            return '<span class="new badge green" data-badge-caption="Terima">' . $lockStatus . '</span>';
        } else {
            return '';
        }
    }
    
    public static function HTMLchip($msg, $kode = false) {
        if ($kode == '2') {
            return '<div class="chip deep-orange darken-1 white-text">' . $msg . '</div>';
        } elseif ($kode == '1') {
            return '<div class="chip yellow darken-3 white-text">' . $msg . '</div>';
        } else {
            return '<div class="chip blue darken-1 white-text">' . $msg . '</div>';
        }
    }

    public static function mergeDate($sdate, $edate) {
        $str_sdate = strtotime($sdate);
        $str_edate = strtotime($edate);

        if (date('m', $str_sdate) != date('m', $str_edate) && date('Y', $str_sdate) == date('Y', $str_edate)) {
            $val_sdate = self::tanggal($sdate, 'long_date');
            $val_edate = self::tanggal($edate, 'long_date');
            $d_sdate = date('d', $str_sdate);
            $m_sdate = self::$namabulan[date('n', $str_sdate) - 1];

            return $d_sdate . ' ' . $m_sdate . ' - ' . $val_edate;

        } elseif (date('m', $str_sdate) != date('m', $str_edate)) {
            $val_sdate = self::tanggal($sdate, 'long_date');
            $val_edate = self::tanggal($edate, 'long_date');
            return $val_sdate . ' - ' . $val_edate;

        } elseif ($sdate == $edate) {
            return self::tanggal($sdate, 'long_date');

        } elseif (date('m', $str_sdate) == date('m', $str_edate)) {
            $val_sdate = date('j', $str_sdate);
            $val_edate = self::tanggal($edate, 'long_date');
            return $val_sdate . ' - ' . $val_edate;

        } else {
            return 'error';
        }
    }



    /*     * ******** !CAUTION! ********* */
    /*     * ******** THIS PART BELONGS TO SPECIFIC HUSNANW IMPLEMENTATIONS ******* */
    /*     * ******** DO NOT REMOVE OR MODIFY UNLESS YOU KNOW WHAT YOU ARE DOING ** */

    public static function husnanWCalculatePotongan($tglAwal, $tglAkhir, $potonganTpp, $satuanPotongan) {
        $jmlHariMod = self::getHusnanWDeltaDates($tglAwal, $tglAkhir);

        if (trim(strtolower($satuanPotongan)) === "hari kerja") {
            return $jmlHariMod * doubleval($potonganTpp);
        } else {
            return 1 * doubleval($potonganTpp);
        }
    }

    public static function husnanWStrImplode($values, $fieldName) {
        $vals = [];

        foreach ($values as $value) {
            $vals[] = "'" . $value[$fieldName] . "'";
        }

        return implode(',', $vals);
    }

    public static function husnanWStrRepeater($str, $n, $separator = ',') {
        $result = "";

        for ($c = 0; $c < $n; $c++) {
            $result .= $str . $separator;
        }

        return substr($result, 0, strlen($result) - 1);
    }

    public static function getHusnanWDeltaDates($firstDate, $secondDate, $isRemaining = true) {
        $firstDate = new \DateTime($firstDate);
        $secondDate = new \DateTime($secondDate);

        if ($isRemaining && $firstDate > $secondDate) {
            return null;
        }

        return $firstDate->diff($secondDate)->days;
    }

    public static function husnanWGenRand($digit = 5) {
        return substr(uniqid(), -$digit);
    }

    public static function husnanWVerModStyle($kode) {
        if ($kode === '0') {
            return '<span class="chip red white-text">DITOLAK</span>';
        } elseif ($kode === '1') {
            return '<span class="chip green white-text">DITERIMA</span>';
        } elseif ($kode === '2') {
            return '<span class="chip orange white-text">DISAHKAN</span>';
        } elseif ($kode === '3') {
            return '<span class="chip black white-text">DIBATALKAN</span>';
        } else {
            return "-";
        }
    }

    public static function toHusnanWStdDateTime($ddmmyyyyhis) {
        $ddmmyyyyhis = empty($ddmmyyyyhis) ? '00-00-0000 00:00:00' : $ddmmyyyyhis;
        $tmp = explode(" ", $ddmmyyyyhis);
        return self::toHusnanWStdDate($tmp[0]) . " | " . $tmp[1];
    }

    public static function toHusnanWSniDateTime($yyyymmddhis) {
        $yyyymmddhis = empty($yyyymmddhis) ? '00-00-0000 00:00:00' : $yyyymmddhis;
        $tmp = explode(" ", $yyyymmddhis);
        return self::toHusnanWSniDate($tmp[0]) . " | " . $tmp[1];
    }

    public static function toHusnanWSniDate($yyyymmdd) {
        $yyyymmdd = empty($yyyymmdd) ? '00-00-0000' : $yyyymmdd;
        $stdDate = explode('-', $yyyymmdd);
        return $stdDate[2] . '-' . $stdDate[1] . '-' . $stdDate[0];
    }

    public static function toHusnanWStdDate($ddmmyyyy) {
        $ddmmyyyy = empty($ddmmyyyy) ? '00-00-0000' : $ddmmyyyy;
        $stdDate = explode('-', $ddmmyyyy);
        return $stdDate[2] . '-' . $stdDate[1] . '-' . $stdDate[0];
    }

    public static function husnanWVarDump($data, $exit = true) {
        echo "<pre>";
        echo var_dump($data);
        echo "</pre>";

        if ($exit) {
            exit();
        }
    }

    /*     * ******* END PART OF HUSNANW IMPLEMENTATIONS ********* */
}

?>
