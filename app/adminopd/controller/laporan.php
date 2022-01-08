<?php

namespace app\adminopd\controller;

use app\adminopd\model\servicemain;
use app\adminopd\model\laporan_service;
use app\adminopd\model\pegawai_service;
use app\adminopd\model\backup_service;
use app\adminsistem\model\webadapter;
use system;
use comp;

class laporan extends system\Controller {

    public function __construct() {
        parent::__construct();
        $this->servicemain = new servicemain();
        $session = $this->servicemain->cekSession();

        if ($session['status'] === true) {
            $this->laporan_service = new laporan_service();
            $this->pegawai_service = new pegawai_service();
            $this->backup_service = new backup_service();
            $this->webadapter = new webadapter();

            $this->setSession('SESSION_LOGIN', $session['data']);
            $this->login = $this->getSession('SESSION_LOGIN');
            $satker = $this->laporan_service->getPilLokasi();
            $this->satker = $satker[$this->login['kdlokasi']];
        } else {
            $this->setSession('SESSION_RELOAD', true);
            $this->redirect($this->link('login'));
        }
    }

    protected function index() {
        $data['title'] = 'Verifikasi Laporan';
        $data['breadcrumb'] = '<a href="' . $this->link() . '" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Verifikasi Laporan</a>';

        $data['satker'] = $this->satker;
        $data['listTahun'] = comp\FUNC::numbSeries('2018', date('Y'));
        $data['bendahara'] = $this->laporan_service->getBendahara($this->login['kdlokasi']);
        $this->showView('index', $data, 'theme_admin');
    }

    protected function verifikasi() {
        $input = $this->post(true);
        if ($input) {
            $input['kdlokasi'] = $this->login['kdlokasi'];
            $input['satker'] = $this->pegawai_service->getDataSatker($this->login['kdlokasi']);
            foreach ($input as $key => $i) {
                $data[$key] = $i;
            }

            $data['laporan'] = $this->laporan_service->getLaporan($data);
            if (isset($data['laporan']['final'])) {
                $data['tingkat'] = 6;
            }

            $data['induk'] = $this->backup_service->getDataInduk($data);
            //admbil dari data backupan
            if ($data['induk'] && isset($data['laporan']['final']) && $data['laporan']['final'] != '') {
                $this->verifikasibc($data);
                exit;
            }

            $data['pegawai'] = $this->laporan_service->getDataPersonilSatker($data);
            $data['personil'] = '';
            if ($data['pegawai']['count'] > 0) {
                $personil = array_map(function ($i) {
                    return $i['pin_absen'];
                }, $data['pegawai']['value']);

                $data['personil'] = implode(',', $personil);
            }

            $data['format'] = 'A';
            $data['jenis'] = '';
            $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], true);

            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];

            $this->subView('verifikasi', $data);

            // tampil hanya untuk user gania1,, pengecekan
            if ($this->login['username'] == 'acil@adminopd') {
                comp\FUNC::showPre($data);
            }
        }
    }

    protected function laporanfinal() {
        $data['title'] = 'Laporan Final';
        $data['breadcrumb'] = '<a href="' . $this->link() . '" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Laporan Final</a>';

        $data['satker'] = $this->satker;
        $data['bendahara'] = $this->laporan_service->getBendahara($this->login['kdlokasi']);
        $this->showView('final', $data, 'theme_admin');
    }

    protected function indexold() {
        $data['title'] = 'Laporan';
        $data['breadcrumb'] = '<a href="' . $this->link() . '" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Laporan</a>';

        $data['satker'] = $this->satker;
        $data['bendahara'] = $this->laporan_service->getBendahara($this->login['kdlokasi']);
        $this->showView('indexold', $data, 'theme_admin');
    }

    protected function cetak() {
        $data['title'] = 'Cetak Laporan';
        $data['breadcrumb'] = '<a href="' . $this->link() . '" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Cetak Laporan</a>';

        $data['satker'] = $this->satker;
        $data['listTahun'] = comp\FUNC::numbSeries('2018', date('Y'));
        $this->showView('cetak', $data, 'theme_admin');
    }

    protected function tabelapelold() {
        $input = $this->post(true);
        if ($input) {
            $input['kdlokasi'] = $this->login['kdlokasi'];
            $data['pegawai'] = $this->laporan_service->getDataPersonilSatker($input);
            $input['personil'] = '';
            if ($data['pegawai']['count'] > 0) {
                $personil = array_map(function ($i) {
                    return $i['pin_absen'];
                }, $data['pegawai']['value']);

                $input['personil'] = implode(',', $personil);
            }

            foreach ($input as $key => $i) :
                $data[$key] = $i;
            endforeach;

            $data['satker'] = $this->satker;
            //$data['rekap'] = $this->apelpagi_service->getRecordPersonil($input);
            $data['rekap'] = $this->apelpagi_service->getRecordApel($input);
            $data['libur'] = $this->laporan_service->getLibur($input);

            //ambil ttd
            if ($data['tingkat'] == 6 && $data['bulan'] == 1 && $data['tahun'] == 2018) :
                $data['tingkat'] = 3;
            endif;

            $data['laporan'] = $this->laporan_service->getLaporan($input);
            //ambil moderasi
            $data['moderasi'] = $this->laporan_service->getArraymod($input, $data['laporan']);
            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];

            // added by husnanw
            // You may add the other officers like kepala opd, admin kota, and kep bkppd
            // CAUTION: Remove these comment lines for real implementation
            //$data["hwTtdAdminOpd"] = $this->laporan_service->getHusnanWTtd($this->login["nipbaru"]);
            //$data["hwStempelAdminOpd"] = $this->laporan_service->getHusnanWStempel($this->login["kdlokasi"]);
            // CAUTION: These scripts are only for example. Remove the scripts when implementing real implementation
            $data["hwTtdAdminOpd"] = $this->laporan_service->getHusnanWTtd("196112301986111001");
            $data["hwStempelAdminOpd"] = $this->laporan_service->getHusnanWStempel("G12002");

            $this->subView('tabelapelold', $data);
        }
    }

    protected function tabelmasukold() {
        $input = $this->post(true);
        if ($input) {
            $input['kdlokasi'] = $this->login['kdlokasi'];
            foreach ($input as $key => $i) :
                $data[$key] = $i;
            endforeach;

            $data['pegawai'] = $this->laporan_service->getDataPersonilSatker($input);
            $data['personil'] = '';
            if ($data['pegawai']['count'] > 0) {
                $personil = array_map(function ($i) {
                    return $i['pin_absen'];
                }, $data['pegawai']['value']);

                $data['personil'] = implode(',', $personil);
            }

            $data['rekap'] = $this->laporan_service->getLogSatker($data);
            $data['libur'] = $this->laporan_service->getLibur($input);
            $data['satker'] = $this->satker;

            //ambil ttd
            if ($data['tingkat'] == 6 && $data['bulan'] == 1 && $data['tahun'] == 2018) :
                $data['tingkat'] = 3;
            endif;

            $data['laporan'] = $this->laporan_service->getLaporan($input);
            //ambil moderasi
            $data['moderasi'] = $this->laporan_service->getArraymod($input, $data['laporan']);
            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];

            $this->subView('tabelmasukold', $data);
        }
    }

    protected function tabelpresensi() {
        $input = $this->post(true);
        if ($input) {
            $versi = $this->laporan_service->getDataVersi('history_of_report_rules', $input);
            switch ($versi['data_1']) {
                case 'v1':
                    $this->tabelpresensi_v1($input, true);
                    break;
                case 'v2':
                    $this->tabelpresensi_v2($input, true);
                    break;
            }
        }
    }

    protected function tabelpresensi_v1($input, $verified) {
        if ($verified) {
            $input['kdlokasi'] = $this->login['kdlokasi'];
            $input['satker'] = $this->satker;
            foreach ($input as $key => $i) :
                $data[$key] = $i;
            endforeach;

            $data['laporan'] = $this->laporan_service->getLaporan($data);
            $data['induk'] = $this->backup_service->getDataInduk($input);
            //admbil dari data backupan
            if ($data['induk'] && isset($data['laporan']['final']) && $data['laporan']['final'] != '') {
                $this->tabelpresensibc($input);
                exit;
            }

            $data['pegawai'] = $this->laporan_service->getDataPersonilSatker($input);
            $data['personil'] = '';
            if ($data['pegawai']['count'] > 0) {
                $personil = array_map(function ($i) {
                    return $i['pin_absen'];
                }, $data['pegawai']['value']);

                $data['personil'] = implode(',', $personil);
            }

//            comp\FUNC::showPre($laporan);exit;

            if ($data['jenis'] == 1) :
                $view = 'tabelmasuk';
            elseif ($data['jenis'] == 2) :
                $view = 'tabelapel';
            elseif ($data['jenis'] == 3) :
                $view = 'tabelpulang';
            endif;

            $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], true);
            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];

            $this->subView($view, $data);
        }
    }

    protected function tabelpresensi_v2($input, $verified) {
        if ($verified) {
            $input['kdlokasi'] = $this->login['kdlokasi'];
            foreach ($input as $key => $i) :
                $data[$key] = $i;
            endforeach;

            $data['laporan'] = $this->laporan_service->getLaporan($data);
            $data['induk'] = $this->backup_service->getDataInduk($input);
            //admbil dari data backupan
            if ($data['induk'] && isset($data['laporan']['final']) && $data['laporan']['final'] != '') {
                $this->tabelpresensibc_v2($input);
                exit;
            }

            $data['satker'] = $this->pegawai_service->getDataSatker($this->login['kdlokasi']);
            $data['pegawai'] = $this->laporan_service->getDataPersonilSatker_v2($input);

            $arrPin = array_column($data['pegawai']['value'], 'pin_absen');
            $data['personil'] = implode(',', $arrPin);

            if ($data['jenis'] == 1) :
                $view = 'tabelmasuk';
            elseif ($data['jenis'] == 2) :
                $view = 'tabelapel';
            elseif ($data['jenis'] == 3) :
                $view = 'tabelpulang';
            endif;


            $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], true);
            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];

            $this->subView($view, $data);
        }
    }

    protected function tabelpulangold() {
        $input = $this->post(true);
        if ($input) {
            $input['kdlokasi'] = $this->login['kdlokasi'];
            foreach ($input as $key => $i) :
                $data[$key] = $i;
            endforeach;

            $data['pegawai'] = $this->laporan_service->getDataPersonilSatker($input);
            $data['personil'] = '';
            if ($data['pegawai']['count'] > 0) {
                $personil = array_map(function ($i) {
                    return $i['pin_absen'];
                }, $data['pegawai']['value']);

                $data['personil'] = implode(',', $personil);
            }

            $data['rekap'] = $this->laporan_service->getLogSatker($data);
            $data['libur'] = $this->laporan_service->getLibur($input);
            $data['satker'] = $this->satker;

            //ambil ttd
            if ($data['tingkat'] == 6 && $data['bulan'] == 1 && $data['tahun'] == 2018) :
                $data['tingkat'] = 3;
            endif;

            $data['laporan'] = $this->laporan_service->getLaporan($input);
            //ambil moderasi
            $data['moderasi'] = $this->laporan_service->getArraymod($input, $data['laporan']);
            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];

            $this->subView('tabelpulangold', $data);
        }
    }

    protected function individu() {
        $data['title'] = 'Laporan Individu';
        $data['breadcrumb'] = '<a href="' . $this->link() . '" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Laporan Individu</a>';

        $data['satker'] = $this->satker;
        $this->showView('individu', $data, 'theme_admin');
    }

    protected function tabelpersonil() {
        $input = $this->post(true);
        if ($input) {
            $versi = $this->laporan_service->getDataVersi('history_of_report_rules', $input);
            switch ($versi['data_1']) {
                case 'v1':
                    $this->tabelpersonil_v1($input, true);
                    break;
                case 'v2':
                    $this->tabelpersonil_v2($input, true);
                    break;
            }
        }
    }

    protected function tabelpersonil_v1($input, $verified) {
        if ($verified) {
            $input['kdlokasi'] = $this->login['kdlokasi'];
            foreach ($input as $key => $i) :
                $data[$key] = $i;
            endforeach;

            $data['laporan'] = $this->laporan_service->getLaporan($data);
            $data['induk'] = $this->backup_service->getDataInduk($input);
            //admbil dari data backupan
            if ($data['induk'] && isset($data['laporan']['final']) && $data['laporan']['final'] != '') {
                $this->tabelpersonilbc_v1($input);
                exit;
            }

            $data['dataTabel'] = $this->laporan_service->getTabelPersonil($input);
            $data['satker'] = $this->satker;
            $this->subView('tabelpersonil', $data);
        }
    }

    protected function tabelpersonil_v2($input, $verified) {
        if ($verified) {
            $input['kdlokasi'] = $this->login['kdlokasi'];
            foreach ($input as $key => $i) :
                $data[$key] = $i;
            endforeach;

            $data['laporan'] = $this->laporan_service->getLaporan($data);
            $data['induk'] = $this->backup_service->getDataInduk($input);
            //admbil dari data backupan
            if ($data['induk'] && isset($data['laporan']['final']) && $data['laporan']['final'] != '') {
                $this->tabelpersonilbc_v2($input);
                exit;
            }

            $data['dataTabel'] = $this->laporan_service->getTabelPersonil_v2($input);
            $data['satker'] = $this->satker;
            $this->subView('tabelpersonil', $data);
        }
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

    private function tabelrekapc1_v1($input, $verified) {
        if ($verified) {
            $input['kdlokasi'] = $this->login['kdlokasi'];
            $input['satker'] = $this->pegawai_service->getDataSatker($this->login['kdlokasi']);
            foreach ($input as $key => $i) :
                $data[$key] = $i;
            endforeach;

            $data['laporan'] = $this->laporan_service->getLaporan($input);
            $data['induk'] = $this->backup_service->getDataInduk($input);
            //admbil dari data backupan
            if ($data['induk'] && isset($data['laporan']['final']) && $data['laporan']['final'] != '') {
                $this->tabelrekapc1bc($input);
                exit;
            }

            $data['pegawai'] = $this->laporan_service->getDataPersonilBatch($input['pin_absen'], true);
            $data['personil'] = '';
            if ($data['pegawai']['count'] > 0) {
                $personil = array_map(function ($i) {
                    return $i['nipbaru'];
                }, $data['pegawai']['value']);

                $data['personil'] = implode(',', $personil);
            }
            $data['tpp_pegawai'] = $this->laporan_service->getTpp($data['personil']);

            $data['format'] = 'A';
            $data['jenis'] = '';
            $data['personil'] = $input['pin_absen'];

            //ambil ttd
            if ($data['tingkat'] == 6 && $data['bulan'] == 1 && $data['tahun'] == 2018) :
                $data['tingkat'] = 3;
            endif;

            $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], true);
            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];
            $this->subView('tabelrekapc1_v1', $data);
        }
    }

    private function tabelrekapc1_v2($input, $verified) {
        if ($verified) {
            $input['kdlokasi'] = $this->login['kdlokasi'];
            $input['satker'] = $this->pegawai_service->getDataSatker($this->login['kdlokasi']);
            foreach ($input as $key => $i) :
                $data[$key] = $i;
            endforeach;

            $data['laporan'] = $this->laporan_service->getLaporan($input);
            $data['induk'] = $this->backup_service->getDataInduk($input);
            //admbil dari data backupan
            if ($data['induk'] && isset($data['laporan']['final']) && $data['laporan']['final'] != '') {
                $this->tabelrekapc1bc($input);
                exit;
            }

            $data['pegawai'] = $this->laporan_service->getDataPersonilBatch($input['pin_absen'], true);
            $data['personil'] = '';
            if ($data['pegawai']['count'] > 0) {
                $personil = array_map(function ($i) {
                    return $i['nipbaru'];
                }, $data['pegawai']['value']);

                $data['personil'] = implode(',', $personil);
                $input['nipbaru'] = $data['pegawai']['value'][0]['nipbaru'];
            }
            $data['tpp_pegawai'] = $this->laporan_service->getTpp_v2($input);

            $data['format'] = 'A';
            $data['jenis'] = '';
            $data['personil'] = $input['pin_absen'];

            //ambil ttd
            if ($data['tingkat'] == 6 && $data['bulan'] == 1 && $data['tahun'] == 2018) :
                $data['tingkat'] = 3;
            endif;

            $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], true);
            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];
            $this->subView('tabelrekapc1_v2', $data);
        }
    }
    
    private function tabelrekapc1_v3($input, $verified) {
        if ($verified) {
            $input['kdlokasi'] = $this->login['kdlokasi'];
            $input['satker'] = $this->pegawai_service->getDataSatker($this->login['kdlokasi']);
            foreach ($input as $key => $i) :
                $data[$key] = $i;
            endforeach;

            $data['laporan'] = $this->laporan_service->getLaporan($input);
            $data['induk'] = $this->backup_service->getDataInduk($input);
            //admbil dari data backupan
//            if ($data['induk'] && isset($data['laporan']['final']) && $data['laporan']['final'] != '') {
//                $this->tabelrekapc1bc($input);
//                exit;
//            }

            $data['pegawai'] = $this->laporan_service->getDataPersonilBatch($input['pin_absen'], true);
            $data['personil'] = '';
            if ($data['pegawai']['count'] > 0) {
                $personil = array_map(function ($i) {
                    return $i['nipbaru'];
                }, $data['pegawai']['value']);

                $data['personil'] = implode(',', $personil);
                $input['nipbaru'] = $data['pegawai']['value'][0]['nipbaru'];
            }
            $data['tpp_pegawai'] = $this->laporan_service->getTpp_v2($input);

            $data['format'] = 'A';
            $data['jenis'] = '';
            $data['personil'] = $input['pin_absen'];

            ###ambil ttd
            if ($data['tingkat'] == 6 && $data['bulan'] == 1 && $data['tahun'] == 2018) :
                $data['tingkat'] = 3;
            endif;

            $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], true);
            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];
            $this->subView('tabelrekapc1_v3', $data);
        }
    }

    protected function tabelrekapc2() {
        $input = $this->post(true);
        if ($input) {
            $versi = $this->laporan_service->getDataVersi('history_of_report_rules', $input);
            switch ($versi['data_1']) {
                case 'v1':
                    $this->tabelrekapc2_v1($input, true);
                    break;
                case 'v2':
                    $this->tabelrekapc2_v1($input, true);
                    break;
            }
        }
    }

    protected function tabelrekapc2_v1() {
        $input = $this->post(true);
        if ($input) {
            $input['kdlokasi'] = $this->login['kdlokasi'];
            $input['satker'] = $this->pegawai_service->getDataSatker($this->login['kdlokasi']);
            foreach ($input as $key => $i) :
                $data[$key] = $i;
            endforeach;

            $data['laporan'] = $this->laporan_service->getLaporan($input);
            $data['induk'] = $this->backup_service->getDataInduk($input);
            //admbil dari data backupan
            if ($data['induk'] && isset($data['laporan']['final']) && $data['laporan']['final'] != '') {
                $this->tabelrekapc2bc($input);
                exit;
            }

            $data['pegawai'] = $this->laporan_service->getDataPersonilBatch($input['pin_absen'], true);

            $data['format'] = 'B';
            $data['jenis'] = '';
            $data['personil'] = $input['pin_absen'];

            ###ambil ttd
            if ($data['tingkat'] == 6 && $data['bulan'] == 1 && $data['tahun'] == 2018) :
                $data['tingkat'] = 3;
            endif;

            $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan']);
            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];

            $this->subView('tabelrekapc2_v1', $data);
        }
    }

    protected function tpp() {
        $data['title'] = 'Penerimaan TPP';
        $data['breadcrumb'] = '<a href="' . $this->link() . '" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Penerimaan TPP</a>';

        $data['satker'] = $this->satker;
        $data['bendahara'] = $this->laporan_service->getBendahara($this->login['kdlokasi']);
        $data['username'] = $this->login['username'];
        $data['listTahun'] = comp\FUNC::numbSeries('2018', date('Y'));
        $this->showView('tpp', $data, 'theme_admin');
    }

    protected function tabeltpp() {
        ini_set('memory_limit', '-1');
        $input = $this->post(true);
        if ($input) {
            $versi = $this->laporan_service->getDataVersi('history_of_report_tpp_rules', $input);
            switch ($versi['data_1']) {
                case 'v1':
                    $this->tabeltpp_v1($input, true);
                    break;
                case 'v2':
                    $this->tabeltpp_v2($input, true);
                    break;
                case 'v3':
                    $this->tabeltpp_v3($input, true);
                    break;
            }
        }
    }

    protected function tabeltpp_v1($input, $verified) {
        if ($verified) {
            $input['kdlokasi'] = $this->login['kdlokasi'];
            $input['satker'] = $this->pegawai_service->getDataSatker($this->login['kdlokasi']);
            foreach ($input as $key => $i) :
                $data[$key] = $i;
            endforeach;

            $data['laporan'] = $this->laporan_service->getLaporan($data);
            $data['induk'] = $this->backup_service->getDataInduk($input);
            //admbil dari data backupan
            if ($data['induk'] && isset($data['laporan']['final']) && $data['laporan']['final'] != '') {
                $this->tabeltppbc($input);
                exit;
            }

            $data['pegawai'] = $this->laporan_service->getDataPersonilTpp($input);
            $data['personil'] = '';
            if ($data['pegawai']['count'] > 0) {
                $personil = array_map(function ($i) {
                    return $i['pin_absen'];
                }, $data['pegawai']['value']);

                $data['personil'] = implode(',', $personil);
            }

            //ambil tambahan data pilih bendahara
            $data['pilbendahara'] = $this->laporan_service->getDataPersonilSatker(['kdlokasi' => $input['kdlokasi']])['value'];
            $get = $this->pegawai_service->getData('SELECT kdlokasi_parent FROM tref_lokasi_kerja WHERE kdlokasi = "' . $input['kdlokasi'] . '" LIMIT 1', []);
            if ($get['count'] == 1 && !empty($get['value'][0]['kdlokasi_parent']) && $get['value'][0]['kdlokasi_parent']) {
                $parent = $get['value'][0]['kdlokasi_parent'];
                $data['pilbendahara'] += $this->laporan_service->getDataPersonilSatker(['kdlokasi' => $parent])['value'];
            }

            //bulan jan dn feb masih uji coba
            $period = $data['bulan'] . $data['tahun'];
            if ($period == '12018' || $period == '22018') {
                $data['tingkat'] = 6;
            }

            $data['kenabpjs'] = $this->laporan_service->getDataSetting('maks_tpp_kena_bpjs');
            $data['pajak'] = $this->laporan_service->getArraypajak();
            $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], true);
            $data['bendahara'] = $this->laporan_service->getBendahara($input['kdlokasi']);
            $data['kepala'] = $this->laporan_service->getKepala($input['kdlokasi']);

            $this->subView('tabeltpp', $data);
        }
    }

    protected function tabeltpp_v2($input, $verified) {
        if ($verified) {
            $input['kdlokasi'] = $this->login['kdlokasi'];
            $input['satker'] = $this->pegawai_service->getDataSatker($this->login['kdlokasi']);
            foreach ($input as $key => $i) :
                $data[$key] = $i;
            endforeach;

            $data['laporan'] = $this->laporan_service->getLaporan($data);
            $data['induk'] = $this->backup_service->getDataInduk($input);
            //admbil dari data backupan
            if ($data['induk'] && isset($data['laporan']['final']) && $data['laporan']['final'] != '') {
                $this->tabeltppbc_v2($input);
                exit;
            }

            $data['pegawai'] = $this->laporan_service->getDataPersonilTpp_v2($input);

            $data['personil'] = '';
            if ($data['pegawai']['count'] > 0) {
                $personil = array_map(function ($i) {
                    return $i['pin_absen'];
                }, $data['pegawai']['value']);

                $data['personil'] = implode(',', $personil);
            }

            //ambil tambahan data pilih bendahara
            $bendahara_satker = $this->laporan_service->getDataPersonilSatker(['kdlokasi' => $input['kdlokasi']])['value'];
            $get = $this->pegawai_service->getData('SELECT kdlokasi_parent FROM tref_lokasi_kerja WHERE kdlokasi = "' . $input['kdlokasi'] . '" LIMIT 1', []);
            if ($get['count'] == 1 && !empty($get['value'][0]['kdlokasi_parent']) && $get['value'][0]['kdlokasi_parent']) {
                $parent = $get['value'][0]['kdlokasi_parent'];
                $bendahara_parent = $this->laporan_service->getDataPersonilSatker(['kdlokasi' => $parent])['value'];
            }

            $data['kenabpjs'] = $this->laporan_service->getDataSetting('maks_tpp_kena_bpjs');
            $data['pajak'] = $this->laporan_service->getArraypajak();
            $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], true);
            $data['bendahara'] = $this->laporan_service->getBendahara($input['kdlokasi']);
            $data['kepala'] = $this->laporan_service->getKepala($input['kdlokasi']);
            $data['pilbendahara'] = (isset($bendahara_parent)) ? array_merge($bendahara_satker, $bendahara_parent) : $bendahara_satker;
            $this->subView('tabeltpp_v2', $data);
        }
    }

    protected function tabeltpp_v3($input, $verified) {
        if ($verified) {
            $input['kdlokasi'] = $this->login['kdlokasi'];
            $input['satker'] = $this->pegawai_service->getDataSatker($this->login['kdlokasi']);
            foreach ($input as $key => $i) :
                $data[$key] = $i;
            endforeach;

            $data['laporan'] = $this->laporan_service->getLaporan($data);
            $data['induk'] = $this->backup_service->getDataInduk($input);
            //ambil dari data backupan
            if ($data['induk'] && isset($data['laporan']['final']) && $data['laporan']['final'] != '') {
                $this->tabeltppbc_v3($input);
                exit;
            }

            $data['pegawai'] = $this->laporan_service->getDataPersonilTpp_v2($input);
//            comp\FUNC::showpre($data['pegawai']);exit;

            $data['personil'] = '';
            if ($data['pegawai']['count'] > 0) {
                $personil = array_map(function ($i) {
                    return $i['pin_absen'];
                }, $data['pegawai']['value']);

                $data['personil'] = implode(',', $personil);
            }

            //ambil tambahan data pilih bendahara
            $bendahara_satker = $this->laporan_service->getDataPersonilSatker(['kdlokasi' => $input['kdlokasi']])['value'];
            $get = $this->pegawai_service->getData('SELECT kdlokasi_parent FROM tref_lokasi_kerja WHERE kdlokasi = "' . $input['kdlokasi'] . '" LIMIT 1', []);
            if ($get['count'] == 1 && !empty($get['value'][0]['kdlokasi_parent']) && $get['value'][0]['kdlokasi_parent']) {
                $parent = $get['value'][0]['kdlokasi_parent'];
                $bendahara_parent = $this->laporan_service->getDataPersonilSatker(['kdlokasi' => $parent])['value'];
            }

            //ambil data kinerja
            $url = 'http://192.168.254.63/super/api/';
            $method = 'poin_pns';
            $accesskey = ['kinerja-key' => 'OFV6Y1NualM3dWZBRHZuaFhySDBVQWZYd29JNTZ0'];
            $request = array('pin' => $data['personil'], 'tahun' => $input['tahun'], 'bulan' => $input['bulan']);
            $kinerja = $this->webadapter->callAPI($url, $method, $accesskey, $request);
            $poin = [];
            if ($kinerja['status'] == true) {
                $arrNip = array_column($kinerja['data'], 'nip');
                $arrPoin = array_column($kinerja['data'], 'poin');
                $poin = array_combine($arrNip, $arrPoin);
            }

            $data['kinerja'] = $poin;

            $data['kenabpjs'] = $this->laporan_service->getDataSetting('maks_tpp_kena_bpjs');
            $data['pajak'] = $this->laporan_service->getArraypajak();
            $data['rekap'] = $this->laporan_service->getRekapAll_v3($data, $data['laporan'], true);
            
            $data['bendahara'] = $this->laporan_service->getBendahara($input['kdlokasi']);
            $data['kepala'] = $this->laporan_service->getKepala($input['kdlokasi']);

            // bendahara berdasarkan kdlokasi & kdlokasi_parent
            // $data['pilbendahara'] = (isset($bendahara_parent)) ? array_merge($bendahara_satker, $bendahara_parent) : $bendahara_satker;
            
            // bendahara berdasarkan lokasi_induk tb_bendahara
            $data['pilbendahara'] = $this->laporan_service->getDataPersonilSatker(['kdlokasi' => $data['bendahara']['lokasi_induk']])['value'];

            $this->subView('tabeltpp_v3', $data);
        }
    }

    protected function tpp13() {
        $data['title'] = 'Penerimaan TPP Ke-13';
        $data['breadcrumb'] = '<a href="' . $this->link() . '" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Penerimaan TPP Ke-13</a>';

        $data['satker'] = $this->satker;
        $data['bendahara'] = $this->laporan_service->getBendahara($this->login['kdlokasi']);
        $this->showView('tpp13', $data, 'theme_admin');
    }

    protected function tabeltpp13() {
        $input = $this->post(true);
        if ($input) {
            $input['kdlokasi'] = $this->login['kdlokasi'];
            $input['satker'] = $this->satker;
            foreach ($input as $key => $i) :
                $data[$key] = $i;
            endforeach;

            $data['pegawai'] = $this->laporan_service->getDataPersonilTpp($input);
            $data['personil'] = '';
            if ($data['pegawai']['count'] > 0) {
                $personil = array_map(function ($i) {
                    return $i['pin_absen'];
                }, $data['pegawai']['value']);

                $data['personil'] = implode(',', $personil);
            }

            //ambil tambahan data pilih bendahara
            $data['pilbendahara'] = [];
            $get = $this->pegawai_service->getData('SELECT kdlokasi_parent FROM tref_lokasi_kerja WHERE kdlokasi = "' . $input['kdlokasi'] . '" LIMIT 1', []);
            if ($get['count'] == 1 && !empty($get['value'][0]['kdlokasi_parent']) && $get['value'][0]['kdlokasi_parent']) {
                $parent = $get['value'][0]['kdlokasi_parent'];
                $data['pilbendahara'] = $this->laporan_service->getDataPersonilSatker(['kdlokasi' => $parent])['value'];
            }

            if ($data['tahun'] == 2018) :
                $data['bulan'] = 5; //tpp13 thn 2018 bln mei
            endif;
            $data['tingkat'] = 6;

            $data['pajak'] = $this->laporan_service->getArraypajak();
            $data['laporan'] = $this->laporan_service->getLaporan($data);
            $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], true);
            $data['bendahara'] = $this->laporan_service->getBendahara($input['kdlokasi']);
            $data['kepala'] = $this->laporan_service->getKepala($input['kdlokasi']);

            $this->subView('tabeltpp13', $data);
        }
    }

    protected function tpp14() {
        $data['title'] = 'Penerimaan TPP Ke-14';
        $data['breadcrumb'] = '<a href="' . $this->link() . '" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Penerimaan TPP Ke-14</a>';

        $data['satker'] = $this->satker;
        $data['bendahara'] = $this->laporan_service->getBendahara($this->login['kdlokasi']);
        $this->showView('tpp14', $data, 'theme_admin');
    }

    protected function tabeltpp14() {
        $input = $this->post(true);
        if ($input) {
            $input['kdlokasi'] = $this->login['kdlokasi'];
            $input['satker'] = $this->satker;
            foreach ($input as $key => $i) :
                $data[$key] = $i;
            endforeach;

            if ($data['tahun'] == 2019) :
                $data['bulan'] = 4; //tpp13 thn 2019 bln april
            endif;
            $data['tingkat'] = 6;

            $data['induk'] = $this->backup_service->getDataInduk($data);
            if (!$data['induk']) {
                $this->subView('notfound', $data);
                exit;
            }

            $data['satker'] = $data['induk']['singkatan_lokasi'];
            $data['pegawai'] = $this->backup_service->getDataPersonilBatch($data, true);
            $data['personil'] = '';
            if ($data['pegawai']['count'] > 0) {
                $personil = array_map(function ($i) {
                    return $i['pin_absen'];
                }, $data['pegawai']['value']);

                $data['personil'] = implode(',', $personil);
            }

            $data['tpp'] = $this->backup_service->getDataTpp($data['induk']['id']);
            $data['laporan'] = $this->backup_service->getLaporan($data['induk']['id']);
            $data['rekap'] = $this->backup_service->getRekapAllView($data['induk']['id']);

            $data['bendahara'] = $this->laporan_service->getBendahara($input['kdlokasi']);
            $data['kepala'] = $this->laporan_service->getKepala($input['kdlokasi']);

            //ambil tambahan data pilih bendahara
            $data['pilbendahara'] = [];
            $get = $this->pegawai_service->getData('SELECT kdlokasi_parent FROM tref_lokasi_kerja WHERE kdlokasi = "' . $input['kdlokasi'] . '" LIMIT 1', []);
            if ($get['count'] == 1 && !empty($get['value'][0]['kdlokasi_parent']) && $get['value'][0]['kdlokasi_parent']) {
                $parent = $get['value'][0]['kdlokasi_parent'];
                $data['pilbendahara'] = $this->laporan_service->getDataPersonilSatker(['kdlokasi' => $parent])['value'];
            }

            $this->subView('tabeltpp14bc', $data);
        }
    }

    protected function tabeltpp14old() {
        $input = $this->post(true);
        if ($input) {
            $input['kdlokasi'] = $this->login['kdlokasi'];
            $input['satker'] = $this->satker;
            foreach ($input as $key => $i) :
                $data[$key] = $i;
            endforeach;

            $data['pegawai'] = $this->laporan_service->getDataPersonilTpp($input);
            $data['personil'] = '';
            if ($data['pegawai']['count'] > 0) {
                $personil = array_map(function ($i) {
                    return $i['pin_absen'];
                }, $data['pegawai']['value']);

                $data['personil'] = implode(',', $personil);
            }

            //ambil tambahan data pilih bendahara
            $data['pilbendahara'] = [];
            $get = $this->pegawai_service->getData('SELECT kdlokasi_parent FROM tref_lokasi_kerja WHERE kdlokasi = "' . $input['kdlokasi'] . '" LIMIT 1', []);
            if ($get['count'] == 1 && !empty($get['value'][0]['kdlokasi_parent']) && $get['value'][0]['kdlokasi_parent']) {
                $parent = $get['value'][0]['kdlokasi_parent'];
                $data['pilbendahara'] = $this->laporan_service->getDataPersonilSatker(['kdlokasi' => $parent])['value'];
            }

            if ($data['tahun'] == 2018) :
                $data['bulan'] = 6; //tpp14 thn 2018 bln juni
            elseif ($data['tahun'] == 2019) :
                $data['bulan'] = 4; //tpp13 thn 2019 bln april
            endif;
            $data['tingkat'] = 6;

            $data['pajak'] = $this->laporan_service->getArraypajak();
            $data['laporan'] = $this->laporan_service->getLaporan($data);
            $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], true);
            $data['bendahara'] = $this->laporan_service->getBendahara($input['kdlokasi']);
            $data['kepala'] = $this->laporan_service->getKepala($input['kdlokasi']);

            $this->subView('tabeltpp14', $data);
        }
    }

    protected function checkmod() {
        $input = $this->post(true);

        if ($input) {
            $input['satker'] = $this->satker;
            $input['kdlokasi'] = $this->login['kdlokasi'];
            foreach ($input as $key => $i) :
                $data[$key] = $i;
            endforeach;

            $nomod = $this->laporan_service->getModerasi($input);
            //$nomod = true;
            if ($nomod['count'] > 0) {
                $data['title'] = 'Daftar Proses Pengajuan Moderasi';
                $data['daftarVerMod'] = $this->laporan_service->getDaftarVerMod($input);
                $data['dateLimit'] = $this->dateLimit;
                $this->subView('daftarmod', $data);
                exit;
            }

            //simpan verifikasi
        }
    }

    public function updateVerifikasi() {
        $input = $this->post(true);

        if ($input) {
            //check unverified moderasi
            $input['kdlokasi'] = $this->login['kdlokasi'];
            $unverified = $this->laporan_service->hitungModUnverified($input);
            if ($unverified['count'] > 0) {
                $error_msg = array('status' => 'unverified', 'message' => 'Maaf, Anda belum bisa mengesahkan laporan karena ada moderasi yang belum Anda verifikasi.');
                header('Content-Type: application/json');
                echo json_encode($error_msg);
                exit;
            }

            //simpan verifikasi
            $idKeys = [
                'kdlokasi' => $input['kdlokasi'],
                //'format' => $input['format'] == 'TPP' ? $input['format'] : $input['format'].$input['jenis'],
                //'format' => $input['format'] == 'C' ? $input['format'].$input['jenis'] : $input['jenis'],
                'bulan' => $input['bulan'],
                'tahun' => $input['tahun']
            ];

            if ($input['format'] == 'C') :
                $idKeys['pin_absen'] = $input['pin_absen'];
            endif;

            $ver = [
                'ver_admin_opd' => $this->login['nipbaru'],
                'dt_ver_admin_opd' => date('Y-m-d H:i:s')
            ];

            //$result = $this->laporan_service->update('tb_laporan', $ver, $idKeys);

            $idKeys = $idKeys + $ver;
            $field = implode(",", array_keys($idKeys));
            $result = $this->laporan_service->save('tb_laporan(' . $field . ')', $idKeys);

            $error_msg = ($result['error']) ? array('status' => 'success', 'message' => 'Terima kasih, laporan sudah disahkan secara elektronik dan sudah terkirim ke Kepala OPD.') : array('status' => 'error', 'message' => 'Maaf, laporan gagal disahkan');
            header('Content-Type: application/json');
            echo json_encode($error_msg);
        }
    }

    public function getPilLokasiFromKelLokasi() {
        $input = $this->post(true);
        if ($input) {
            $valData = $this->laporan_service->getPilLokasi($input);
            header('Content-Type: application/json');
            echo json_encode($valData);
        }
    }

    public function updateBendahara() {
        $input = $this->post(true);
        if ($input) {
            $input['kdlokasi'] = $this->login['kdlokasi'];
            $result = $this->laporan_service->save_update('tb_bendahara', $input);

            $error_msg = ($result['error']) ? array('status' => 'success', 'message' => 'Bendahara pengeluaran berhasil diubah') : array('status' => 'error', 'message' => 'Bendahara pengeluaran gagal diubah');
            header('Content-Type: application/json');
            echo json_encode($error_msg);
        }
    }

    /*     * *****************************START*AMBIL DATA BACKUP*************************************** */

    protected function tabelpresensibc($input) {
        if ($input) {
            foreach ($input as $key => $i) :
                $data[$key] = $i;
            endforeach;

            $data['induk'] = $this->backup_service->getDataInduk($input);
            if (!$data['induk']) {
                $this->subView('notfound', $data);
                exit;
            }

            $data['satker'] = $data['induk']['singkatan_lokasi'];
            $data['pegawai'] = $this->backup_service->getDataPersonil($data);
            $data['personil'] = '';
            if ($data['pegawai']['count'] > 0) {
                $personil = array_map(function ($i) {
                    return $i['pin_absen'];
                }, $data['pegawai']['value']);

                $data['personil'] = implode(',', $personil);
            }

            if ($data['jenis'] == 1) :
                $view = 'tabelmasukbc';
            elseif ($data['jenis'] == 2) :
                $view = 'tabelapelbc';
            elseif ($data['jenis'] == 3) :
                $view = 'tabelpulangbc';
            endif;

            $data['laporan'] = $this->backup_service->getLaporan($data['induk']['id']);

            $check = $this->backup_service->getData("SELECT tb_presensi.* FROM tb_presensi 
                JOIN tb_personil ON tb_personil.id = tb_presensi.personil_id
                WHERE induk_id = ?", [$data['induk']['id']]);

            if ($check['count'] == $data['pegawai']['count']) :
                $data['rekapbc'] = $this->backup_service->getRekapAllView($data['induk']['id']);
            else :
                $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], true);
            endif;

            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];

            $this->subView($view, $data);
        }
    }

    protected function tabelpresensibc_v2($input) {
        if ($input) {
            foreach ($input as $key => $i) :
                $data[$key] = $i;
            endforeach;

            $data['induk'] = $this->backup_service->getDataInduk($input);
            if (!$data['induk']) {
                $this->subView('notfound', $data);
                exit;
            }

            $data['satker'] = $data['induk'];
            $data['pegawai'] = $this->backup_service->getDataPersonil_v2($data);

            $arrPin = array_column($data['pegawai']['value'], 'pin_absen');
            $data['personil'] = implode(',', $arrPin);

            if ($data['jenis'] == 1) :
                $view = 'tabelmasukbc';
            elseif ($data['jenis'] == 2) :
                $view = 'tabelapelbc';
            elseif ($data['jenis'] == 3) :
                $view = 'tabelpulangbc';
            endif;

            $data['laporan'] = $this->backup_service->getLaporan($data['induk']['id']);

            $check = $this->backup_service->getData("SELECT tb_presensi.* FROM tb_presensi 
                JOIN tb_personil ON tb_personil.id = tb_presensi.personil_id
                WHERE induk_id = ?", [$data['induk']['id']]);

            if ($check['count'] == $data['pegawai']['count']) :
                $data['rekapbc'] = $this->backup_service->getRekapAllView($data['induk']['id']);
            else :
                $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], true);
            endif;

            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];

            $this->subView($view, $data);
        }
    }

    protected function tabelpersonilbc_v1($input) {
        if ($input) {
            foreach ($input as $key => $i) :
                $data[$key] = $i;
            endforeach;

            $data['induk'] = $this->backup_service->getDataInduk($input);
            if (!$data['induk']) {
                $this->subView('notfound', $data);
                exit;
            }

            $data['dataTabel'] = $this->backup_service->getTabelPersonil($data);
            $data['laporan'] = $this->backup_service->getLaporan($data['induk']['id']);
            $data['satker'] = $data['induk']['singkatan_lokasi'];
            $this->subView('tabelpersonilbc', $data);
        }
    }

    protected function tabelpersonilbc_v2($input) {
        if ($input) {
            foreach ($input as $key => $i) :
                $data[$key] = $i;
            endforeach;

            $data['induk'] = $this->backup_service->getDataInduk($input);
            if (!$data['induk']) {
                $this->subView('notfound', $data);
                exit;
            }

            $data['dataTabel'] = $this->backup_service->getTabelPersonil_v2($data);
            $data['laporan'] = $this->backup_service->getLaporan($data['induk']['id']);
            $data['satker'] = $data['induk']['singkatan_lokasi'];
            $this->subView('tabelpersonilbc', $data);
        }
    }
    
    protected function tabelrekapc1bc($input) {
        if ($input) {
            foreach ($input as $key => $i) :
                $data[$key] = $i;
            endforeach;

            $data['induk'] = $this->backup_service->getDataInduk($input);
            if (!$data['induk']) {
                $this->subView('notfound', $data);
                exit;
            }

            $data['satker'] = $data['induk']['singkatan_lokasi'];
            $data['pegawai'] = $this->backup_service->getDataPersonilBatch_v2($data, true);
            $data['personil'] = '';
            if ($data['pegawai']['count'] > 0) {
                $personil = array_map(function ($i) {
                    return $i['pin_absen'];
                }, $data['pegawai']['value']);

                $data['personil'] = implode(',', $personil);
            }

            $data['tpp'] = $this->backup_service->getDataTpp($data['induk']['id']);

            $data['format'] = 'A';
            $data['jenis'] = '';
            $data['personil'] = $input['pin_absen'];

            //ambil ttd
            if ($data['tingkat'] == 6 && $data['bulan'] == 1 && $data['tahun'] == 2018) :
                $data['tingkat'] = 3;
            endif;

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

            $this->subView('tabelrekapc1bc', $data);
        }
    }

    protected function tabelrekapc2bc($input) {
        if ($input) {
            foreach ($input as $key => $i) :
                $data[$key] = $i;
            endforeach;

            $data['induk'] = $this->backup_service->getDataInduk($input);
            if (!$data['induk']) {
                $this->subView('notfound', $data);
                exit;
            }

            $data['satker'] = $data['induk']['singkatan_lokasi'];
            $data['pegawai'] = $this->backup_service->getDataPersonilBatch($data, true);
            $data['format'] = 'B';
            $data['jenis'] = '';
            $data['personil'] = $input['pin_absen'];

            //ambil ttd
            if ($data['tingkat'] == 6 && $data['bulan'] == 1 && $data['tahun'] == 2018) :
                $data['tingkat'] = 3;
            endif;

            $data['laporan'] = $this->backup_service->getLaporan($data['induk']['id']);
            $check = $this->backup_service->getData("SELECT tb_presensi.* FROM tb_presensi 
                JOIN tb_personil ON tb_personil.id = tb_presensi.personil_id
                JOIN tb_induk ON tb_induk.id = tb_personil.induk_id
                WHERE tb_induk.id = " . $data['induk']['id'] . " AND tb_personil.pin_absen IN (" . $data['pin_absen'] . ")");

            if ($check['count'] > 0) :
                $data['rekapbc'] = $this->backup_service->getRekapAllView($data['induk']['id'], $data['pin_absen']);
            else :
                $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], true);
            endif;

            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];

            $this->subView('tabelrekapc2bc', $data);
        }
    }

    protected function tabeltppbc($input) {
        if ($input) {
            foreach ($input as $key => $i) :
                $data[$key] = $i;
            endforeach;

            $data['induk'] = $this->backup_service->getDataInduk($input);
            if (!$data['induk']) {
                $this->subView('notfound', $data);
                exit;
            }

            $data['satker'] = $data['induk']['singkatan_lokasi'];
            $data['pegawai'] = $this->backup_service->getDataPersonilBatch($data, true);
            $data['personil'] = '';
            if ($data['pegawai']['count'] > 0) {
                $personil = array_map(function ($i) {
                    return $i['pin_absen'];
                }, $data['pegawai']['value']);

                $data['personil'] = implode(',', $personil);
            }

            $data['tpp'] = $this->backup_service->getDataTpp($data['induk']['id']);

            //bulan jan dn feb masih uji coba
            $period = $data['bulan'] . $data['tahun'];
            if ($period == '12018' || $period == '22018') :
                $data['tingkat'] = 6;
            endif;

            $data['laporan'] = $this->backup_service->getLaporan($data['induk']['id']);

            $check = $this->backup_service->getData("SELECT tb_presensi.* FROM tb_presensi 
                JOIN tb_personil ON tb_personil.id = tb_presensi.personil_id
                WHERE induk_id = ?", [$data['induk']['id']]);

            //if ($check['count'] == $data['pegawai']['count']) {
            //ambil dari presensi backup
            $data['rekap'] = $this->backup_service->getRekapAllView($data['induk']['id']);
            $this->subView('tabeltppbcall', $data);
            /* } else {
              $data['kenabpjs'] = $this->laporan_service->getDataSetting('maks_tpp_kena_bpjs');
              $data['pajak'] = $this->laporan_service->getArraypajak();
              $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], true);
              $this->subView('tabeltppbc', $data);
              } */
        }
    }

    protected function tabeltppbc_v2($input) {
        if ($input) {
            foreach ($input as $key => $i) :
                $data[$key] = $i;
            endforeach;

            $data['induk'] = $this->backup_service->getDataInduk($input);
            if (!$data['induk']) {
                $this->subView('notfound', $data);
                exit;
            }

            $data['satker'] = $data['induk']['singkatan_lokasi'];
            $data['pegawai'] = $this->backup_service->getDataPersonilBatch_v2($data, true);

            $arrPin = array_column($data['pegawai']['value'], 'pin_absen');
            $data['personil'] = implode(',', $arrPin);
            $data['tpp'] = $this->backup_service->getDataTpp($data['induk']['id']);
            $data['laporan'] = $this->backup_service->getLaporan($data['induk']['id']);

            //ambil dari presensi backup
            $data['rekap'] = $this->backup_service->getRekapAllView($data['induk']['id']);
            $this->subView('tabeltppbcall_v2', $data);
        }
    }

    protected function tabeltppbc_v3($input) {
        if ($input) {
            foreach ($input as $key => $i) :
                $data[$key] = $i;
            endforeach;

            $data['induk'] = $this->backup_service->getDataInduk($input);
            if (!$data['induk']) {
                $this->subView('notfound', $data);
                exit;
            }

            $data['satker'] = $data['induk']['singkatan_lokasi'];
            $data['pegawai'] = $this->backup_service->getDataPersonilBatch_v2($data, true);

            $arrPin = array_column($data['pegawai']['value'], 'pin_absen');
            $data['personil'] = implode(',', $arrPin);
            $data['tpp'] = $this->backup_service->getDataTpp($data['induk']['id']);
            $data['laporan'] = $this->backup_service->getLaporan($data['induk']['id']);

            //ambil dari presensi backup
            $data['rekap'] = $this->backup_service->getRekapAllView($data['induk']['id']);
            $this->subView('tabeltppbcall_v3', $data);
        }
    }

    protected function verifikasibc($input) {
        if ($input) {
            foreach ($input as $key => $i) :
                $data[$key] = $i;
            endforeach;

            $data['induk'] = $this->backup_service->getDataInduk($input);
            if (!$data['induk']) {
                $this->subView('notfound', $data);
                exit;
            }

            $data['format'] = 'A';
            $data['jenis'] = '';
            $data['satker'] = $data['induk']['singkatan_lokasi'];
            $data['pegawai'] = $this->backup_service->getDataPersonil($data);
            $data['personil'] = '';
            if ($data['pegawai']['count'] > 0) {
                $personil = array_map(function ($i) {
                    return $i['pin_absen'];
                }, $data['pegawai']['value']);

                $data['personil'] = implode(',', $personil);
            }

            $data['laporan'] = $this->backup_service->getLaporan($data['induk']['id']);

            $check = $this->backup_service->getData("SELECT tb_presensi.* FROM tb_presensi 
                JOIN tb_personil ON tb_personil.id = tb_presensi.personil_id
                WHERE induk_id = ?", [$data['induk']['id']]);

            if ($check['count'] == $data['pegawai']['count']) :
                $data['rekapbc'] = $this->backup_service->getRekapAllView($data['induk']['id']);
            else :
                $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], true);
            endif;

            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];

            $this->subView('verifikasibc', $data);
        }
    }

    /*     * *****************************START*AMBIL DATA BACKUP*************************************** */

    public function script() {
        $data['title'] = '<!-- Script -->';
        $this->subView('script', $data);
    }

    public function test() {
        $url = 'http://192.168.254.63/super/api/poin_pns';
        $method = 'poin';

        $accesskey = ['kinerja-key' => 'OFV6Y1NualM3dWZBRHZuaFhySDBVQWZYd29JNTZ0'];
        $request = array('opd' => 'G09001', 'tahun' => '2021', 'bulan' => '6');
        $data = $this->webadapter->callAPI($url, $method, $accesskey, $request);

        comp\FUNC::showPre($data);
    }

    public function getJSON() {
        header('Content-Type: application/json');
        $input = $this->post(true);
        switch ($input['mode']) {
            case 'cekBackup':
                $param = $input['params'];
                $idKey['kdlokasi'] = $this->login['kdlokasi'];
                $idKey['tahun'] = $param['tahun'];
                $idKey['bulan'] = $param['bulan'];
                // cetak laporan TPP di tutup sementara untuk desember 2021
                if ($param['tahun'] == 2021 && $param['bulan'] == 12) {
                    $response = ['msg' => 'Pencetakan dibatasi..', 'value' => 0];
                    echo json_encode($response);
                    exit;
                }
                $data = $this->backup_service->getDataInduk($idKey);
                $response = ($data == false && $param['tingkat'] == 6) ? 
                    ['msg' => 'Menunggu backup data..', 'value' => 0] : ['msg' => '', 'value' => 1];
        }
        echo json_encode($response);
    }

}
