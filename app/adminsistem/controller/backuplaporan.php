<?php

namespace app\adminsistem\controller;

use app\adminsistem\model\servicemain;
use app\adminsistem\model\pegawai_service;
use app\adminsistem\model\laporan_service;
use app\adminsistem\model\backup_service;
use system;
use comp;

class backuplaporan extends system\Controller {
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

    protected function index() {
        $data['title'] = 'Backup Laporan Final';
        $data['breadcrumb'] = '<a href="'.$this->link().'" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Backup Laporan Final</a>';
        $this->showView('index', $data, 'theme_admin');
    }

    protected function lihat() {
        $data['title'] = 'Backup Laporan Final';
        $data['breadcrumb'] = '<a href="'.$this->link().'" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Backup Laporan Final</a>';

        $satker = $this->laporan_service->getPilLokasi();
        $params = explode('/', $_GET['p4']);
        if (!isset($satker[$params[0]])) {
            $this->showView('index', $data, 'theme_admin');
            exit;
        }

        $period = $params[1];
        $data['tahun'] = substr($period, -4);
        $data['bulan'] = str_replace($data['tahun'], '', $period);
        $data['kdlokasi'] = $params[0];
        $data['listTahun'] = comp\FUNC::numbSeries('2018', date('Y'));
        $data['satker'] = $satker[$params[0]];
        $this->showView('lihat', $data, 'theme_admin');
    }

    protected function tabellist() {
        $input = $this->post(true);
        if ($input) {
            foreach ($input as $key => $i)
                $data[$key] = $i;

            $data['induk'] = $this->backup_service->getData('SELECT * FROM tb_induk 
                WHERE bulan = "'.$input['bulan'].'" AND tahun = "'.$input['tahun'].'"');

            $check = $this->backup_service->getData('SELECT tb_induk.kdlokasi FROM tb_induk 
                JOIN tb_personil ON tb_personil.induk_id = tb_induk.id
                WHERE bulan = "'.$input['bulan'].'" AND tahun = "'.$input['tahun'].'" 
                AND tb_personil.backup_presensi = 1
                GROUP BY tb_induk.kdlokasi
            ');

            $data['sudah'] = [];
            foreach ($check['value'] as $i) {
                $data['sudah'][] = $i['kdlokasi'];
            }

            $data['belum'] = $this->backup_service->getBelumBackup($input, $data['induk']);
            $data['lokasi'] = $this->laporan_service->getPilLokasi();
            $this->subView('tabellist', $data);
        }
    }

    protected function tabelpresensiold() {
        $input = $this->post(true);
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
                $view = 'tabelmasuk';
            elseif ($data['jenis'] == 2)
                $view = 'tabelapel';
            elseif ($data['jenis'] == 3)
                $view = 'tabelpulang';

            $data['laporan'] = $this->backup_service->getLaporan($data['induk']['id']);
            $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], true);
            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];

            $this->subView($view, $data);
        }
    }

    protected function tabelpresensi() {
        $input = $this->post(true);
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
                $view = 'tabelmasuk';
            elseif ($data['jenis'] == 2)
                $view = 'tabelapel';
            elseif ($data['jenis'] == 3)
                $view = 'tabelpulang';

            $data['laporan'] = $this->backup_service->getLaporan($data['induk']['id']);
            $data['rekap'] = $this->backup_service->getRekapAllView($data['induk']['id']);

            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];

            $this->subView($view, $data);
        }
    }

    protected function tabelpersonil() {
        $input = $this->post(true);
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
            $this->subView('tabelpersonil', $data);
        }
    }

    protected function tabelrekapc1() {
        $input = $this->post(true);
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
            $data['rekap'] = $this->backup_service->getRekapAllView($data['induk']['id'], $data['pin_absen']);
            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];

            $this->subView('tabelrekapc1', $data);
        }
    }

    protected function tabelrekapc2() {
        $input = $this->post(true);
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
            $data['rekap'] = $this->backup_service->getRekapAllView($data['induk']['id'], $data['pin_absen']);
            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];

            $this->subView('tabelrekapc2', $data);
        }
    }

    protected function tabeltpp() {
        $input = $this->post(true);
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

            //bulan jan dn feb masih uji coba
            $period = $data['bulan'].$data['tahun'];
            if ($period == '12018' || $period == '22018')
                $data['tingkat'] = 6;

            $data['laporan'] = $this->backup_service->getLaporan($data['induk']['id']);
            $data['rekap'] = $this->backup_service->getRekapAllView($data['induk']['id']);

            $this->subView('tabeltpp', $data);
        }
    }

    protected function dobackup() {
        $input = $this->post(true);
        if ($input) {
            $input['pegawai'] = $this->laporan_service->getDataPersonilSatker($input);
            $input['personil'] = '';
            if ($input['pegawai']['count'] > 0) {
                $personil = array_map(function ($i) {
                    return $i['pin_absen'];
                }, $input['pegawai']['value']);

                $input['personil'] = implode(',', $personil);
            }

            $input['kenabpjs'] = $this->laporan_service->getDataSetting('maks_tpp_kena_bpjs');
            $result = $this->backup_service->dobackup($input);
            $error_msg = ($result['error']) ? array('status' => 'success', 'message' => 'Backup laporan berhasil.') : array('status' => 'error', 'message' => 'Maaf, backup laporan gagal.');

            header('Content-Type: application/json');
            echo json_encode($error_msg + ['page' => $input['page']]);
        }
    }

    protected function hapus() {
        $input = $this->post(true);
        if ($input) {
            $result = $this->backup_service->hapusBackup($input);
            $error_msg = $result ? array('status' => 'success', 'message' => 'Hapus backup laporan berhasil.') : array('status' => 'error', 'message' => 'Maaf, hapus backup laporan gagal.');

            header('Content-Type: application/json');
            echo json_encode($error_msg);
        }
    }

    public function script() {
        $data['title'] = '<!-- Script -->';
        $this->subView('script', $data);
    }

    public function savePresensi() { 
        $input = $this->post(true);
        if ($input) {
            foreach ($input as $key => $i)
                $data[$key] = $i;

            $data['induk'] = $this->backup_service->getDataInduk($data);
            if (!$data['induk']) {
                $this->subView('notfound', $data);
                exit;
            }

            $data['pegawai'] = $this->backup_service->getDataPersonilBatch($data, true);
            $data['personil'] = '';
            if ($data['pegawai']['count'] > 0) {
                $personil = array_map(function ($i) {
                    return $i['pin_absen'];
                }, $data['pegawai']['value']);

                $data['personil'] = implode(',', $personil);
            }

            $rekap = [];
            $laporan = $this->laporan_service->getLaporan($data);
            for ($i = 1; $i <= 6; $i++) {
                $data['tingkat'] = $i;
                $rekap[$i] = $this->backup_service->getRekapAll($data, $laporan, true);
            }

            $gagal = [];
            foreach ($data['pegawai']['value'] as $peg) {
                $tbpresensi = $this->backup_service->save_presensi($rekap, $peg['pin_absen'], $peg['id']);

                if ($tbpresensi['error'])
                    $tbpresensi = $this->backup_service->update('tb_personil', ['backup_presensi' => 1], ['id' => $peg['id']]);
                else {
                    $arrData = [
                        'id' => '',
                        'personil_id' => $peg['id'],
                        'keterangan' => $tbpresensi['message'],
                        'dateAdd' => date('Y-m-d H:i:s')
                    ];
                    $tbgagal = $this->backup_service->save('tb_gagal', $arrData);
                }
            }

            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success', 
                'message' => 'Backup data presensi berhasil.',
                'page' => $data['page']
            ]);
        }
    }


// --------------------------------------------------------------------------------//
    public function checkbackup() 
    { 
        exit;
        set_time_limit(0);
        $data = [
            'bulan' => 0,
            'tahun' => 2019
        ];

        $query = 'SELECT * FROM tb_induk WHERE bulan = ? AND tahun = ?';
        $get = $this->backup_service->getData($query, [$data['bulan'], $data['tahun']]);

        foreach ($get['value'] as $bc) {
            $induk_id = $bc['id'];
            $kdlokasi = $bc['kdlokasi'];

            $query = 'SELECT pp.*, s.urutan_sotk, 0 AS tampil_tpp

                FROM texisting_kepegawaian kp 
                JOIN view_presensi_personal pp ON pp.nipbaru = kp.nipbaru
                LEFT JOIN tref_sotk s ON s.kdsotk = pp.kdsotk

            WHERE pp.kdlokasi = ? AND kp.kd_stspeg = "03"
            ';
            $sts = $this->pegawai_service->getData($query, [$kdlokasi]);

            $data['pegawai'] = $sts;
            $data['personil'] = '';
            if ($sts['count'] > 0) {
                $personil = array_map(function ($i) {
                    return $i['pin_absen'];
                }, $data['pegawai']['value']);

                $data['personil'] = implode(',', $personil);
            } else
                continue;

            $rekap = [];
            $data['kdlokasi'] = $kdlokasi;
            $laporan = $this->laporan_service->getLaporan($data);
            for ($i = 1; $i <= 6; $i++) {
                $data['tingkat'] = $i;
                $rekap[$i] = $this->backup_service->getRekapAll($data, $laporan, true);
            }

            $result = $this->backup_service->save_cpns($induk_id, $data, $rekap);
            var_dump($result);
            echo "<br><br>";
        }
    }

    //--------------------------//
    public function getPot() {
        exit;
        $idKey = [6, 2018];
        $query = 'SELECT * FROM tb_induk WHERE bulan = ? AND tahun = ?';
        $dataArr = $this->backup_service->getData($query, $idKey);

        $update = [];
        foreach ($dataArr['value'] as $induk) {
            $query = 'SELECT tb_presensi.id, tb_personil.nominal_tp, 
                tb_personil.pajak_tpp, tb_presensi.sum_pot 
                FROM tb_personil 
                JOIN tb_presensi ON tb_personil.id = tb_presensi.personil_id
                WHERE induk_id = "'.$induk['id'] . '"
            ';
            $presensi = $this->backup_service->getData($query, $idKey);
            foreach ($presensi['value'] as $i) {
                $sum_pot = json_decode($i['sum_pot'], true);
                if (!isset($sum_pot[6]))
                    continue;

                $final = ($sum_pot[6]['all'] > 100 ? 100 : $sum_pot[6]['all']);
                $pot = ($final/100 * $i['nominal_tp']);
                $tpp_kotor = $i['nominal_tp'] - $pot;
                //remove whitespace-- ambil % pajak
                $pot_pajak = round($i['pajak_tpp'] * $tpp_kotor);

                $terima = $tpp_kotor - $pot_pajak;
                $update[] = [$i['id'], $final, $tpp_kotor, $terima];
            }
        }

        $sukses = 0; $gagal = [];
        foreach ($update as $u) {
            $data = [
                'pot_final' => $u[1],
                'tpp_kotor' => $u[2],
                'tpp_terima' => $u[3]
            ];
            $result = $this->backup_service->update('tb_presensi', $data, ['id' => $u[0]]);
            if ($result['error'])
                $sukses++;
            else
                $gagal[] = $u;
        }

        echo 'sukses :'.$sukses.'<br>';
        var_dump($gagal);
    }

    public function updateLog() {
        set_time_limit(0);
        if (!isset($_GET['p4'])) {
            echo 'missing parameter';
            exit;
        }

        $period = explode('-', $_GET['p4']);
        if (count($period) < 2) {
            echo 'wrong parameter';
            exit;
        }

        $bulan = $period[0];
        $tahun = $period[1];

        $idKey = [$bulan, $tahun];
        $query = 'SELECT * FROM tb_induk WHERE bulan = ? AND tahun = ?';
        $dataArr = $this->backup_service->getData($query, $idKey);

        $update = [];
        foreach ($dataArr['value'] as $induk) {
            $query = 'SELECT tb_personil.*, tb_presensi.id AS presensi_id FROM tb_personil 
                LEFT JOIN tb_presensi ON tb_personil.id = tb_presensi.personil_id
            WHERE induk_id = "'.$induk['id'] . '"
            ';
            $personil = $this->backup_service->getData($query, []);
            $p = [];
            foreach ($personil['value'] as $per) {
                $p[] = $per['pin_absen'];
            }

            $data = $induk;
            $data['personil'] = implode(',', $p);

            $rekap = [];
            $data['pegawai'] = $personil;
            $laporan = $this->laporan_service->getLaporan($data);
            for ($i = 1; $i <= 6; $i++) {
                $data['tingkat'] = $i;
                $rekap[$i] = $this->backup_service->getRekapAll($data, $laporan, true);
            }

            foreach ($personil['value'] as $updt) {
                if ($updt['presensi_id'])
                    $hapus = $this->backup_service->delete('tb_presensi', ['id' => $updt['presensi_id']]);
                $update = $this->backup_service->update_presensi($rekap, $updt);
                if (!$update['error'])
                    return json_encode($personil);
                else {
                    $this->backup_service->update('tb_personil', ['backup_presensi' => 1], ['id' => $updt['id']]);
                }
            }
        }

        echo 'sukses '. $bulan . '-'.$tahun;
        exit;
    }

    public function saveLog() {
        set_time_limit(0);
        if (!isset($_GET['p4'])) {
            echo 'missing parameter';
            exit;
        }

        $period = explode('-', $_GET['p4']);
        if (count($period) < 2) {
            echo 'wrong parameter';
            exit;
        }

        $data['bulan'] = $period[0];
        $data['tahun'] = $period[1];

        $idKey = [$data['bulan'], $data['tahun']];
        $query = 'SELECT * FROM tb_laporan WHERE bulan = ? AND tahun = ? 
            AND sah_final != "" AND dt_sah_final != "0000-00-00 00:00:00"
        ';
        $laporan = $this->laporan_service->getData($query, $idKey);

        $error = [];
        foreach ($laporan['value'] as $lap) {
            $data['kdlokasi'] = $lap['kdlokasi'];
            $key = [$data['bulan'], $data['tahun'], $data['kdlokasi']];
            $q = 'SELECT * FROM tb_induk WHERE bulan = ? AND tahun = ? AND kdlokasi = ?';
            $dataArr = $this->backup_service->getData($q, $key);

            if ($dataArr['count'] > 0)
                continue;

            $data['pegawai'] = $this->laporan_service->getDataPersonilSatker($data);
            $data['personil'] = '';
            if ($data['pegawai']['count'] > 0) {
                $personil = array_map(function ($i) {
                    return $i['pin_absen'];
                }, $data['pegawai']['value']);

                $data['personil'] = implode(',', $personil);
            }

            $result = $this->backup_service->dobackup($data, false);
            if(!$result['error']) {
                $error[] = $data['kdlokasi'];
            }
        }

        echo json_encode($error);

        return $this->updatecpns($data['bulan'], $data['tahun']);
    }

    public function updatecpns($bulan, $tahun)
    {
        set_time_limit(0);
        $data = [
            'bulan' => $bulan,
            'tahun' => $tahun
        ];

        //get data cpns
        $query = 'SELECT * FROM tb_induk WHERE bulan = ? AND tahun = ?';
        $get = $this->backup_service->getData($query, [$data['bulan'], $data['tahun']]);

        foreach ($get['value'] as $bc) {
            $induk_id = $bc['id'];
            $kdlokasi = $bc['kdlokasi'];

            $query = 'SELECT pp.*, s.urutan_sotk

                FROM texisting_kepegawaian kp 
                JOIN view_presensi_personal pp ON pp.nipbaru = kp.nipbaru
                LEFT JOIN tref_sotk s ON s.kdsotk = pp.kdsotk

            WHERE pp.kdlokasi = ? AND kp.kd_stspeg = "03"
            ';
            $sts = $this->pegawai_service->getData($query, [$kdlokasi]);

            $data['pegawai'] = $sts;
            $data['personil'] = '';
            if ($sts['count'] > 0) {
                $personil = array_map(function ($i) {
                    return $i['nipbaru'];
                }, $data['pegawai']['value']);

                $data['personil'] = implode(',', $personil);
            } else
                continue;

            //update
            $update = $this->backup_service->updatetampil($induk_id, $data['personil']);
            var_dump($induk_id . '-' .$data['bulan'].$data['tahun']. '-' . $bc['kdlokasi'] . '-' . $sts['count']. '-' .$update);
            echo '<br>';
        }

    }

    public function saveLogDes() {
        set_time_limit(0);
        if (!isset($_GET['p4'])) {
            echo 'missing parameter';
            exit;
        }

        $period = explode('-', $_GET['p4']);
        if (count($period) < 2) {
            echo 'wrong parameter';
            exit;
        }

        $data['bulan'] = $period[0];
        $data['tahun'] = $period[1];

        $idKey = [$data['bulan']-1, $data['tahun']];
        $query = 'SELECT * FROM tb_laporan WHERE bulan = ? AND tahun = ?
            AND sah_final != "" AND dt_sah_final != "0000-00-00 00:00:00"
        ';
        $laporan = $this->laporan_service->getData($query, $idKey);

        $error = [];
        foreach ($laporan['value'] as $lap) {
            $data['kdlokasi'] = $lap['kdlokasi'];
            /*$key = [$data['bulan'], $data['tahun'], $data['kdlokasi']];
            $q = 'SELECT * FROM tb_induk WHERE bulan = ? AND tahun = ? AND kdlokasi = ?';
            $dataArr = $this->backup_service->getData($q, $key);

            if ($dataArr['count'] > 0)
                continue;*/

            $data['pegawai'] = $this->laporan_service->getDataPersonilSatker($data);
            $data['personil'] = '';
            if ($data['pegawai']['count'] > 0) {
                $personil = array_map(function ($i) {
                    return $i['pin_absen'];
                }, $data['pegawai']['value']);

                $data['personil'] = implode(',', $personil);
            }

            $result = $this->backup_service->dobackup_des($data, false);
            if(!$result['error']) {
                $error[] = $data['kdlokasi'];
            }
        }

        echo json_encode($error);
        return $this->updatecpns($data['bulan'], $data['tahun']);
    }

    public function updateBpjs() {
        set_time_limit(0);
        if (!isset($_GET['p4'])) {
            echo 'missing parameter';
            exit;
        }

        $period = explode('-', $_GET['p4']);
        if (count($period) < 2) {
            echo 'wrong parameter';
            exit;
        }

        $kenabpjs = $this->laporan_service->getDataSetting('maks_tpp_kena_bpjs');

        $extra = '';
        $bulan = $period[0];
        $tahun = $period[1];
        //$extra = 'AND kdlokasi = "G09011"';
        $idKey = [$bulan, $tahun];
        $query = 'SELECT * FROM tb_induk WHERE bulan = ? AND tahun = ? '.$extra;
        $dataArr = $this->backup_service->getData($query, $idKey);

        $update = []; $sukses = 0;
        foreach ($dataArr['value'] as $induk) {
            $query = 'SELECT tb_personil.nipbaru, tb_presensi.* FROM tb_personil 
                LEFT JOIN tb_presensi ON tb_personil.id = tb_presensi.personil_id
            WHERE induk_id = "'.$induk['id'] . '"
            ';
            $presensi = $this->backup_service->getData($query, []);

            $data = $induk;
            $datapegawai = $this->backup_service->getDataPersonilTpp($induk);
            $gaji = [];
            foreach ($datapegawai['value'] as $q) {
                $gaji[$q['nipbaru']] = $q['totgaji'];
            }

            foreach ($presensi['value'] as $updt) {
                $update = $this->backup_service->update_bpjs($updt, $kenabpjs, $gaji);
                if (!$update['error'])
                    return json_encode($personil);
                else {
                    $sukses++;
                    echo 'sukses '. $bulan . '-'.$tahun . '-' . $updt['nipbaru'];
                }
            }
        }
        echo '<br>Jumlah : '. $sukses;
        exit;
    }
}