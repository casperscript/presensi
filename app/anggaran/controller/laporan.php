<?php

namespace app\anggaran\controller;

use app\anggaran\model\servicemain;
use app\anggaran\model\laporan_service;
use app\anggaran\model\pegawai_service;
use app\anggaran\model\backup_service;
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
        $data['breadcrumb'] = '<a href="' . $this->link() . '" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Laporan</a>';

        $data['bulan'] = ((int) date('m') == 1 ? 12 : (int) date('m') - 1);
        $data['tahun'] = ((int) date('m') == 1 ? (int) date('Y') - 1 : date('Y'));

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
        $data['breadcrumb'] = '<a href="' . $this->link() . '" class="breadcrumb white-text" style="font-size: 13px;">'
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

            $data['laporan'] = $this->laporan_service->getLaporan($data);
            $data['induk'] = $this->backup_service->getDataInduk($input);
            //admbil dari data backupan
            if ($data['induk'] && isset($data['laporan']['final']) && $data['laporan']['final'] != '') {
                $this->tabelpresensibc($input);
                exit;
            }

            if ($data['jenis'] == 1)
                $view = 'tabelmasuk';
            elseif ($data['jenis'] == 2)
                $view = 'tabelapel';
            elseif ($data['jenis'] == 3)
                $view = 'tabelpulang';

            $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], true);
            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];
            $data['satker'] = $this->laporan_service->getPilLokasi()[$input['kdlokasi']];

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

            $data['format'] = 'A';
            $data['jenis'] = '';
            $data['personil'] = $input['pin_absen'];
            $data['satker'] = $this->laporan_service->getPilLokasi()[$input['kdlokasi']];

            //ambil ttd
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

            $data['format'] = 'B';
            $data['jenis'] = '';
            $data['personil'] = $input['pin_absen'];
            $data['satker'] = $this->laporan_service->getPilLokasi()[$input['kdlokasi']];

            //ambil ttd
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
            $data['pilbendahara'] = [];
            $get = $this->pegawai_service->getData('SELECT kdlokasi_parent FROM tref_lokasi_kerja WHERE kdlokasi = "' . $input['kdlokasi'] . '" LIMIT 1', []);
            if ($get['count'] == 1 && !empty($get['value'][0]['kdlokasi_parent']) && $get['value'][0]['kdlokasi_parent']) {
                $parent = $get['value'][0]['kdlokasi_parent'];
                $data['pilbendahara'] = $this->laporan_service->getDataPersonilSatker(['kdlokasi' => $parent])['value'];
            }

            $data['kenabpjs'] = $this->laporan_service->getDataSetting('maks_tpp_kena_bpjs');
            $data['pajak'] = $this->laporan_service->getArraypajak();
            $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], true);
            $data['satker'] = $this->laporan_service->getPilLokasi()[$input['kdlokasi']];
            $data['bendahara'] = $this->laporan_service->getBendahara($input['kdlokasi']);
            $data['kepala'] = $this->laporan_service->getKepala($input['kdlokasi']);

            $this->subView('tabeltpp', $data);
        }
    }

    protected function tabelrekaptpp() {
        $input = $this->post(true);
        if ($input) {
            foreach ($input as $key => $i) {
                $data[$key] = $i;
            }

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
            
            $data['kenabpjs'] = $this->laporan_service->getDataSetting('maks_tpp_kena_bpjs');
            $data['pajak'] = $this->laporan_service->getArraypajak();
            $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], true);
            $data['satker'] = $this->laporan_service->getPilLokasi()[$input['kdlokasi']];
            $this->subView('tabelrekaptpp', $data);
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

    /*     * *****************************START*AMBIL DATA BACKUP*************************************** */

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

            $data['format'] = 'A';
            $data['jenis'] = '';
            $data['personil'] = $input['pin_absen'];

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
            $data['format'] = 'B';
            $data['jenis'] = '';
            $data['personil'] = $input['pin_absen'];

            //ambil ttd
            if ($data['tingkat'] == 6 && $data['bulan'] == 1 && $data['tahun'] == 2018)
                $data['tingkat'] = 3;

            $data['laporan'] = $this->backup_service->getLaporan($data['induk']['id']);
            $check = $this->backup_service->getData("SELECT tb_presensi.* FROM tb_presensi 
                JOIN tb_personil ON tb_personil.id = tb_presensi.personil_id
                JOIN tb_induk ON tb_induk.id = tb_personil.induk_id
                WHERE tb_induk.id = " . $data['induk']['id'] . " AND tb_personil.pin_absen IN (" . $data['pin_absen'] . ")");

            if ($check['count'] > 0)
                $data['rekapbc'] = $this->backup_service->getRekapAllView($data['induk']['id'], $data['pin_absen']);
            else
                $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], true);

            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];

            $this->subView('tabelrekapc2bc', $data);
        }
    }

    protected function tabeltppbc($input) {
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
            //ambil tambahan data pilih bendahara
            $data['pilbendahara'] = [];
            $get = $this->pegawai_service->getData('SELECT kdlokasi_parent FROM tref_lokasi_kerja WHERE kdlokasi = "' . $input['kdlokasi'] . '" LIMIT 1', []);
            if ($get['count'] == 1 && !empty($get['value'][0]['kdlokasi_parent']) && $get['value'][0]['kdlokasi_parent']) {
                $parent = $get['value'][0]['kdlokasi_parent'];
                $data['pilbendahara'] = $this->laporan_service->getDataPersonilSatker(['kdlokasi' => $parent])['value'];
            }

            //bulan jan dn feb masih uji coba
            $period = $data['bulan'] . $data['tahun'];
            if ($period == '12018' || $period == '22018')
                $data['tingkat'] = 6;

            $data['laporan'] = $this->backup_service->getLaporan($data['induk']['id']);

            $check = $this->backup_service->getData("SELECT tb_presensi.* FROM tb_presensi 
                JOIN tb_personil ON tb_personil.id = tb_presensi.personil_id
                WHERE induk_id = ?", [$data['induk']['id']]);

            if ($check['count'] == $data['pegawai']['count']) {
                //ambil dari presensi backup
                $data['rekap'] = $this->backup_service->getRekapAllView($data['induk']['id']);
                $this->subView('tabeltppbcall', $data);
            } else {
                $data['pajak'] = $this->laporan_service->getArraypajak();
                $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], true);
                $this->subView('tabeltppbc', $data);
            }
        }
    }

    public function updateBendaharabc() {
        $input = $this->post(true);
        if ($input) {
            $result = $this->backup_service->save_update('tb_tpp', $input);

            $error_msg = ($result['error']) ? array('status' => 'success', 'message' => 'Bendahara pengeluaran berhasil diubah') : array('status' => 'error', 'message' => 'Bendahara pengeluaran gagal diubah');
            header('Content-Type: application/json');
            echo json_encode($error_msg);
        }
    }

    /*     * *****************************START*AMBIL DATA BACKUP*************************************** */

    public function script() {
        $data['title'] = '<!-- Script -->';
        $this->subView('script', $data);
    }

}
