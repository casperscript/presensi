<?php

namespace app\admin\controller;

use app\admin\model\servicemain;
use app\admin\model\laporan_service;
use app\admin\model\backup_service;
use system;

class laporan extends system\Controller {

	public function __construct() {
        parent::__construct();
        $this->servicemain = new servicemain();
        $session = $this->servicemain->cekSession();

        if ($session['status'] === true) {
            $this->laporan_service = new laporan_service();
            $this->backup_service = new backup_service();

            $this->setSession('SESSION_LOGIN', $session['data']);
            $this->login = $this->getSession('SESSION_LOGIN');
        } else {
            $this->setSession('SESSION_RELOAD', true);
            $this->redirect($this->link('login'));
        }
    }

    protected function index() {
        $data['title'] = 'Laporan dari OPD';
        $data['pil_kel_satker'] = ['' => '-- Pilih Kelompok Lokasi Kerja --'] + $this->laporan_service->getPilKelSatker([]);
        $data['pil_satker'] = ['' => '-- Pilih Satuan Kerja --'];
        $data['breadcrumb'] = '<a href="'.$this->link().'" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Laporan dari OPD</a>';

        $data['bulan'] = ((int)date('m') == 1 ? 12 : (int)date('m')-1);
        $data['tahun'] = ((int)date('m') == 1 ? (int)date('Y')-1 : date('Y'));

        $this->showView('index', $data, 'theme_admin');
    }

    protected function tabelindex() {
        $input = $this->post(true);
        if ($input) {
            foreach ($input as $key => $i)
                $data[$key] = $i;

            $namabulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

            $data['lokasi'] = $this->laporan_service->getPilLokasi();
            $data['laporan'] = $this->laporan_service->getAllLaporan($data)['value'];
            $data['namabulan'] = $namabulan[$data['bulan']-1];
            $this->subView('tabelindex', $data);
        }
    }

    protected function verified() {
        $data['title'] = 'Laporan Terverifikasi';
        //$data['pil_kel_satker'] = ['' => '-- Pilih Kelompok Lokasi Kerja --'] + $this->laporan_service->getPilKelSatker([]);
        $data['pil_satker'] = ['' => '-- Pilih Satuan Kerja --'];
        $data['breadcrumb'] = '<a href="'.$this->link().'" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Laporan Terverifikasi</a>';

        $this->showView('verified', $data, 'theme_admin');
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

    protected function indexold() {
        $data['title'] = 'Laporan';
        $data['pil_kel_satker'] = ['' => '-- Pilih Kelompok Lokasi Kerja --'] + $this->laporan_service->getPilKelSatker([]);
        $data['pil_satker'] = ['' => '-- Pilih Satuan Kerja --'];
        $data['breadcrumb'] = '<a href="'.$this->link().'" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Laporan</a>';
        $this->showView('indexold', $data, 'theme_admin');
    }

    protected function tabelapelold() {
        $input = $this->post(true);
        if ($input && $input['kdlokasi']) {
            $data['pegawai'] = $this->laporan_service->getDataPersonilSatker($input);
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
            $error_msg = ($result['error']) ? array('status' => 'success', 'message' => 'Terima kasih, laporan sudah disahkan secara elektronik dan sudah terkirim ke Kepala BKPPD.') : array('status' => 'error', 'message' => 'Maaf, laporan gagal disahkan');
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

    public function loadVerifikasi() {
        $input = $this->post(true);
        if ($input) {
            foreach ($input as $key => $i)
                $data[$key] = $i;

            $data['satker'] = $this->laporan_service->getPilLokasi()[$data['kdlokasi']];
            $data['pil_kel_satker'] = ['' => '-- Pilih Kelompok Lokasi Kerja --'] + $this->laporan_service->getPilKelSatker([]);
            $data['pil_satker'] = ['' => '-- Pilih Satuan Kerja --'];
            $data['breadcrumb'] = '<a href="'.$this->link().'" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Laporan</a>';
            $this->subView('verifikasi', $data);
        }
    }

    protected function tabelverifikasi() {
        $input = $this->post(true);
        if ($input) {
            foreach ($input as $key => $i)
                $data[$key] = $i;

            $data['satker'] = $this->laporan_service->getPilLokasi()[$input['kdlokasi']];
            $data['pegawai'] = $this->laporan_service->getDataPersonilSatker($input);
            $data['personil'] = '';
            if ($data['pegawai']['count'] > 0) {
                $personil = array_map(function ($i) {
                    return $i['pin_absen'];
                }, $data['pegawai']['value']);

                $data['personil'] = implode(',', $personil);
            }

            $data['format'] = 'A'; $data['jenis'] = '';
            $data['laporan'] = $this->laporan_service->getLaporan($data);
            $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], true);
            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];

            $this->subView('tabelverifikasi', $data);
        }
    }

    public function batalverifikasi() {
        $data['title'] = 'Pembatalan Verifikasi Laporan';
        $data['breadcrumb'] = '<a href="'.$this->link().'" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Pembatalan Verifikasi Laporan</a>';

        $this->showView('batalverifikasi', $data, 'theme_admin');
    }

    protected function tabelbatal() {
        $input = $this->post(true);
        if ($input) {
            foreach ($input as $key => $i)
                $data[$key] = $i;

            $data['dataTabel'] = $this->laporan_service->getVerLaporan($data);
            $data['satker'] = $this->laporan_service->getPilLokasi();
            $this->subView('tabelbatal', $data);
        }
    }

    public function prosesbatal() {
        $input = $this->post(true);
        if ($input) {
            $copy = $result = $this->laporan_service->getData('SELECT * FROM tb_laporan WHERE kdlokasi = ? AND bulan = ? AND tahun = ?', [$input['kdlokasi'], $input['bulan'], $input['tahun']]);

            $idKeys = [
                'kdlokasi' => $input['kdlokasi'],
                'bulan' => $input['bulan'],
                'tahun' => $input['tahun']
            ];

            $result = $this->laporan_service->delete('tb_laporan', $idKeys);

            if ($copy['count'] > 0) {
                $data = $copy['value'][0];
                $data['id'] = NULL;
                $data['petugas'] = $this->login['nipbaru'];
                $data['cancelled_by'] = NULL;
                $simpan = $this->laporan_service->save('tb_batalver', $data);
                $this->backup_service->hapusBackup($idKeys);
            }

            $error_msg = ($result['error']) ? array('status' => 'success', 'message' => 'Verifikasi laporan berhasil dibatalkan.') : array('status' => 'error', 'message' => 'Maaf, laporan gagal dibatalkan');
            header('Content-Type: application/json');
            echo json_encode($error_msg);
        }
    }

    public function script() {
        $data['title'] = '<!-- Script -->';
        $this->subView('script', $data);
    }
}