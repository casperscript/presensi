<?php

namespace app\pns\model;

use system;
use comp;

class apelpagi_service extends system\Model {

    public function __construct() {
        parent::__construct();
        parent::setConnection('db_presensi');
    }

    public function getArrayJam($id = null) {
        parent::setConnection('db_presensi');

        $q_cari = '';
        if ($id)
            $q_cari = ' AND WHERE id_jam_apel = "'.$id.'"';

        $data = $this->getData('SELECT * FROM tb_jam_apel WHERE is_default=0 '.$q_cari.' ORDER BY id_jam_apel DESC');
        $jamapel = [];
        foreach ($data['value'] as $i) {
            for ($a = strtotime($i['tanggal_mulai']); $a <= strtotime($i['tanggal_akhir']);){
                $jamapel[date('Y-m-d', $a)] = [
                    'awal' => $i['mulai_apel'],
                    'akhir' => $i['akhir_apel']
                ];
                $a += 86400;
            }
        }
        return $jamapel;
    }

    /*-------------------------------------*/

    public function getMasukkerja($data) {
        parent::setConnection('db_presensi');
        //$params = [$data['kdlokasi'], $data['bulan'], $data['tahun']];
        /*$get = $this->getData("SELECT * FROM view_jadwal WHERE kdlokasi = ? 
            AND MONTH(tanggal) = ? AND YEAR(tanggal) = ?
        ", $params);*/

        $params = [$data['bulan'], $data['tahun']];
        $get = $this->getData("SELECT * FROM view_jadwal 
            WHERE pin_absen in (". $data['personil'] .") AND MONTH(tanggal) = ? 
            AND YEAR(tanggal) = ?
        ", $params);

        $masuk = [];
        foreach ($get['value'] as $i) {
            $tgl = (int)date('d', strtotime($i['tanggal']));
            $pin = $i['pin_absen'];

            if ($i['masuk'] == '00:00:00')
                $masuk[$pin][$tgl] = 'HL';
            else
                $masuk[$pin][$tgl] = $i['masuk'];
        }
        return $masuk;
    }

    public function getBatalApel($data) {
        parent::setConnection('db_presensi');
        $get = $this->getData("SELECT * FROM tb_batal_apel WHERE MONTH(tanggal_apel) = ? AND YEAR(tanggal_apel) = ? AND pin_absen = ? AND status_batal = 1", [$data['bulan'], $data['tahun'], $data['pin_absen']]);
        
        $rekap = [];
        foreach ($get['value'] as $i) {
            $tgl = (int)date('d', strtotime($i['tanggal_apel']));
            $rekap[$i['pin_absen']][$tgl] = true;
        }
        
        return $rekap;
    }

    public function getRecordApel($data) {
        parent::setConnection('db_presensi');
        $idKey = array($data['bulan'], $data['tahun']);
        $batal = $this->getBatalApel($data);
        /*$query = 'SELECT * FROM view_apel_all '
                . 'WHERE (MONTH(tanggal_log_presensi) = ?) AND (YEAR(tanggal_log_presensi) = ?) AND pin_absen in ('.$data['personil'].') '
                . 'ORDER BY tanggal_log_presensi DESC, jam_log_presensi DESC';*/
        $query = 'SELECT * FROM tb_log_presensi '
                . 'WHERE (MONTH(tanggal_log_presensi) = ?) AND (YEAR(tanggal_log_presensi) = ?) AND pin_absen in ('.$data['personil'].') AND status_log_presensi = 2 '
                . 'ORDER BY tanggal_log_presensi DESC, jam_log_presensi DESC';
        $dataArr = $this->getData($query, $idKey);

        $result = [];
        //if ($dataArr['count'] == 0)
            //return $result;

        $masuk = $this->getMasukkerja($data);
        $jadwal_apel = $this->getArrayJam();
        foreach ($dataArr['value'] as $i) {
            $tgl = (int)date('d', strtotime($i['tanggal_log_presensi']));
            $pin_absen = $i['pin_absen'];

            $jammasuk = ''; 
            if (isset($masuk[$pin_absen][$tgl])) {
                if ($masuk[$pin_absen][$tgl] != 'HL') {
                    $jammasuk = \DateTime::createFromFormat('H:i:s', $masuk[$pin_absen][$tgl]);
                    $jammasuk = strtotime($jammasuk->modify('+1 minutes')->format('H:i:s'));
                    unset($masuk[$pin_absen][$tgl]);
                }
            }

            $compare = $this->compare($i['tanggal_log_presensi'], $i['jam_log_presensi'], $jadwal_apel, $jammasuk);
            
            if ($compare == 1) {
                if (isset($batal[$pin_absen][$tgl]))
                    $result[$pin_absen][$tgl] = 'A0';
                elseif ($data['format'] == 'A') 
                    $result[$pin_absen][$tgl] = 'A1';
                elseif ($data['format'] == 'B')
                    $result[$pin_absen][$tgl] = substr($i['jam_log_presensi'], 0, 5);
            } 

            if ($compare && $compare == 'NR')
                $result[$pin_absen][$tgl] = 'NR';
        }

        foreach ($masuk as $key => $i) {
            foreach ($i as $tgl => $val) {
                $hari = date("l", strtotime($data['tahun'] . '-'. $data['bulan'] . '-' . $tgl));
                if (!is_array($val) && $val == 'HL') {
                    if ($hari == 'Saturday' || $hari == 'Sunday')
                        $result[$key][$tgl] = 'HL';
                    else
                        $result[$key][$tgl] = 'NR';
                } else {
                    $jammasuk = \DateTime::createFromFormat('H:i:s', $val);
                    $jammasuk = strtotime($jammasuk->modify('+1 minutes')->format('H:i:s'));

                    $bln = ($data['bulan'] < 10 ? '0' : '') . $data['bulan'];
                    $tgl_full = $data['tahun'] . '-' . $bln . '-' . ($tgl < 10 ? '0' : '') . $tgl;
                    $compare = $this->compare($tgl_full, '00:00:00', $jadwal_apel, $jammasuk);

                    if ($compare && $compare == 'NR')
                        $result[$key][$tgl] = 'NR';
                    
                    //jk masuk hari sabtu / minggu NR
                    if ($hari == 'Saturday' || $hari == 'Sunday')
                        $result[$key][$tgl] = 'NR';
                }
            }
        }
        return $result;
    }

    public function compare($tanggal, $finger, $jamapel, $jammasuk = '') {
        parent::setConnection('db_presensi');

        if (!isset($jamapel[$tanggal])) {
            $default = $this->getData('SELECT * FROM tb_jam_apel WHERE is_default=1 ORDER BY id_jam_apel DESC', []);
            if ($default['count'] > 0) {
                $awal = $default['value'][0]['mulai_apel'];
                $akhir = $default['value'][0]['akhir_apel'];
            } else {
                $awal = '07:15:00';
                $akhir = '08:00:00';
            }
        } else {
            $awal = $jamapel[$tanggal]['awal'];
            $akhir = $jamapel[$tanggal]['akhir'];
        }

        $awal = strtotime($awal);
        $akhir = strtotime($akhir);
        $apel = strtotime($finger);

        if ($jammasuk != '' && ($jammasuk < $awal || $jammasuk >= $akhir)) {
            return 'NR';
        }

        if ($apel > $awal && $apel <= $akhir)
            return 1;
        else
            return 0;
    }

    public function getTabelRecordPersonil($data) {
        parent::setConnection('db_presensi');

        $idKey = array($data['pin_absen'], $data['sdate'], $data['edate']);
        $page = (!empty($data['page'])) ? $data['page'] : 1;
        $batas = (!empty($data['batas'])) ? $data['batas'] : 10;
        $query = 'SELECT * FROM tb_record_apel '
                . 'WHERE (pin_absen = ?) AND (tanggal_apel BETWEEN ? AND ?) '
                . 'GROUP BY tanggal_apel ORDER BY tanggal_apel DESC, jam_apel DESC';
        $j_query = 'SELECT COUNT(pin_absen) AS jumlah FROM tb_record_apel '
                . 'WHERE (pin_absen = ?) AND (tanggal_apel BETWEEN ? AND ?) GROUP BY tanggal_apel';

        $posisi = ($page - 1) * $batas;
        $jmlData = $this->getData($j_query, $idKey);
        $dataArr = $this->getData($query . ' LIMIT ' . $posisi . ', ' . $batas, $idKey);

        //check apel
        if ($dataArr['count'] > 0) {
            $last = $dataArr['count'] - 1;
            $max = $dataArr['value'][0]['tanggal_apel'];
            $min = $dataArr['value'][$last]['tanggal_apel'];
            $idKey = array($data['pin_absen'], $min, $max);
        }
        $check = $this->getData('SELECT * FROM tb_record_apel '
                . 'WHERE (pin_absen = ?) AND (tanggal_apel BETWEEN ? AND ?) '
                . 'ORDER BY tanggal_apel DESC, jam_apel DESC', $idKey);
        $data = $this->checkApel($check['value']);

        $result['no'] = $posisi + 1;
        $result['page'] = $page;
        $result['batas'] = $batas;
        $result['jmlData'] = $jmlData['count'];
        $result['dataTabel'] = $data;
        $result['query'] = $dataArr['query'];

        return $result;
    }

    public function checkApel($data) {
        $check = [];
        $jadwal = $this->getArrayJam();
        foreach ($data as $i) {
            $key = $i['tanggal_log_presensi'];
            //jk multi finger
            $compare = $this->compare($i['tanggal_log_presensi'], $i['jam_log_presensi'], $jadwal);

            if ($compare == 1)
                $check[$key] = $i;
        }

        return $check;
    }

        /****************** Get Data ************************** */
    public function getDataPersonil($id) {
        parent::setConnection('db_pegawai');

        $query = 'SELECT * FROM view_presensi_personal WHERE (pin_absen = ?)';
        $result = $this->getData($query, array($id));
        if ($result['count'] > 0) {
            return $result['value'][0];
        } else {
            return $this->getTabel('view_presensi_personal');
        }
    }
}

?>
