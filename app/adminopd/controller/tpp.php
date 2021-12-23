<?php

namespace app\adminopd\controller;

use app\adminopd\model\servicemain;
use app\adminopd\model\laporan_service;
use app\adminopd\model\pegawai_service;
use app\adminopd\model\backup_service;
use app\adminopd\model\backup_des_service;
use system;
use comp;

class tpp extends system\Controller {

	public function __construct() {
        parent::__construct();
        $this->servicemain = new servicemain();
        $session = $this->servicemain->cekSession();

        if ($session['status'] === true) {
            $this->laporan_service = new laporan_service();
            $this->pegawai_service = new pegawai_service();
            $this->backup_service = new backup_service();
            $this->backup_des_service = new backup_des_service();

            $this->setSession('SESSION_LOGIN', $session['data']);
            $this->login = $this->getSession('SESSION_LOGIN');
            $satker = $this->laporan_service->getPilLokasi();
            $this->satker = $satker[$this->login['kdlokasi']];
        } else {
            $this->setSession('SESSION_RELOAD', true);
            $this->redirect($this->link('login'));
        }
    }

    private function getDesc($data) {
        $title = 'TPP';
        $breadcrumb = '<a href="'.$this->link().'" class="breadcrumb white-text" style="font-size: 13px;">Index</a>';

        $desc = [
            'TPP13' => [
                'title' => $title.' Ke-13',
                'breadcrumb' => $breadcrumb.'<a class="breadcrumb white-text" style="font-size: 13px;">'.$title.' Ke-13</a>',
                'judul_tpp' => 'DAFTAR PENERIMAAN TAMBAHAN PENGHASILAN KE-13'
            ],
            'TPP14' => [
                'title' => $title.' Ke-14',
                'breadcrumb' => $breadcrumb.'<a class="breadcrumb white-text" style="font-size: 13px;">'.$title.' Ke-14</a>',
                'judul_tpp' => 'DAFTAR PENERIMAAN TAMBAHAN PENGHASILAN KE-14'
            ],
            'TPPDES' => [
                'title' => $title.' Desember '. $data['tahun'],
                'breadcrumb' => $breadcrumb.'<a class="breadcrumb white-text" style="font-size: 13px;">'.$title.' Desember '. $data['tahun'].'</a>',
                'judul_tpp' => 'DAFTAR PENERIMAAN TAMBAHAN PENGHASILAN'
            ]
        ];

        return $desc[$data['jenis_tpp']];
    }

    protected function cetak() {
        $kd_tpp = $_GET['p4']; //get data jenis tpp

        $tpp = $this->laporan_service->getData("SELECT * FROM tb_tpp WHERE kd_tpp = ?
            AND tampil = 1
        ", [$kd_tpp]);

        if ($tpp['count'] == 0)
            $this->redirect('adminopd');

        $data = $tpp['value'][0];
        $data += $this->getDesc($data);
        $data['kdlokasi'] = $this->login['kdlokasi'];
        $data['satker'] = $this->satker;
        $data['bendahara'] = $this->laporan_service->getBendahara($this->login['kdlokasi']);

        $this->showView('cetak', $data, 'theme_admin');
    }

    public function cetaktpp() 
    {
        $input = $this->post(true);
        if ($input) {
            foreach ($input as $key => $i)
                $data[$key] = $i;

            $tpp = $this->laporan_service->getData("SELECT * FROM tb_tpp WHERE kd_tpp = ?
                AND tampil = 1
            ", [$data['kd_tpp']]);

            if ($tpp['count'] == 0)
                $this->redirect('adminopd');

            $data += $tpp['value'][0];
            $data += $this->getDesc($data);
            $data['kdlokasi'] = $this->login['kdlokasi'];
            $data['satker'] = $this->satker;
            $data['format'] = 'B';

            $data['bendahara'] = $this->laporan_service->getBendahara($data['kdlokasi']);
            $data['kepala'] = $this->laporan_service->getKepala($data['kdlokasi']);
            // comp\FUNC::showPre($data['bendahara']);exit;

            //ambil tambahan data pilih bendahara
            //$data['pilbendahara'] = [];
            $data['pilbendahara'] = $this->laporan_service->getDataPersonilSatker(['kdlokasi' => $data['bendahara']['lokasi_induk']])['value'];
            // $get = $this->pegawai_service->getData('SELECT kdlokasi_parent FROM tref_lokasi_kerja WHERE kdlokasi = "'.$data['kdlokasi'].'" LIMIT 1', []);
            // if ($get['count'] == 1 && !empty($get['value'][0]['kdlokasi_parent']) && $get['value'][0]['kdlokasi_parent']) {
            //     $parent = $get['value'][0]['kdlokasi_parent'];
            //     $data['pilbendahara'] = $this->laporan_service->getDataPersonilSatker(['kdlokasi' => $parent])['value'];
            // }

            $data['custom'] = [
                'awal' => $data['tgl_awal'], 
                'akhir' => $data['tgl_akhir']
            ];

            switch ($input['kd_tpp']) {
                case '5':
                    $data['koreksi'] = $this->laporan_service->getDataVarLaporan('koreksibpjs2020');
                    $data['view'] = 'tabeltpp2020';
//                    comp\FUNC::showPre($data['koreksi']['196504091986032019']['data1']); exit;
                    break;
                case '6':
                    $data['view'] = 'tabeltpp2021';
                    break;
                default:
                    $data['view'] = 'tabeltpp';
            }

            if ($data['potongan'])
                $data['view'] .= 'pot';

            /*if ($data['bulan'] < date('m')) { //ambil dari bakcup
                $data['view'] .= 'bc';
                $this->showtppbc($data);
            } else
                $this->showtpp($data);*/

            //ambil data pegawai yg dikecualikan
            $dataKecuali = $this->laporan_service->getDataTppKecuali($data);
            $p = '';
            $data['kecuali'] = [];
            if ($dataKecuali['count'] > 0) {
                $data['kecuali'] = array_map(function ($i) {
                    return $i['nipbaru'];
                }, $dataKecuali['value']);
            }

            $data['induk'] = $this->backup_des_service->getDataInduk_V3($data);
            if ($data['induk']) {
                $data['view'] .= 'bc';
                $this->showtppbc($data);
            } else {
                $this->showtpp($data);
            }
        
        }

    }

    private function showtpp($data) {
        $versi = $this->laporan_service->getDataVersi('history_of_report_tpp_rules', $data);
            switch ($versi['data_1']) {
                case 'v1':
                    $this->showtpp_v1($data, true);
                    break;
                case 'v3':
                    $this->showtpp_v3($data, true);
                    break;
            }
    }

    private function showtpp_v1($data) {
        $data['pegawai'] = $this->laporan_service->getDataPersonilTpp($data);
        $data['personil'] = '';
        if ($data['pegawai']['count'] > 0) {
            $personil = array_map(function ($i) {
                return $i['pin_absen'];
            }, $data['pegawai']['value']);

            $data['personil'] = implode(',', $personil);
        }

        $data['kenabpjs'] = $this->laporan_service->getDataSetting('maks_tpp_kena_bpjs');
        $data['pajak'] = $this->laporan_service->getArraypajak();
        $data['laporan'] = $this->laporan_service->getLaporan($data);
        $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], $data['potongan'], $data['custom']);
        $data['bendahara'] = $this->laporan_service->getBendahara($data['kdlokasi']);
        $data['kepala'] = $this->laporan_service->getKepala($data['kdlokasi']);
        
        $this->subView($data['view'], $data);
    }

    private function showtpp_v3($data) {
        $data['pegawai'] = $this->laporan_service->getDataPersonilTpp_v2($data);
        $data['personil'] = '';
        if ($data['pegawai']['count'] > 0) {
            $personil = array_map(function ($i) {
                return $i['pin_absen'];
            }, $data['pegawai']['value']);

            $data['personil'] = implode(',', $personil);
        }

        $data['satker'] = $this->pegawai_service->getDataSatker($this->login['kdlokasi']);
        $data['kenabpjs'] = $this->laporan_service->getDataSetting('maks_tpp_kena_bpjs');
        $data['pajak'] = $this->laporan_service->getArraypajak();
        $data['laporan'] = $this->laporan_service->getLaporan($data);
        $data['rekap'] = $this->laporan_service->getRekapAll_v3($data, $data['laporan'], $data['potongan'], $data['custom']);
        $data['bendahara'] = $this->laporan_service->getBendahara($data['kdlokasi']);
        $data['kepala'] = $this->laporan_service->getKepala($data['kdlokasi']);
        // comp\FUNC::showPre($data);exit;

        $this->subView($data['view'], $data);
    }

    private function showtppbc($data) {
        /*$data['induk'] = $this->backup_service->getDataInduk($data);
        if (!$data['induk']) {
            $this->subView('notfound', $data);
            exit;
        }*/

        $data['satker'] = $data['induk']['singkatan_lokasi'];
        $data['pegawai'] = $this->backup_des_service->getDataPersonilBatch_v2($data, true);
        $data['personil'] = '';
        if ($data['pegawai']['count'] > 0) {
            $personil = array_map(function ($i) {
                return $i['pin_absen'];
            }, $data['pegawai']['value']);

            $data['personil'] = implode(',', $personil);
        }

        $data['tpp'] = $this->backup_des_service->getDataTpp($data['induk']['id']);
        $data['laporan'] = $this->backup_des_service->getLaporan($data['induk']['id']);
        $data['rekap'] = $this->backup_des_service->getRekapAllView($data['induk']['id']);
        
        $this->subView($data['view'], $data);
    }

    protected function presensi() {
        $kd_tpp = $_GET['p4']; //get data jenis tpp

        $tpp = $this->laporan_service->getData("SELECT * FROM tb_tpp WHERE kd_tpp = ? AND tampil = 1", [$kd_tpp]);

        if ($tpp['count'] == 0)
            $this->redirect('adminopd');

        $data = $tpp['value'][0];

        $data += $this->getDesc($data);
        $data['kdlokasi'] = $this->login['kdlokasi'];
        $data['satker'] = $this->satker;

        $namabulan = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
        $data['namabulan'] = $namabulan[$data['bulan']-1];
        $data['tingkattpp'] = $data['tingkat'];

        //check backup date
        // $data['induk'] = $this->backup_service->getDataInduk($data);
//        comp\FUNC::showPre($data); exit;
        $this->showView('presensi', $data, 'theme_admin');
    }

    public function cetakpresensi() {
        $input = $this->post(true);
        if ($input) {
            foreach ($input as $key => $i)
                $data[$key] = $i;

            $tpp = $this->laporan_service->getData("SELECT * FROM tb_tpp WHERE kd_tpp = ? AND tampil = 1", [$data['kd_tpp']]);

            if ($tpp['count'] == 0)
                $this->redirect('adminopd');

            $data += $tpp['value'][0];
            $data += $this->getDesc($data);
            $data['kdlokasi'] = $this->login['kdlokasi'];
            $data['satker'] = $this->pegawai_service->getDataSatker($this->login['kdlokasi']);
            $data['format'] = 'B';
            $data['kepala'] = $this->laporan_service->getKepala($data['kdlokasi'], false);
            $data['adminopd'] = $this->laporan_service->getAdminopd($data['kdlokasi'], $this->login['nipbaru']);
            $data['custom'] = [
                'awal' => $data['tgl_awal'], 
                'akhir' => $data['tgl_akhir']
            ];

            $data['laporan'] = $this->laporan_service->getLaporan($data);
            $data['induk'] = $this->backup_service->getDataInduk($data);
            // $data['induk'] = false;

            //admbil dari data backupan
            if ($data['induk'] && isset($data['laporan']['final']) && $data['laporan']['final'] != '') 
                $this->showpresensibc($data);
            else
                $this->showpresensi($data);
        
        }
    }

    protected function showpresensi($data) {
        $data['pegawai'] = $this->laporan_service->getDataPersonilSatker($data);
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

        $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], $data['potongan'], $data['custom']);
        $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];
        // comp\FUNC::showPre($data); exit;
        $this->subView($view, $data);
    }

    protected function showpresensibc($data) {
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
            $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], $data['potongan'], $data['custom']);

        $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];

        $this->subView($view, $data);
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

    public function script() {
        $data['title'] = '<!-- Script -->';
        $this->subView('script', $data);
    }

    public function getJSON() {
        $input = $this->post(true);
        if ($input) {
            switch ($input['op']) {
                case 'checkInduk':
                    $input['kdlokasi'] = $this->login['kdlokasi'];
                    $data = $this->backup_service->getDataInduk($input);
                    $result = ($data) ? ['status' => true] : ['status' => true];
                    break;
                default:
                    $result = array();
            }
            echo json_encode($result);
        }
    }
}