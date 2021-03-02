<?php

namespace app\pengawas\controller;

use app\pengawas\model\servicemain;
use app\pengawas\model\pegawai_service;
use app\pengawas\model\presensi_service;
use system;
use comp;

class updatemesin extends system\Controller {

    public function __construct() {
        parent::__construct();
        $this->servicemain = new servicemain();
        $session = $this->servicemain->cekSession();
        if ($session['status'] === true) {
            $this->pegawai_service = new pegawai_service();
            $this->presensi_service = new presensi_service();
            $this->setSession('SESSION_LOGIN', $session['data']);
            $this->login = $this->getSession('SESSION_LOGIN');
        } else {
            $this->setSession('SESSION_RELOAD', true);
            $this->redirect($this->link('login'));
        }
    }

    protected function index() {
        $data['title'] = 'Pembaruan Data Mesin Fingerprint';
        $data['subtitle'] = 'Tabel Mesin Fingerprint';

        $cari = array();
        $data['pil_kel_mesin'] = $this->presensi_service->getPilKelMesin($cari);
        $this->showView('index', $data, 'theme_admin');
    }

    protected function tabel() {
        $input = $this->post(true);
        if ($input) {
            $data['title'] = '';
            $data['input'] = $input;
            $data['dataTabel'] = $this->presensi_service->getTabelMesin($input);
            $this->subView('tabel', $data);
        }
    }

    function Parse_Data($data, $p1, $p2) {
        $data = " " . $data;
        // echo $data;

        $awal = strpos($data, $p1);

        if ($awal != "") {
            $awal = strpos($data, $p1) + strlen($p1);
            $akhir = strpos($data, $p2);

            $panjang = $akhir - $awal;

            $hasil = substr($data, $awal, $panjang);
            return $hasil;
        }
    }

    protected function update() {
        set_time_limit(0);
        $input = $this->post(true);
        if ($input) {

            $dataMesin = $this->presensi_service->getDataKrit('view_mesin', $input);
            $ip = $dataMesin['ip_mesin'];
            $port = $dataMesin['port_mesin'];
            $pswdComp = $dataMesin['password_mesin'];

            $buffer = "";
            $errno = "";
            $errstr = "";
            $arrLog = array();
            $jumlahRecord = 0;
            $valData['ip'] = $ip;
            $valData['port'] = $port;
            $valData['pswd'] = $pswdComp;

            $Connect = fsockopen($ip, $port, $errno, $errstr, 1);
            if (!$Connect) {
                $msg = array('status' => 'error', 'info' => 'Gagal', 'message' => 'Koneksi ke mesin tidak dapat dilakukan');
                
            } else {
                $soap_request = "<GetAttLog>
                                <ArgComKey xsi:type=\"xsd:string\">" . $pswdComp . "</ArgComKey>
                                <Arg><PIN xsi:type=\"xsd:integer\">all</PIN> 
                                </Arg>                  
                            </GetAttLog>";


                $newLine = "\r\n";

                fputs($Connect, "POST /iWsService HTTP/1.0" . $newLine);
                fputs($Connect, "Content-Type: text/xml" . $newLine);
                fputs($Connect, "Content-Length: " . strlen($soap_request) . $newLine . $newLine);
                fputs($Connect, $soap_request);

                while ($Response = fgets($Connect, 1024)) {
                    $buffer = $buffer . $Response;
                }
                $buffer = $this->Parse_Data($buffer, "<GetAttLogResponse>", "</GetAttLogResponse>");

                $buffer = explode("\r\n", $buffer);

                for ($a = 1; $a < count($buffer); $a++) {
                    $data = $this->Parse_Data($buffer[$a], "<Row>", "</Row>");
                    $PIN = $this->Parse_Data($data, "<PIN>", "</PIN>");
                    $dateTime = $this->Parse_Data($data, "<DateTime>", "</DateTime>");
                    $verified = $this->Parse_Data($data, "<Verified>", "</Verified>");
                    $status = $this->Parse_Data($data, "<Status>", "</Status>");

                    $date = substr($dateTime, 0, 10); //tanggal absen pada mesin
                    if ($PIN != "" && $date > '2017-12-31') {
                        $time = substr($dateTime, 11, 8); //waktu absen pada mesin
                        // insert semua data finger ke database
                        $log['pin_absen'] = $PIN;
                        $log['tanggal_log_presensi'] = $date;
                        $log['jam_log_presensi'] = $time;
                        $log['status_log_presensi'] = $status;
                        $log['status_log_verified'] = $status;
                        $log['verifikasi'] = $verified;
                        $log['id_mesin'] = $dataMesin['id_mesin'];
//                        array_push($arrLog, $log);
                        $jumlahRecord++;
                        $this->presensi_service->save('tb_log_presensi', $log);
                    }
                }
                if ($jumlahRecord > 0) {
                    $msg = array('status' => 'success', 'info' => 'Sukses', 'message' => 'Data berhasil diupdate', 'last' => $this->presensi_service->getDataLastUpdate($dataMesin['id_mesin']));
                } else if ($jumlahRecord < 1) {
                    $msg = array('status' => 'warning', 'info' => 'Perhatian', 'message' => 'Tidak ada data yang diintegrasikan');
                } else {
                    $msg = array('status' => 'error', 'info' => 'Gagal', 'message' => 'Koneksi ke mesin tidak dapat dilakukan');
                }
            }


            //header('Content-Type: application/json');
//            comp\FUNC::showPre($valData);
            echo json_encode($msg);
        }
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

    protected function script() {
        $this->subView('script', array());
    }

}
