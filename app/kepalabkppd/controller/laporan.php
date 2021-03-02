<?php

namespace app\kepalabkppd\controller;

use app\kepalabkppd\model\servicemain;
use app\kepalabkppd\model\laporan_service;
use app\kepalabkppd\model\backup_service;
use system;
use comp;

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

    protected function indexold() {
        $data['title'] = 'Laporan';
        $data['pil_kel_satker'] = ['' => '-- Pilih Kelompok Lokasi Kerja --'] + $this->laporan_service->getPilKelSatker([]);
        $data['pil_satker'] = ['' => '-- Pilih Satuan Kerja --'];
        $data['breadcrumb'] = '<a href="'.$this->link().'" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Laporan</a>';
        $this->showView('indexold', $data, 'theme_admin');
    }

    protected function index() {
        $data['title'] = 'Laporan Masuk untuk Disahkan';
        $data['pil_kel_satker'] = ['' => '-- Pilih Kelompok Lokasi Kerja --'] + $this->laporan_service->getPilKelSatker([]);
        $data['pil_satker'] = ['' => '-- Pilih Satuan Kerja --'];
        $data['breadcrumb'] = '<a href="'.$this->link().'" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Laporan</a>';

        $data['bulan'] = ((int)date('m') == 1 ? 12 : (int)date('m')-1);
        $data['tahun'] = ((int)date('m') == 1 ? (int)date('Y')-1 : date('Y'));
        $data['listTahun'] = comp\FUNC::numbSeries('2018', date('Y'));
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
        $data['title'] = 'Laporan Telah Disahkan';
        //$data['pil_kel_satker'] = ['' => '-- Pilih Kelompok Lokasi Kerja --'] + $this->laporan_service->getPilKelSatker([]);
        $data['pil_satker'] = ['' => '-- Pilih Satuan Kerja --'];
        $data['breadcrumb'] = '<a href="'.$this->link().'" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Laporan</a>';
        $data['listTahun'] = comp\FUNC::numbSeries('2018', date('Y'));

        $this->showView('verified', $data, 'theme_admin');
    }

    public function getPilLaporan() {
        $input = $this->post(true);
        if ($input) {
            $lokasi = $this->laporan_service->getPilLokasi();
            $laporan = $this->laporan_service->getAllLaporan($input)['value'];
            $sudah = [];
            foreach ($laporan as $i) {
                if ($i['sah_kepala_bkppd'])
                    $sudah[$i['kdlokasi']] = $lokasi[$i['kdlokasi']];
            }

            header('Content-Type: application/json');
            echo json_encode($sudah);
        }
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

            $data['laporan'] = $this->laporan_service->getLaporan($data);
            $data['induk'] = $this->backup_service->getDataInduk($input);
            //admbil dari data backupan
            if ($data['induk'] && isset($data['laporan']['final']) && $data['laporan']['final'] != '') {
                $this->tabelpersonilbc($input);
                exit;
            }

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

            $data['laporan'] = $this->laporan_service->getLaporan($input);
            $data['induk'] = $this->backup_service->getDataInduk($input);
            //admbil dari data backupan
            if ($data['induk'] && isset($data['laporan']['final']) && $data['laporan']['final'] != '') {
                $this->tabelrekapc2bc($input);
                exit;
            }

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
                'sah_kepala_bkppd' => $this->login['nipbaru'],
                'dt_sah_kepala_bkppd' => date('Y-m-d H:i:s')
            ];

            $result = $this->laporan_service->update('tb_laporan', $ver, $idKeys);

            //BEGIN -- update pengesahan final kepala opd jika tidak ada catatan admin kota atau catatan kepala kota
            if ($input['catatan'] == 0) {
                $params = [$input['kdlokasi'], $input['bulan'], $input['tahun']];
                $sql = "SELECT * FROM tb_laporan WHERE kdlokasi = ? AND bulan = ? AND tahun = ? AND pin_absen IS NULL";
                $get = $this->laporan_service->getData($sql, $params);

                if ($get['count'] > 0) {
                    $lap = $get['value'][0];
                    $ver = [
                        'sah_final' => $lap['sah_kepala_opd'],
                        'dt_sah_final' => $lap['dt_sah_kepala_opd']
                    ];
                    $update = $this->laporan_service->update('tb_laporan', $ver, $idKeys);

                    //backup laporan//
                    /*if ($update['error'])
                        $this->backup_service->dobackup($input);*/
                    //backup laporan//
                }
            }
            //END -- update pengesahan final kepala opd jika tidak ada catatan admin kota atau catatan kepala kota

            $satker = $this->laporan_service->getPilLokasi()[$input['kdlokasi']];
            $error_msg = ($result['error']) ? array('status' => 'success', 'message' => 'Terima kasih, laporan sudah disahkan secara elektronik dan sudah terkirim ke '.$satker.'.') : array('status' => 'error', 'message' => 'Maaf, laporan gagal disahkan');
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
            foreach ($input as $key => $i) {
                $data[$key] = $i;
            }

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
            $data['periode'] = $input['kdlokasi'] . '|' . $input['bulan'] . '|' . $input['tahun'];
//            comp\FUNC::showPre($input); exit;
            
            $this->subView('tabelverifikasi', $data);
        }
    }
    
/*******************************START*AMBIL DATA BACKUP****************************************/
    protected function tabelpresensibc($input) {
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
                $view = 'tabelmasukbc';
            elseif ($data['jenis'] == 2)
                $view = 'tabelapelbc';
            elseif ($data['jenis'] == 3)
                $view = 'tabelpulangbc';

            $data['laporan'] = $this->backup_service->getLaporan($data['induk']['id']);

            $check = $this->backup_service->getData("SELECT tb_presensi.* FROM tb_presensi 
                JOIN tb_personil ON tb_personil.id = tb_presensi.personil_id
                WHERE induk_id = ?", [$data['induk']['id']]);

            if ($check['count'] == $data['pegawai']['count'])
                $data['rekapbc'] = $this->backup_service->getRekapAllView($data['induk']['id']);
            else
                $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], true);

            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];

            $this->subView($view, $data);
        }
    }

    protected function tabelpersonilbc($input) {
        if ($input) {
            foreach ($input as $key => $i)
                $data[$key] = $i;

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

    protected function tabelrekapc1bc($input) {
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
            $check = $this->backup_service->getData("SELECT tb_presensi.* FROM tb_presensi 
                JOIN tb_personil ON tb_personil.id = tb_presensi.personil_id
                JOIN tb_induk ON tb_induk.id = tb_personil.induk_id
                WHERE tb_induk.id = ".$data['induk']['id']." AND tb_personil.pin_absen IN (".$data['pin_absen'].")");

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
            $check = $this->backup_service->getData("SELECT tb_presensi.* FROM tb_presensi 
                JOIN tb_personil ON tb_personil.id = tb_presensi.personil_id
                JOIN tb_induk ON tb_induk.id = tb_personil.induk_id
                WHERE tb_induk.id = ".$data['induk']['id']." AND tb_personil.pin_absen IN (".$data['pin_absen'].")");

            if ($check['count'] > 0)
                $data['rekapbc'] = $this->backup_service->getRekapAllView($data['induk']['id'], $data['pin_absen']);
            else
                $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], true);

            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];

            $this->subView('tabelrekapc2bc', $data);
        }
    }
/*******************************START*AMBIL DATA BACKUP****************************************/

    public function script() {
        $data['title'] = '<!-- Script -->';
        $this->subView('script', $data);
    }
}