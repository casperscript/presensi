<?php

namespace app\adminsistem\controller;

use app\adminsistem\model\servicemain;
use app\adminsistem\model\servicemasterpegawai;
use system;
use comp;

class jadwal extends system\Controller {

    public function __construct() {
        parent::__construct();
        $this->servicemain = new servicemain();
        $this->svpegawai = new \app\adminopd\model\servicemasterpegawai();
        $session = $this->servicemain->cekSession();
        if ($session['status'] === true) {
            $this->setSession('SESSION_LOGIN', $session['data']);
            $this->login = $this->getSession('SESSION_LOGIN');
        } else {
            $this->setSession('SESSION_RELOAD', true);
            $this->redirect($this->link('login'));
        }
    }

    protected function index() {
        $data['title'] = 'Jadwal';
        $data['subtitle'] = 'Halaman Master - Jadwal';
        $data['table_title'] = 'Tabel Master Jadwal';
        $data['breadcrumb'] = '<nav class="custom">
          <div class="nav-wrapper">
            <div class="col s12">
              <a href="' . $this->link() . '" class="breadcrumb">Beranda</a>
              <a href="#!" class="breadcrumb">Master Jadwal</a>
            </div>
          </div>
        </nav>';
        $this->showView('index', $data, 'theme_admin');
    }

    protected function tabel() {
        $input = $this->post(true);
        if ($input) {
//            $dataTabel = $this->servicemain->getTabelBarang($input);
            //$data['title'] = 'Total Data : ' . $dataTabel['jmlData'] . ' Pengguna';
            //$data = array_merge($data, $dataTabel);
            $data['title'] = '';
            $this->subView('tabel', $data);
        }
    }

    public function form() {
        $input = $this->post(true);
        if ($input) {
            $data['form_title'] = (!(empty($input['id_jadwal']))) ? 'Ubah Data Jadwal' : 'Tambah Data Jadwal';
            //$data['dataTabel'] = $this->servicemain->getDataKategoriForm($input['id_kategori']);
            $this->subView('form', $data);
        }
    }

    public function simpan() {
        $input = $this->post(true);
        if ($input) {
            $data = $this->servicemain->getDataBarangForm($input['id_barang']);
            foreach ($data as $key => $value) {
                if (isset($input[$key])) {
                    $data[$key] = $input[$key];
                }
            }
            $result = $this->servicemain->save_update('tb_barang', $data);
            $error_msg = ($result['error']) ? array('status' => 'success', 'message' => 'Data berhasil disimpan') : array('status' => 'error', 'message' => 'Data gagal disimpan');
            echo json_encode($error_msg);
        }
    }

    protected function hapus() {
        $input = $this->post(true);
        if ($input) {
            $idKey = array('id_barang' => $input['id']);
            $result = $this->servicemain->delete('tb_barang', $idKey);
            $error_msg = ($result['error']) ? array('status' => 'success', 'message' => 'Data berhasil dihapus') :
                    array('status' => 'error', 'message' => 'Data gagal dihapus');
            echo json_encode($error_msg);
        }
    }

    public function script() {
        $data['title'] = '<!-- Script -->';
        $this->subView('script', $data);
    }

    public function migrasiPersonal() {
        $cpns2020 = $this->svpegawai->getData('SELECT * FROM cpns_2020', []);
        $field = $this->svpegawai->getTabel('texisting_personal');

        foreach ($cpns2020['value'] as $key => $val) :
            foreach ($field as $keyF => $valF) :
                if (isset($val[$keyF])) :
                    $dPeg[$keyF] = $val[$keyF];
                endif;
            endforeach;
            $result = $this->svpegawai->save_update('texisting_personal', $dPeg);
            if ($result['error']) :
                echo $dPeg['nipbaru'] . ' => ok <br />';
            else:
                echo $dPeg['nipbaru'] . ' => gagal <br />';
            endif;
        endforeach;
    }

    public function migrasiKepegawaian() {
        $cpns2020 = $this->svpegawai->getData('SELECT * FROM cpns_2020', []);
        $field = $this->svpegawai->getTabel('texisting_kepegawaian');
        $numb = 1;
        foreach ($cpns2020['value'] as $key => $val) :
            foreach ($field as $keyF => $valF) :
                if (isset($val[$keyF])) :
                    $dPeg[$keyF] = $val[$keyF];
                endif;
            endforeach;
            $result = $this->svpegawai->save_update('texisting_kepegawaian', $dPeg);
            if ($result['error']) :
                echo $numb . ' ' . $dPeg['nipbaru'] . ' => ok <br />';
            else:
                echo $numb . ' ' . $dPeg['nipbaru'] . ' => gagal <br />';
            endif;
            $numb++;
        endforeach;
    }

    public function migrasiPengguna() {
        $cpns2020 = $this->svpegawai->getData('SELECT * FROM cpns_2020', []);
        $numb = 1;
        foreach ($cpns2020['value'] as $key => $val) :
            $data['username'] = $val['nipbaru'];
            $data['password'] = comp\FUNC::encryptor(self::migrasiRand());
            $data['nipbaru'] = $val['nipbaru'];
            $data['kdlokasi'] = $val['kdlokasi'];
            $data['grup_pengguna_kd'] = 'KDGRUP05';
            $data['status_pengguna'] = 'enable';
            
            $result = $this->servicemain->save_update('tb_pengguna', $data);
            if ($result['error']) :
                echo $numb . ' ' . $data['nipbaru'] . ' => ok <br />';
            else:
                echo $numb . ' ' . $data['nipbaru'] . ' => gagal <br />';
            endif;
            $numb++;
        endforeach;
    }

    public function migrasiRand() {
        $char[] = 'bcdfghjklmnpqrstvwxyz';
        $char[] = 'aiueo';
        $char[] = 'BCDFGHJKLMNPQRSTVWXYZ';
        $char[] = 'AIUEO';
        $char[] = '0123456789';
        $char[] = '0123456789';
        $result = '';
        foreach ($char as $val) {
            $result .= $val[rand(0, strlen($val) - 1)];
        }
        return $result;
    }

}

?>
