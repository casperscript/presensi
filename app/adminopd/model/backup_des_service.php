<?php

namespace app\adminopd\model;

use system;
use app\adminopd\model\laporan_service;
use app\adminopd\model\pegawai_service;

class backup_des_service extends system\Model
{

    public function __construct()
    {
        parent::__construct();
        parent::setConnection('db_backup_desember');
        $this->laporan_service = new laporan_service();
        $this->pegawai_service = new pegawai_service();
    }
    public function getDataPersonilBatch_v2($data)
    {
        $q_cari = '';
        if (isset($data['pin_absen']) && $data['pin_absen'] != '') {
            $q_cari .= 'AND pin_absen IN (' . $data['pin_absen'] . ')';
        }
        $result = $this->getData('SELECT * FROM tb_personil '
            . 'WHERE tampil_tpp = 1 AND induk_id = "' . $data['induk']['id'] . '" ' . $q_cari
            . 'ORDER BY kelas DESC, nama_personil ASC');
        return $result;
    }

    public function getDataInduk_V3($data) {
        parent::setConnection('db_backup_desember');
        $idKey = [$data['kdlokasi'], $data['bulan'], $data['tahun']];
        $query = 'SELECT * FROM tb_induk WHERE kdlokasi = ? AND bulan = ? AND tahun = ?';
        $dataArr = $this->getData($query, $idKey);

        if ($dataArr['count'] > 0)
            return $dataArr['value'][0];

        return false;
    }

    public function getDataTpp($induk_id) {
        $query = 'SELECT * FROM tb_tpp WHERE induk_id = "' . $induk_id . '" ';
        $dataArr = $this->getData($query, []);

        if ($dataArr['count'] > 0)
            return $dataArr['value'][0];

        return false;
    }

    public function getLaporan($induk_id) {
        $query = 'SELECT * FROM tb_laporan WHERE induk_id = "' . $induk_id . '"';
        $dataArr = $this->getData($query, []);

        if ($dataArr['count'] > 0) {
            $lap = $dataArr['value'][0];

            $plus = [
                'ver_admin_opd' => $lap['nip_admin_opd'],
                'sah_kepala_opd' => $lap['nip_kepala_opd'],
                'ver_admin_kota' => $lap['nip_admin_kota'],
                'sah_kepala_bkppd' => $lap['nip_kepala_bkppd'],
                'sah_final' => $lap['nip_final'],
            ];

            return array_merge($lap, $plus);
        }

        return false;
    }

    public function getRekapAllView($induk_id, $pin_absen = '') {
        $cond = "";
        if ($pin_absen != '')
            $cond = " AND tb_personil.pin_absen IN (" . $pin_absen . ")";

        $presensi = $this->getData('SELECT tb_presensi.* FROM tb_presensi
            JOIN tb_personil ON tb_personil.id = tb_presensi.personil_id
            JOIN tb_induk ON tb_induk.id = tb_personil.induk_id
            WHERE tb_induk.id="' . $induk_id . '"' . $cond, []);

        $response = [];
        if ($presensi['count'] > 0) {
            $get = $presensi['value'];
            foreach ($get as $isi) {
                $response[$isi['personil_id']] = $isi;
            }
        }

        return $response;
    }
}
