<?php

namespace app\pns\controller;

use app\pns\model\servicemain;
use app\pns\model\laporan_service;
use app\pns\model\pegawai_service;
use app\pns\model\backup_service;
use app\pns\model\HusnanWModerasiModel;
use app\adminsistem\model\webadapter;
use system;
use comp;

class laporan extends system\Controller {

    public function __construct() {
        parent::__construct();
        $this->servicemaster = new HusnanWModerasiModel();
        $this->servicemain = new servicemain();
        $session = $this->servicemain->cekSession();

        if ($session['status'] === true) {
            $this->laporan_service = new laporan_service();
            $this->pegawai_service = new pegawai_service();
            $this->backup_service = new backup_service();
            $this->webadapter = new webadapter();

            $this->setSession('SESSION_LOGIN', $session['data']);
            $this->login = $this->getSession('SESSION_LOGIN');
            $this->pinAbsen = $this->servicemaster->getPinAbsen($this->login["nipbaru"]);
            $this->satker = $this->laporan_service->getDataSatker($this->login['kdlokasi'])['singkatan_lokasi'];
        } else {
            $this->setSession('SESSION_RELOAD', true);
            $this->redirect($this->link('login'));
        }
    }

    protected function index() {
        $data['title'] = 'Laporan';
        $data['breadcrumb'] = '<a href="' . $this->link() . '" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Laporan</a>';

        $data['listTahun'] = comp\FUNC::numbSeries('2018', date('Y'));
        $this->showView('index', $data, 'theme_admin');
    }

    protected function tabelrekapc1() {
        $input = $this->post(true);
        if ($input) {
            $versi = $this->laporan_service->getDataVersi('history_of_report_tpp_rules', $input);
            switch ($versi['data_1']) {
                case 'v1':
                    $this->tabelrekapc1_v1($input, true);
                    break;
                case 'v2':
                    $this->tabelrekapc1_v2($input, true);
                    break;
                case 'v3':
                    $this->tabelrekapc1_v3($input, true);
                    break;
            }
        }
    }

    protected function tabelrekapc1_v1($input) {
        if ($input) {
            foreach ($input as $key => $i)
                $data[$key] = $i;

            $data['format'] = 'A';
            $data['jenis'] = '';
            $data['personil'] = $this->pinAbsen;
            $data['kdlokasi'] = $this->login['kdlokasi'];
            $data['satker'] = $this->laporan_service->getDataSatker($this->login['kdlokasi']);
            $data['pegawai'] = $this->laporan_service->getDataPersonil($this->pinAbsen);

            $data['laporan'] = $this->laporan_service->getLaporan($data);
            $data['backup'] = $this->backup_service->getData("SELECT tb_induk.* FROM tb_induk 
                JOIN tb_personil ON tb_personil.induk_id = tb_induk.id
                LEFT JOIN tb_presensi ON tb_presensi.personil_id = tb_personil.id
                WHERE tb_personil.pin_absen = ? AND tb_induk.bulan = ? AND tb_induk.tahun = ?", [$this->pinAbsen, $data['bulan'], $data['tahun']]);
            //admbil dari data backupan
            if ($data['backup']['count'] > 0 && isset($data['laporan']['final']) && $data['laporan']['final'] != '') {
                $this->tabelrekapc1bc($data);
                exit;
            }

            //ambil ttd
            if ($data['tingkat'] == 6 && $data['bulan'] == 1 && $data['tahun'] == 2018)
                $data['tingkat'] = 3;

            $data['tpp_pegawai'] = $this->laporan_service->getTpp($data['pegawai']['nipbaru']);
            $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], true);
            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];

            $this->subView('tabelrekapc1', $data);
        }
    }

    protected function tabelrekapc1_v2($input) {
        if ($input) {
            foreach ($input as $key => $i) {
                $data[$key] = $i;
            }

            $data['format'] = 'A';
            $data['jenis'] = '';
            $data['personil'] = $this->pinAbsen;
            $data['kdlokasi'] = $this->login['kdlokasi'];
            $data['satker'] = $this->laporan_service->getDataSatker($this->login['kdlokasi']);
            $data['pegawai'] = $this->laporan_service->getDataPersonil($this->pinAbsen);

            $data['laporan'] = $this->laporan_service->getLaporan($data);
            $data['backup'] = $this->backup_service->getData("SELECT tb_induk.* FROM tb_induk 
                JOIN tb_personil ON tb_personil.induk_id = tb_induk.id
                LEFT JOIN tb_presensi ON tb_presensi.personil_id = tb_personil.id
                WHERE tb_personil.pin_absen = ? AND tb_induk.bulan = ? AND tb_induk.tahun = ?", [$this->pinAbsen, $data['bulan'], $data['tahun']]);
            //admbil dari data backupan
            if ($data['backup']['count'] > 0 && isset($data['laporan']['final']) && $data['laporan']['final'] != '') {
                $this->tabelrekapc1bc($data);
                exit;
            }

            $data['tpp_pegawai'] = $this->laporan_service->getTpp_v2($input + ['nipbaru' => $data['pegawai']['nipbaru']]);
            $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], true);
            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];

//            comp\FUNC::showPre($data['tpp_pegawai']);
            $this->subView('tabelrekapc1_v2', $data);
        }
    }

    protected function tabelrekapc1_v3($input) {
        if ($input) {
            foreach ($input as $key => $i) {
                $data[$key] = $i;
            }

            $data['format'] = 'A';
            $data['jenis'] = '';
            $data['personil'] = $this->pinAbsen;
            $data['kdlokasi'] = $this->login['kdlokasi'];
            $data['satker'] = $this->laporan_service->getDataSatker($this->login['kdlokasi']);
            $data['pegawai'] = $this->laporan_service->getDataPersonil($this->pinAbsen);

            $data['laporan'] = $this->laporan_service->getLaporan($data);
            $data['backup'] = $this->backup_service->getData("SELECT tb_induk.* FROM tb_induk 
                JOIN tb_personil ON tb_personil.induk_id = tb_induk.id
                LEFT JOIN tb_presensi ON tb_presensi.personil_id = tb_personil.id
                WHERE tb_personil.pin_absen = ? AND tb_induk.bulan = ? AND tb_induk.tahun = ?", [$this->pinAbsen, $data['bulan'], $data['tahun']]);
            //admbil dari data backupan
            if ($data['backup']['count'] > 0 && isset($data['laporan']['final']) && $data['laporan']['final'] != '') {
                $this->tabelrekapc1bc($data);
                exit;
            }
            
            //ambil data kinerja
            $url = 'http://192.168.254.63/super/api/';
            $method = 'poin_pns';
            $accesskey = ['kinerja-key' => 'OFV6Y1NualM3dWZBRHZuaFhySDBVQWZYd29JNTZ0'];
            $request = array('pin' => $data['pegawai']['pin_absen'], 'tahun' => $input['tahun'], 'bulan' => $input['bulan']);
            $kinerja = $this->webadapter->callAPI($url, $method, $accesskey, $request);
            $poin = [];
            if (!empty($kinerja)) {
                $arrNip = array_column($kinerja['data'], 'nip');
                $arrPoin = array_column($kinerja['data'], 'poin');
                $poin = array_combine($arrNip, $arrPoin);
            }

            $data['kinerja'] = $poin;

            $data['tpp_pegawai'] = $this->laporan_service->getTpp_v2($input + ['nipbaru' => $data['pegawai']['nipbaru']]);
            $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], true);
//            comp\FUNC::showPre($data['kinerja']);//exit;
//            comp\FUNC::showPre($data['pegawai']);//exit;
            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];

            $this->subView('tabelrekapc1_v3', $data);
        }
    }

    protected function tabelrekapc2() {
        $input = $this->post(true);
        if ($input) {
            foreach ($input as $key => $i)
                $data[$key] = $i;

            $data['format'] = 'B';
            $data['jenis'] = '';
            $data['personil'] = $this->pinAbsen;
            $data['kdlokasi'] = $this->login['kdlokasi'];
            $data['satker'] = $this->laporan_service->getDataSatker($this->login['kdlokasi']);
            $data['pegawai'] = $this->laporan_service->getDataPersonil($this->pinAbsen);

            $data['laporan'] = $this->laporan_service->getLaporan($data);
            $data['backup'] = $this->backup_service->getData("SELECT tb_induk.* FROM tb_induk 
                JOIN tb_personil ON tb_personil.induk_id = tb_induk.id
                LEFT JOIN tb_presensi ON tb_presensi.personil_id = tb_personil.id
                WHERE tb_personil.pin_absen = ? AND tb_induk.bulan = ? AND tb_induk.tahun = ?", [$this->pinAbsen, $data['bulan'], $data['tahun']]);
            //admbil dari data backupan
            if ($data['backup']['count'] > 0 && isset($data['laporan']['final']) && $data['laporan']['final'] != '') {
                $this->tabelrekapc2bc($data);
                exit;
            }

            //ambil ttd
            if ($data['tingkat'] == 6 && $data['bulan'] == 1 && $data['tahun'] == 2018)
                $data['tingkat'] = 3;

            $data['laporan'] = $this->laporan_service->getLaporan($data);

            $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan']);
            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];

            $this->subView('tabelrekapc2', $data);
        }
    }

    /*     * *****************************START*AMBIL DATA BACKUP*************************************** */

    protected function tabelrekapc1bc($input) {
        if ($input) {
            $versi = $this->laporan_service->getDataVersi('history_of_report_tpp_rules', $input);
            switch ($versi['data_1']) {
                case 'v1':
                    $this->tabelrekapc1bc_v1($input, true);
                    break;
                case 'v2':
                    $this->tabelrekapc1bc_v2($input, true);
                    break;
                case 'v3':
                    $this->tabelrekapc1bc_v3($input, true);
                    break;
                default:
                    echo 'Versi laporan TPP tidak ditemukan!';
            }
        }
    }

    protected function tabelrekapc1bc_v1($input) {
        if ($input) {
            foreach ($input as $key => $i) :
                $data[$key] = $i;
            endforeach;

            $data['pin_absen'] = $this->pinAbsen;
            $data['induk'] = $data['backup']['value'][0];
            $data['satker'] = $data['induk']['singkatan_lokasi'];
            $data['pegawai'] = $this->backup_service->getDataPersonilBatch($data, true);
            $data['tpp'] = $this->backup_service->getDataTpp($data['induk']['id']);
            $data['format'] = 'A';
            $data['jenis'] = '';

            //ambil ttd
            if ($data['tingkat'] == 6 && $data['bulan'] == 1 && $data['tahun'] == 2018)
                $data['tingkat'] = 3;

            $data['laporan'] = $this->backup_service->getLaporan($data['induk']['id']);
            $check = $this->backup_service->getData("SELECT tb_presensi.* FROM tb_presensi 
                JOIN tb_personil ON tb_personil.id = tb_presensi.personil_id
                JOIN tb_induk ON tb_induk.id = tb_personil.induk_id
                WHERE tb_induk.id = " . $data['induk']['id'] . " AND tb_personil.pin_absen IN (" . $data['pin_absen'] . ")");

            if ($check['count'] > 0) {
                $data['rekapbc'] = $this->backup_service->getRekapAllView($data['induk']['id'], $data['pin_absen']);
            } else {
                $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], true);
                $data['tpp_pegawai'] = $this->laporan_service->getTpp($data['personil']);
            }

            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];

            $this->subView('tabelrekapc1bc_v1', $data);
        }
    }

    protected function tabelrekapc1bc_v2($input) {
        if ($input) {
            foreach ($input as $key => $i) :
                $data[$key] = $i;
            endforeach;

            $data['pin_absen'] = $this->pinAbsen;
            $data['induk'] = $data['backup']['value'][0];
            $data['satker'] = $data['induk']['singkatan_lokasi'];
            $data['pegawai'] = $this->backup_service->getDataPersonilBatch_v2($data, true);
            $data['tpp'] = $this->backup_service->getDataTpp($data['induk']['id']);
            $data['format'] = 'A';
            $data['jenis'] = '';

            //ambil ttd
            if ($data['tingkat'] == 6 && $data['bulan'] == 1 && $data['tahun'] == 2018) {
                $data['tingkat'] = 3;
            }

            $data['laporan'] = $this->backup_service->getLaporan($data['induk']['id']);
            $check = $this->backup_service->getData("SELECT tb_presensi.* FROM tb_presensi 
                JOIN tb_personil ON tb_personil.id = tb_presensi.personil_id
                JOIN tb_induk ON tb_induk.id = tb_personil.induk_id
                WHERE tb_induk.id = " . $data['induk']['id'] . " AND tb_personil.pin_absen IN (" . $data['pin_absen'] . ")");

            if ($check['count'] > 0) {
                $data['rekapbc'] = $this->backup_service->getRekapAllView($data['induk']['id'], $data['pin_absen']);
            } else {
                $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], true);
                $data['tpp_pegawai'] = $this->laporan_service->getTpp($data['personil']);
            }

            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];

            $this->subView('tabelrekapc1bc_v2', $data);
        }
    }
    
    protected function tabelrekapc1bc_v3($input) {
        if ($input) {
            foreach ($input as $key => $i) :
                $data[$key] = $i;
            endforeach;

            $data['pin_absen'] = $this->pinAbsen;
            $data['induk'] = $data['backup']['value'][0];
            $data['satker'] = $data['induk']['singkatan_lokasi'];
            $data['pegawai'] = $this->backup_service->getDataPersonilBatch_v2($data, true);
            $data['tpp'] = $this->backup_service->getDataTpp($data['induk']['id']);
            $data['format'] = 'A';
            $data['jenis'] = '';

            //ambil ttd
            if ($data['tingkat'] == 6 && $data['bulan'] == 1 && $data['tahun'] == 2018) {
                $data['tingkat'] = 3;
            }

            $data['laporan'] = $this->backup_service->getLaporan($data['induk']['id']);
            $check = $this->backup_service->getData("SELECT tb_presensi.* FROM tb_presensi 
                JOIN tb_personil ON tb_personil.id = tb_presensi.personil_id
                JOIN tb_induk ON tb_induk.id = tb_personil.induk_id
                WHERE tb_induk.id = " . $data['induk']['id'] . " AND tb_personil.pin_absen IN (" . $data['pin_absen'] . ")");

            if ($check['count'] > 0) {
                $data['rekapbc'] = $this->backup_service->getRekapAllView($data['induk']['id'], $data['pin_absen']);
            } else {
                $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], true);
                $data['tpp_pegawai'] = $this->laporan_service->getTpp($data['personil']);
            }

            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];
            $this->subView('tabelrekapc1bc_v3', $data);
        }
    }

    protected function tabelrekapc2bc($input) {
        if ($input) {
            foreach ($input as $key => $i)
                $data[$key] = $i;

            $data['pin_absen'] = $this->pinAbsen;
            $data['induk'] = $data['backup']['value'][0];
            $data['satker'] = $data['induk']['singkatan_lokasi'];
            $data['pegawai'] = $this->backup_service->getDataPersonilBatch_v2($data, true);
            $data['format'] = 'B';
            $data['jenis'] = '';

            //ambil ttd
            if ($data['tingkat'] == 6 && $data['bulan'] == 1 && $data['tahun'] == 2018) {
                $data['tingkat'] = 3;
            }

            $data['laporan'] = $this->backup_service->getLaporan($data['induk']['id']);
            $check = $this->backup_service->getData("SELECT tb_presensi.* FROM tb_presensi 
                JOIN tb_personil ON tb_personil.id = tb_presensi.personil_id
                JOIN tb_induk ON tb_induk.id = tb_personil.induk_id
                WHERE tb_induk.id = " . $data['induk']['id'] . " AND tb_personil.pin_absen IN (" . $data['pin_absen'] . ")");

            if ($check['count'] > 0) {
                $data['rekapbc'] = $this->backup_service->getRekapAllView($data['induk']['id'], $data['pin_absen']);
            } else {
                $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], true);
            }

            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];
//            comp\FUNC::showPre($data);exit;

            $this->subView('tabelrekapc2bc', $data);
        }
    }

    /*     * *****************************START*AMBIL DATA BACKUP*************************************** */

    public function script() {
        $data['title'] = '<!-- Script -->';
        $this->subView('script', $data);
    }

}
