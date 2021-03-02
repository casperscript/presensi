<?php

namespace app\admin\controller;

use app\admin\model\servicemain;
use app\admin\model\apelpagi_service;
use system;

class jamapelpagi extends system\Controller {

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
        $data['title'] = 'Jam Apel Pagi';
        $data['breadcrumb'] = '<a href="'.$this->link().'" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Apel Pagi</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Jam Apel Pagi</a>';
        $this->showView('index', $data, 'theme_admin');
    }

    protected function tabel() {
        $input = $this->post(true);
        if ($input) {
            $dataTabel = $this->apelpagi_service->getTabelJamApel($input);
            $data['title'] = 'Total Data : ' . $dataTabel['jmlData'] . ' Data';
            $data = array_merge($data, $dataTabel);
            $this->subView('tabel', $data);
        }
    }

    public function form() {
        $input = $this->post(true);
        if ($input) {
            $data['form_title'] = (!(empty($input['id_jam_apel']))) ? 'Ubah Data Jam Apel' : 'Tambah Data Jam Apel';
            $data['op'] = (!(empty($input['id_jam_apel']))) ? 'edit' : 'input';
            $data['dataTabel'] = $this->apelpagi_service->getDataJamApelForm($input['id_jam_apel']);
            $this->subView('form', $data);
        }
    }

    public function simpan() {
        $input = $this->post(true);
        if ($input) {
            $data = $this->apelpagi_service->getDataJamApelForm($input['id_jam_apel']);
            $old = $data;
            foreach ($data as $key => $value) {
                if (isset($input[$key])) {
                    $data[$key] = $input[$key];
                }
            }

            $result = $this->apelpagi_service->save_update('tb_jam_apel', $data);
            if ($result) {
                if ($input['id_jam_apel'])
                    $this->updateStatus($old);

                $this->updateStatus($input);
            }

            $error_msg = ($result['error']) ? array('status' => 'success', 'message' => 'Data berhasil disimpan') : array('status' => 'error', 'message' => 'Data gagal disimpan');
            echo json_encode($error_msg);
        }
    }

    protected function updateStatus($input)
    {
        $get = $this->apelpagi_service->getData('SELECT * FROM tb_record_apel WHERE tanggal_apel BETWEEN ? AND ?', [$input['tanggal_mulai'], $input['tanggal_akhir']]);

        if ($get['count'] > 0)
            $jamapel = $this->apelpagi_service->getArrayJam();

        foreach ($get['value'] as $i) {
            $idKey = [
                'pin_absen' => $i['pin_absen'],
                'tanggal_apel' => $i['tanggal_apel']
            ];
            $record['status_apel'] = $this->apelpagi_service->compare($i['tanggal_apel'], $i['jam_apel'], $jamapel);

            $result = $this->apelpagi_service->update('tb_record_apel', $record, $idKey);
        }

        return true;
    }

    protected function hapus() {
        $input = $this->post(true);
        if ($input) {
            $idKey = array('id_jam_apel' => $input['id_jam_apel']);
            $result = $this->apelpagi_service->delete('tb_jam_apel', $idKey);

            if ($result) 
                $this->updateStatus($input);

            $error_msg = ($result['error']) ? array('status' => 'success', 'message' => 'Data berhasil dihapus') :
                    array('status' => 'error', 'message' => 'Data gagal dihapus');
            echo json_encode($error_msg);
        }
    }

    public function script() {
        $data['title'] = '<!-- Script -->';
        $this->subView('script', $data);
    }
}