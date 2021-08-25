<?php

namespace app\adminsistem\controller;

use app\adminopd\model\servicemain;
use app\adminopd\model\laporan_service;
use app\adminopd\model\pegawai_service;
use app\adminopd\model\backup_service;
use app\adminsistem\model\servicemasterpresensi;
use system;
use comp;

class laporanselisih extends system\Controller {

    public function __construct() {
        parent::__construct();
        $this->servicemain = new servicemain;
        $session = $this->servicemain->cekSession();
        if ($session['status'] === true) {
            $this->laporan_service = new laporan_service();
            $this->pegawai_service = new pegawai_service();
            $this->backup_service = new backup_service();
            $this->materpresensi_service = new servicemasterpresensi();

            $this->setSession('SESSION_LOGIN', $session['data']);
            $this->login = $this->getSession('SESSION_LOGIN');
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

        $data['listTahun'] = comp\FUNC::numbSeries('2018', date('Y'));
        $data['listBulan'] = comp\FUNC::$namabulan1;
        $data['listSatker'] = $this->pegawai_service->getPilLokasi(['status_lokasi_kerja' => 1]);
        $data['listFormat'] = ['tabeltppselisih_v3' => 'Selisih V3', 'tabeltppselisih_v2' => 'Selisih V2', 'tabeltppdesbc_v1' => 'TPP Des V1'];
        $this->showView('index', $data, 'theme_admin');
    }

    protected function tabeltpp() {
        $input = $this->post(true);
        if ($input) {
            switch ($input['format']) {
                case 'tabeltppselisih_v2':
                    $this->tabeltppselisih_v2($input, true);
                    break;
                case 'tabeltppselisih_v3':
                    $this->tabeltppselisih_v3($input, true);
                    break;
                case 'tabeltppdesbc_v1':
                    $this->tabeltppdesbc_v1($input);
                    break;
                default:
                    echo 'Format tidak ditemukan';
            }
        }
    }

    protected function tabeltppselisih_v2($input, $verified) {
        if ($verified) {
            $input['tingkat'] = '6';
            $input['format'] = 'TPP';
            $input['satker'] = $this->pegawai_service->getDataSatker($input['kdlokasi']);
            foreach ($input as $key => $i) :
                $data[$key] = $i;
            endforeach;

            $data['laporan'] = $this->laporan_service->getLaporan($data);
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

            // backup
            $data['tppbackup'] = $this->backup_service->getTppTerima($input);

            $this->subView('tabeltpp2021', $data);
        }
    }

    //Laporan TPP Versi 3 dengan penambahan poin kinerja
    protected function tabeltppselisih_v3($input, $verified) {
        if ($verified) {
            $input['tingkat'] = '6';
            $input['format'] = 'TPP';
            $input['satker'] = $this->pegawai_service->getDataSatker($input['kdlokasi']);

            foreach ($input as $key => $i) :
                $data[$key] = $i;
            endforeach;

            $data['laporan'] = $this->laporan_service->getLaporan($data);
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
            
            //ambil data kinerja
//            $url = 'http://pamomong.pekalongankota.go.id/e-kinerja-beta/super/api/';
//            $method = 'poin_pns';
//            $accesskey = ['kinerja-key' => 'OFV6Y1NualM3dWZBRHZuaFhySDBVQWZYd29JNTZ0'];
//            $request = array('pin' => $data['personil'], 'tahun' => $input['tahun'], 'bulan' => $input['bulan']);
//            $kinerja = $this->webadapter->callAPI($url, $method, $accesskey, $request);
//            $poin = [];
//            if (!empty($kinerja)) {
//                $arrNip = array_column($kinerja['data'], 'nip');
//                $arrPoin = array_column($kinerja['data'], 'poin');
//                $poin = array_combine($arrNip, $arrPoin);
//            }

            $data['kinerja'] = $poin;

            $data['kenabpjs'] = $this->laporan_service->getDataSetting('maks_tpp_kena_bpjs');
            $data['pajak'] = $this->laporan_service->getArraypajak();
            $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], true);
            $data['bendahara'] = $this->laporan_service->getBendahara($input['kdlokasi']);
            $data['kepala'] = $this->laporan_service->getKepala($input['kdlokasi']);
            $data['pilbendahara'] = (isset($bendahara_parent)) ? array_merge($bendahara_satker, $bendahara_parent) : $bendahara_satker;

            // backup
            $data['tppbackup'] = $this->backup_service->getTppTerima($input);

            $this->subView('tabeltpp2021', $data);
        }
    }

    protected function tabeltppdesbc_v1($input) {
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

        $arrPin = array_column($data['pegawai']['value'], 'pin_absen');
        $data['personil'] = implode(',', $arrPin);
        $data['tpp'] = $this->backup_service->getDataTpp($data['induk']['id']);
        $data['laporan'] = $this->backup_service->getLaporan($data['induk']['id']);

        //ambil dari presensi backup
        $data['rekap'] = $this->backup_service->getRekapAllView($data['induk']['id']);

        $data['download'] = 0;
        $data['tpp_periodik'] = $this->materpresensi_service->getDataTppForm(5);
        $data['tingkat'] = $data['tpp_periodik']['tingkat'];
        $data['kenabpjs'] = $this->laporan_service->getDataSetting('maks_tpp_kena_bpjs');
//        comp\FUNC::showPre($data['rekap']); exit;

        $this->subView('tabeltppbcall_v1', $data);
    }

    protected function script() {
        $data['title'] = '<!-- Script -->';
        $this->subView('script', $data);
    }

}
