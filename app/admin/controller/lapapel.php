<?php

namespace app\admin\controller;

use app\admin\model\servicemain;
use app\admin\model\pegawai_service;
use app\admin\model\presensi_service;
use app\admin\model\apelpagi_service;
use comp;
use system;
use system\Files;

class lapapel extends system\Controller {

    public function __construct() {
        parent::__construct();
        $this->servicemain = new servicemain();
        $this->file = new Files();
        $session = $this->servicemain->cekSession();

        if ($session['status'] === true) {
            $this->pegawai_service = new pegawai_service();
            $this->presensi_service = new presensi_service();
            $this->apelpagi_service = new apelpagi_service();

            $this->setSession('SESSION_LOGIN', $session['data']);
            $this->login = $this->getSession('SESSION_LOGIN');
        } else {
            $this->setSession('SESSION_RELOAD', true);
            $this->redirect($this->link('login'));
        }
    }

    protected function index() {
        $data['title'] = 'Laporan Apel Pagi';
        // nav index
        $data['pil_kel_satker'] = ['' => '-- Pilih Kelompok Lokasi Kerja --'] + $this->pegawai_service->getPilKelSatker([]);
        $data['pil_satker'] = ['' => '-- Pilih Satuan Kerja --'];
        $data['breadcrumb'] = '<a href="'.$this->link().'" class="breadcrumb white-text" style="font-size: 13px;">'
        . 'Index</a><a style="font-size: 13px;" class="breadcrumb white-text">Laporan</a><a class="breadcrumb white-text" style="font-size: 13px;">'
        . 'Apel Pagi</a>';
        
        // nav tabel
        $data['sdate'] = date('1 F, Y');
        $data['edate'] = date('t F, Y');
        
        $this->showView('index', $data, 'theme_admin');
    }

    protected function tabel() {
        $input = $this->post(true);
        if ($input) {
            $data['title'] = '';
            $data['kdlokasi'] = $input['kdlokasi'];
            $data['dataTabel'] = $this->pegawai_service->getTabelPersonil($input);
            $this->subView('tabel', $data);
        }
    }

    public function buatlaporan() {
        $input = $this->post(true);
        if ($input) {
            $data['sdate'] = date('Y-m-d', strtotime($input['sdate']));
            $data['edate'] = date('Y-m-d', strtotime($input['edate']));
            $data['dataUser'] = $this->pegawai_service->getArrayPersonal($input['pin_absen']);
            $data['dataSatker'] = $this->pegawai_service->getDataSatker($input['kdlokasi']);
            $data['dataApel'] = $this->apelpagi_service->getArrayRecord($input);
            $data['jadwal'] = $this->apelpagi_service->getArrayJam();
            $data['default'] = '07:15:01 - 08:00:00';

            $filename = $input['kdlokasi'] . date('YmdHis');
            $data['filename_f'] = $this->dir_arsip . $filename . '.pdf';
            $data['filename_f_id'] = $filename;
            $data['filename_d'] = 'Lap_Apelpagi_(' . $input['sdate_submit'] . '_sd_' . $input['edate_submit'] . ')_' . date('His') . '.pdf';

            $data_simpan = $this->presensi_service->getTabel('tb_arsip');
            $data_simpan['id'] = $filename;
            $data_simpan['jenis'] = 'apelpagi';
            $data_simpan['kdlokasi'] = $input['kdlokasi'];
            $data_simpan['filename'] = $filename . '.pdf';
            $data_simpan['dateAdd'] = date('Y-m-d H:i:s');
            $data_simpan['author'] = $this->login['username'];
            $data_simpan['ip'] = comp\FUNC::getUserIp();

            $this->presensi_service->save('tb_arsip', $data_simpan);
            $this->subView('pdfApelpagi', $data);
            echo '/unduhlaporan/'.$filename.'/'.$data['filename_d'];
        }
    }

    public function unduhlaporan() {
        $opt = [
            'folder' => $this->dir_arsip,
            'fileName' => $_GET['p3'] . '.pdf',
            'fileDownload' => $_GET['p4']
        ];
        $download = $this->file->download($opt);

        return $download;
    }

    public function getPilLokasiFromKelLokasi() {
        $input = $this->post(true);
        if ($input) {
            $data = $this->pegawai_service->getData('SELECT kdlokasi, singkatan_lokasi FROM tref_lokasi_kerja WHERE (kd_kelompok_lokasi_kerja = ?)', array($input['id_kelompok']));
            if ($data['count'] > 0) {
                foreach ($data['value'] as $val) {
                    $valData[$val['kdlokasi']] = $val['singkatan_lokasi'];
                }
            } else {
                $valData[] = '';
            }
            header('Content-Type: application/json');
            echo json_encode($valData);
        }
    }

    public function laporan() {
        $input = $this->post(true);

        //default
        $input['kdlokasi'] = 'G09011';
        $input['bulan'] = 1;
        $input['tahun'] = 2018;
        $input['jenis'] = 2;

        if ($input) {
            $data['pegawai'] = $this->apelpagi_service->getDataPersonilSatker($input);
            $input['personil'] = '';
            if ($data['pegawai']['count'] > 0) {
                $personil = array_map(function ($i) {
                    return $i['pin_absen'];
                }, $data['pegawai']['value']);

                $input['personil'] = implode(',', $personil);
            }

            $data['bln'] = $input['bulan'];
            $data['tahun'] = $input['tahun'];

            $data['rekap'] = $this->apelpagi_service->getRecordPersonil($input);
            $this->subView('laporan', $data);
        }
    }

    protected function script() {
        $this->subView('script', array());
    }
}