<?php

namespace app\pengawas\controller;

use app\pengawas\model\servicemain;
use app\pengawas\model\laporan_service;
use system;

class laporan extends system\Controller {

	public function __construct() {
        parent::__construct();
        $this->servicemain = new servicemain();
        $session = $this->servicemain->cekSession();

        if ($session['status'] === true) {
            $this->laporan_service = new laporan_service();

            $this->setSession('SESSION_LOGIN', $session['data']);
            $this->login = $this->getSession('SESSION_LOGIN');
        } else {
            $this->setSession('SESSION_RELOAD', true);
            $this->redirect($this->link('login'));
        }
    }

    protected function indexbaru() {
        $data['title'] = 'Laporan dari OPD';
        $data['pil_kel_satker'] = ['' => '-- Pilih Kelompok Lokasi Kerja --'] + $this->laporan_service->getPilKelSatker([]);
        $data['pil_satker'] = ['' => '-- Pilih Satuan Kerja --'];
        $data['breadcrumb'] = '<a href="'.$this->link().'" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Laporan</a>';

        $data['bulan'] = ((int)date('m') == 1 ? 12 : (int)date('m')-1);
        $data['tahun'] = ((int)date('m') == 1 ? (int)date('Y')-1 : date('Y'));

        $data['lokasi'] = $this->laporan_service->getPilLokasi();
        $data['laporan'] = $this->laporan_service->getAllLaporan($data)['value'];
        $this->showView('index', $data, 'theme_admin');
    }

    public function getPilLaporan() {
        $input = $this->post(true);
        if ($input) {
            $lokasi = $this->laporan_service->getPilLokasi();
            $laporan = $this->laporan_service->getAllLaporan($input)['value'];
            $sudah = [];
            foreach ($laporan as $i) {
                if ($i['ver_admin_kota'])
                    $sudah[$i['kdlokasi']] = $lokasi[$i['kdlokasi']];
            }

            header('Content-Type: application/json');
            echo json_encode($sudah);
        }
    }

    protected function index() {
        $data['title'] = 'Laporan';
        $data['pil_kel_satker'] = ['' => '-- Pilih Kelompok Lokasi Kerja --'] + $this->laporan_service->getPilKelSatker([]);
        $data['pil_satker'] = ['' => '-- Pilih Satuan Kerja --'];
        $data['breadcrumb'] = '<a href="'.$this->link().'" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Laporan</a>';
        $this->showView('index', $data, 'theme_admin');
    }

    protected function tabelapelold() {
        $input = $this->post(true);
        if ($input && $input['kdlokasi']) {
            $data['pegawai'] = $this->apelpagi_service->getDataPersonilSatker($input);
            $input['personil'] = '';
            if ($data['pegawai']['count'] > 0) {
                $personil = array_map(function ($i) {
                    return $i['pin_absen'];
                }, $data['pegawai']['value']);

                $input['personil'] = implode(',', $personil);
            }

            foreach ($input as $key => $i)
                $data[$key] = $i;

            $data['satker'] = $this->laporan_service->getPilLokasi()[$input['kdlokasi']];
			//$data['rekap'] = $this->laporan_service->getRecordPersonil($input);
            $data['rekap'] = $this->apelpagi_service->getRecordApel($input);            
			$data['libur'] = $this->laporan_service->getLibur($input);

            //ambil ttd
            $data['laporan'] = $this->laporan_service->getLaporan($input);
            //ambil moderasi
            $data['moderasi'] = $this->laporan_service->getArraymod($input, $data['laporan']);
			$data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];

            $this->subView('tabelapelold', $data);
        }
    }

    protected function tabelpresensi() {
        $input = $this->post(true);
        if ($input) {
            $data['satker'] = $this->laporan_service->getPilLokasi()[$input['kdlokasi']];
            foreach ($input as $key => $i)
                $data[$key] = $i;

            $data['pegawai'] = $this->laporan_service->getDataPersonilSatker($input);
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

            $data['jenis'] = '';
            $data['laporan'] = $this->laporan_service->getLaporan($data);
            $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], true);
            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];

            $this->subView($view, $data);
        }
    }

    protected function tabelmasukold() {
        $input = $this->post(true);
        if ($input && $input['kdlokasi']) {
            foreach ($input as $key => $i)
                $data[$key] = $i;

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
            $data['satker'] = $this->laporan_service->getPilLokasi()[$input['kdlokasi']];

            //ambil ttd
            $data['laporan'] = $this->laporan_service->getLaporan($input);
            //ambil moderasi
            $data['moderasi'] = $this->laporan_service->getArraymod($input, $data['laporan']);
            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];

            $this->subView('tabelmasukold', $data);
        }
    }

    protected function tabelpulangold() {
        $input = $this->post(true);
        if ($input && $input['kdlokasi']) {
            foreach ($input as $key => $i)
                $data[$key] = $i;

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
            $data['satker'] = $this->laporan_service->getPilLokasi()[$input['kdlokasi']];

            //ambil ttd
            $data['laporan'] = $this->laporan_service->getLaporan($input);
            //ambil moderasi
            $data['moderasi'] = $this->laporan_service->getArraymod($input, $data['laporan']);
            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];
            
            $this->subView('tabelpulangold', $data);
        }
    }

    protected function tabelpersonil() {
        $input = $this->post(true);
        if ($input) {
            foreach ($input as $key => $i)
                $data[$key] = $i;

            $data['dataTabel'] = $this->laporan_service->getTabelPersonil($input);
            $data['satker'] = $this->laporan_service->getPilLokasi()[$input['kdlokasi']];
            $this->subView('tabelpersonil', $data);
        }
    }

    protected function tabelrekapc1() {
        $input = $this->post(true);
        if ($input) {
            foreach ($input as $key => $i)
                $data[$key] = $i;

            $data['pegawai'] = $this->laporan_service->getDataPersonilBatch($input['pin_absen'], true);
            $data['personil'] = '';
            if ($data['pegawai']['count'] > 0) {
                $personil = array_map(function ($i) {
                    return $i['nipbaru'];
                }, $data['pegawai']['value']);

                $data['personil'] = implode(',', $personil);
            }
            $data['tpp_pegawai'] = $this->laporan_service->getTpp($data['personil']);

            $data['format'] = 'A'; $data['jenis'] = '';
            $data['personil'] = $input['pin_absen'];
			$data['satker'] = $this->laporan_service->getPilLokasi()[$input['kdlokasi']];

            //ambil ttd
            $data['laporan'] = $this->laporan_service->getLaporan($input);
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

            $data['pegawai'] = $this->laporan_service->getDataPersonilBatch($input['pin_absen'], true);

            $data['format'] = 'B'; $data['jenis'] = '';
            $data['personil'] = $input['pin_absen'];
			$data['satker'] = $this->laporan_service->getPilLokasi()[$input['kdlokasi']];

            //ambil ttd
            $data['laporan'] = $this->laporan_service->getLaporan($input);
            $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan']);
            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];

            $this->subView('tabelrekapc2', $data);
        }
    }

    protected function tabeltpp() {
        $input = $this->post(true);
        if ($input) {
            foreach ($input as $key => $i)
                $data[$key] = $i;
            
            $data['pegawai'] = $this->laporan_service->getDataPersonilTpp($input);
            $data['personil'] = '';
            if ($data['pegawai']['count'] > 0) {
                $personil = array_map(function ($i) {
                    return $i['pin_absen'];
                }, $data['pegawai']['value']);

                $data['personil'] = implode(',', $personil);
            }

            //bulan jan dn feb masih uji coba
            $period = $data['bulan'].$data['tahun'];
            if ($period == '12018' || $period == '22018')
                $data['tingkat'] = 6;

            $data['pajak'] = $this->laporan_service->getArraypajak();
            $data['laporan'] = $this->laporan_service->getLaporan($data);
            $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], true);
			$data['satker'] = $this->laporan_service->getPilLokasi()[$input['kdlokasi']];
			$data['bendahara'] = $this->laporan_service->getBendahara($input['kdlokasi']);
            $data['kepala'] = $this->laporan_service->getKepala($input['kdlokasi']);

            $this->subView('tabeltpp', $data);
        }
    }

    protected function checkmod() {
        $input = $this->post(true);

        if ($input) {
            foreach ($input as $key => $i)
                $data[$key] = $i;
            
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
            //simpan verifikasi
            $idKeys = [
                'kdlokasi' => $input['kdlokasi'],
                //'format' => $input['format'] == 'TPP' ? $input['format'] : $input['format'].$input['jenis'],
                'bulan' => $input['bulan'],
                'tahun' => $input['tahun']
            ];

            if ($input['format'] == 'C')
                $idKeys['pin_absen'] = $input['pin_absen'];

            $ver = [
                'ver_admin_kota' => $this->login['nipbaru'],
                'dt_ver_admin_kota' => date('Y-m-d H:i:s')
            ];

            $result = $this->laporan_service->update('tb_laporan', $ver, $idKeys);
            $error_msg = ($result['error']) ? array('status' => 'success', 'message' => 'Laporan berhasil diverifikasi') : array('status' => 'error', 'message' => 'Laporan gagal diverifikasi');
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

    public function script() {
        $data['title'] = '<!-- Script -->';
        $this->subView('script', $data);
    }
}