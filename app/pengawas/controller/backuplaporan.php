<?php

namespace app\pengawas\controller;

use app\pengawas\model\servicemain;
use app\pengawas\model\pegawai_service;
use app\pengawas\model\laporan_service;
use app\pengawas\model\backup_service;
use system;

class backuplaporan extends system\Controller {
	public function __construct() {
        parent::__construct();
        $this->servicemain = new servicemain();
        $session = $this->servicemain->cekSession();

        if ($session['status'] === true) {
            $this->laporan_service = new laporan_service();
            $this->pegawai_service = new pegawai_service();
            $this->backup_service = new backup_service();
            $this->setSession('SESSION_LOGIN', $session['data']);
            $this->login = $this->getSession('SESSION_LOGIN');
        } else {
            $this->setSession('SESSION_RELOAD', true);
            $this->redirect($this->link('login'));
        }
    }

    protected function index() {
        $data['title'] = 'Backup Laporan Final';
        $data['breadcrumb'] = '<a href="'.$this->link().'" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Backup Laporan Final</a>';
        $this->showView('index', $data, 'theme_admin');
    }

    protected function lihat() {
        $data['title'] = 'Backup Laporan Final';
        $data['breadcrumb'] = '<a href="'.$this->link().'" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Backup Laporan Final</a>';

        $satker = $this->laporan_service->getPilLokasi();
        $params = explode('/', $_GET['p4']);
        if (!isset($satker[$params[0]])) {
            $this->showView('index', $data, 'theme_admin');
            exit;
        }

        $period = $params[1];
        $data['tahun'] = substr($period, -4);
        $data['bulan'] = str_replace($data['tahun'], '', $period);
        $data['kdlokasi'] = $params[0];
        $data['satker'] = $satker[$params[0]];
        $this->showView('lihat', $data, 'theme_admin');
    }

    protected function tabellist() {
        $input = $this->post(true);
        if ($input) {
            foreach ($input as $key => $i)
                $data[$key] = $i;

            $data['induk'] = $this->backup_service->getData('SELECT * FROM tb_induk 
                WHERE bulan = "'.$input['bulan'].'" AND tahun = "'.$input['tahun'].'"');

            $data['belum'] = $this->backup_service->getBelumBackup($input, $data['induk']);
            $data['lokasi'] = $this->laporan_service->getPilLokasi();
            $this->subView('tabellist', $data);
        }
    }

    protected function tabelpresensi() {
        $input = $this->post(true);
        if ($input) {
            foreach ($input as $key => $i)
                $data[$key] = $i;

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

            if ($data['jenis'] == 1)
                $view = 'tabelmasuk';
            elseif ($data['jenis'] == 2)
                $view = 'tabelapel';
            elseif ($data['jenis'] == 3)
                $view = 'tabelpulang';

            $data['laporan'] = $this->backup_service->getLaporan($data['induk']['id']);
            $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], true);
            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];

            $this->subView($view, $data);
        }
    }

    protected function tabelpersonil() {
        $input = $this->post(true);
        if ($input) {
            foreach ($input as $key => $i)
                $data[$key] = $i;

            $data['induk'] = $this->backup_service->getDataInduk($input);
            if (!$data['induk']) {
                $this->subView('notfound', $data);
                exit;
            }

            $data['dataTabel'] = $this->backup_service->getTabelPersonil($input);
            $data['laporan'] = $this->backup_service->getLaporan($data['induk']['id']);
            $data['satker'] = $data['induk']['singkatan_lokasi'];
            $this->subView('tabelpersonil', $data);
        }
    }

    protected function tabelrekapc1() {
        $input = $this->post(true);
        if ($input) {
            foreach ($input as $key => $i)
                $data[$key] = $i;

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

            $data['format'] = 'A'; $data['jenis'] = '';
            $data['personil'] = $input['pin_absen'];

            //ambil ttd
            if ($data['tingkat'] == 6 && $data['bulan'] == 1 && $data['tahun'] == 2018)
                $data['tingkat'] = 3;

            $data['laporan'] = $this->backup_service->getLaporan($data['induk']['id']);
            $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], true);
            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];

            $this->subView('tabelrekapc1', $data);
        }
    }

    protected function tabelrekapc2() {
        $input = $this->post(true);
        if ($input) {
            foreach ($input as $key => $i)
                $data[$key] = $i;

            $data['induk'] = $this->backup_service->getDataInduk($input);
            if (!$data['induk']) {
                $this->subView('notfound', $data);
                exit;
            }

            $data['satker'] = $data['induk']['singkatan_lokasi'];
            $data['pegawai'] = $this->backup_service->getDataPersonilBatch($data, true);
            $data['format'] = 'B'; $data['jenis'] = '';
            $data['personil'] = $input['pin_absen'];

            //ambil ttd
            if ($data['tingkat'] == 6 && $data['bulan'] == 1 && $data['tahun'] == 2018)
                $data['tingkat'] = 3;

            $data['laporan'] = $this->backup_service->getLaporan($data['induk']['id']);
            $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], true);
            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];

            $this->subView('tabelrekapc2', $data);
        }
    }

    protected function tabeltpp() {
        $input = $this->post(true);
        if ($input) {
            foreach ($input as $key => $i)
                $data[$key] = $i;
            
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
            $period = $data['bulan'].$data['tahun'];
            if ($period == '12018' || $period == '22018')
                $data['tingkat'] = 6;

            $data['laporan'] = $this->backup_service->getLaporan($data['induk']['id']);
            $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], true);

            $this->subView('tabeltpp', $data);
        }
    }

    protected function dobackup() {
        $input = $this->post(true);
        if ($input) {
            $result = $this->backup_service->dobackup($input);
            $error_msg = ($result['error']) ? array('status' => 'success', 'message' => 'Backup laporan berhasil.') : array('status' => 'error', 'message' => 'Maaf, backup laporan gagal.');

            header('Content-Type: application/json');
            echo json_encode($error_msg);
        }
    }

    public function script() {
        $data['title'] = '<!-- Script -->';
        $this->subView('script', $data);
    }
}