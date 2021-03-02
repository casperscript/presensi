<?php

namespace app\kepalabkppd\controller;

use app\kepalabkppd\model\servicemain;
use app\kepalabkppd\model\apelpagi_service;
use system;

class apelpagi extends system\Controller {

    public function __construct() {
        parent::__construct();
        $this->servicemain = new servicemain();
        $session = $this->servicemain->cekSession();

        if ($session['status'] === true) {
            $this->apelpagi_service = new apelpagi_service();

            $this->setSession('SESSION_LOGIN', $session['data']);
            $this->login = $this->getSession('SESSION_LOGIN');
        } else {
            $this->setSession('SESSION_RELOAD', true);
            $this->redirect($this->link('login'));
        }
    }

    protected function index() {
        $data['title'] = 'Apel Pagi';
        $data['pil_kel_satker'] = ['' => '-- Pilih Kelompok Lokasi Kerja --'] + $this->apelpagi_service->getPilKelSatker([]);
        $data['pil_satker'] = ['' => '-- Pilih Satuan Kerja --'];
        $data['breadcrumb'] = '<a href="'.$this->link().'" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Apel Pagi</a>';
        $this->showView('index', $data, 'theme_admin');
    }

    protected function tabel() {
        $input = $this->post(true);
        if ($input) {
            $data['title'] = '';
            $data['dataTabel'] = $this->apelpagi_service->getTabelPersonil($input);
            $data['respon'] = $this->ambildata($input);
            $this->subView('tabel', $data);
        }
    }

    protected function ambildata($input) {
        $dataPersonil = $this->apelpagi_service->getDataPersonilSatker($input);
        $personil = '';
        if (isset($dataPersonil['value'])) {
            $personil = array_map(function ($i) {
                return $i['pin_absen'];
            }, $dataPersonil['value']);

            $personil = implode(',', $personil);
        }

        //get array data jadwal
        $jamapel = $this->apelpagi_service->getArrayJam();

        $last = '';
        $tglterakhir = $this->apelpagi_service->getDataLastUpdate($personil);
        if ($tglterakhir && $tglterakhir['last_update'])
            $last = 'AND tanggal_log_presensi >= "'.$tglterakhir['last_update'] .'"';
        else
            $last = ' AND tanggal_log_presensi >= "2017-12-01"';

        $logs = $this->apelpagi_service->getData('SELECT * FROM tb_log_presensi WHERE status_log_presensi = 2 AND pin_absen IN ('.$personil.') '.$last.'', []);

        $jumlahRecord = 0;
        if ($logs['count'] > 0)
            foreach ($logs['value'] as $log) {
                $apel['pin_absen'] = $log['pin_absen'];
                $apel['tanggal_apel'] = $log['tanggal_log_presensi'];
                $apel['jam_apel'] = $log['jam_log_presensi'];
                $apel['status_apel'] = $this->apelpagi_service->compare($log['tanggal_log_presensi'], $log['jam_log_presensi'], $jamapel);
                $this->apelpagi_service->save('tb_record_apel', $apel);
                $jumlahRecord++;
            }
        if ($jumlahRecord > 0)
            $tglterakhir = $this->apelpagi_service->getDataLastUpdate($personil);

        return $tglterakhir;
    }

    public function getPilLokasiFromKelLokasi() {
        $input = $this->post(true);
        if ($input) {
            $valData = $this->apelpagi_service->getPilLokasi($input);
            header('Content-Type: application/json');
            echo json_encode($valData);
        }
    }

    public function getDetailPersonil() {
        $input = $this->post(true);
        if ($input) {
            $data = $this->apelpagi_service->getDataPersonil($input['pin_absen']);
            header('Content-Type: application/json');
            echo json_encode($data);
        }
    }

    public function getDetailRecord() {
        $input = $this->post(true);
        if ($input) {
            $input['sdate'] = $input['sdate_submit'];
            $input['edate'] = $input['edate_submit'];
            $data['pin_absen'] = $input['pin_absen'];
            $data['dataTabel'] = $this->apelpagi_service->getTabelRecordPersonil($input);
            $data['jadwal'] = $this->apelpagi_service->getArrayJam();

            $data['default'] = '07.15.01 - 08.00';
            $default = $this->apelpagi_service->getData('SELECT * FROM tb_jam_apel WHERE is_default=1', []);
            if ($default['count'] > 0)
                $data['default'] = $default['value'][0]['mulai_apel'] . ' - ' . $default['value'][0]['akhir_apel'];

            $this->subView('record', $data);
        }
    }

    public function script() {
        $data['title'] = '<!-- Script -->';
        $this->subView('script', $data);
    }

}

?>
