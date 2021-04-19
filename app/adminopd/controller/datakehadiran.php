<?php

namespace app\adminopd\controller;

use app\adminopd\model\servicemain;
use app\adminopd\model\pegawai_service;
use app\adminopd\model\presensi_service;
use system;
use comp;

class datakehadiran extends system\Controller {

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
        $data['title'] = 'Data Kehadiran Pegawai';
        $data['listPersonil'] = $this->pegawai_service->getPilPersonil($this->login['kdlokasi']);
        $data['listMesin'] = $this->presensi_service->getPilKrit('view_mesin', $this->login, array('key' => 'id_mesin', 'value' => 'nama_mesin'));
        $this->showView('index', $data, 'theme_admin');
    }

    protected function tabel() {
        $input = $this->post(true);
        if ($input) {
            $input['cari'] = array('key' => 'nama_personil', 'value' => $input['cari']);
            $input['kdlokasi'] = $this->login['kdlokasi'];
            $data['dataPersonil'] = $this->pegawai_service->getTabelKrit('view_user_pns', $input);
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
            $dataMesin = $this->presensi_service->getDataKrit('tb_mesin', $input);
            $limitUpdate = $this->presensi_service->getSetting('tgl_awal_update');
            $ip = $dataMesin['ip_mesin'];
            $port = $dataMesin['port_mesin'];
            $pswdComp = $dataMesin['password_mesin'];

            // Render riwayat
            $logTemp['id_mesin'] = $input['id_mesin'];
            $logTemp['temp_id'] = date('YmdHis') . $input['id_mesin'];

            if (self::create_log($logTemp)) {
                $getLog = self::get_log($logTemp);
                $buffer = "";
                $errno = "";
                $errstr = "";
                $newLog = 0;

                $Connect = fsockopen($ip, $port, $errno, $errstr, 1);
                if (!$Connect) {
                    // Fail connect log
                    $logTemp['method'] = '3';
                    $message = (self::update_log($logTemp)) ?
                            'Koneksi ke mesin tidak dapat dilakukan' :
                            'Koneksi ke mesin dan database riwayat gagal dilakukan';
                    $msg = array('status' => 'error', 'info' => 'Gagal', 'message' => $message);
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
                        if ($PIN != "" && $date >= $limitUpdate) {
                            $time = substr($dateTime, 11, 8); //waktu absen pada mesin
                            // insert semua data finger ke database
                            $log['pin_absen'] = $PIN;
                            $log['tanggal_log_presensi'] = $date;
                            $log['jam_log_presensi'] = $time;
                            $log['status_log_presensi'] = $status;
                            $log['status_log_verified'] = $status;
                            $log['verifikasi'] = $verified;
                            $log['id_mesin'] = $dataMesin['id_mesin'];
                            $log['update_id'] = $getLog['id_update'];

                            $insert = $this->presensi_service->save('tb_log_presensi', $log);
                            if ($insert['error']) {
                                $newLog++;
                            }
                            unset($log);
                        }
                    }

                    // Resume log update
                    $jumlahRecord = count($buffer) - 1;
                    $logTemp['method'] = '1';
                    $logTemp['count_log'] = $jumlahRecord;
                    $logTemp['count_inserted'] = $newLog;
                    self::update_log($logTemp);

                    // Alert message
                    $lastUpdate = $this->presensi_service->getDataLastUpdate($dataMesin['id_mesin']);
                    $textLastUpdate = comp\FUNC::tanggal(current($lastUpdate), 'long_date');

                    if ($jumlahRecord > 0 && $newLog == 0) {
                        $msg = array(
                            'status' => 'warning',
                            'info' => 'Perhatian',
                            'message' => 'Tidak ada data baru yang tambahkan. Jumlah data mesin ' . $jumlahRecord,
                            'last' => comp\FUNC::HTMLchip($textLastUpdate, 1)
                        );
                    } elseif ($jumlahRecord > 0 && $newLog > 0) {
                        $msg = array(
                            'status' => 'success',
                            'info' => 'Sukses',
                            'message' => 'Data berhasil diupdate. Jumlah data mesin ' . $jumlahRecord . ', data baru ' . $newLog,
                            'last' => comp\FUNC::HTMLchip($textLastUpdate)
                        );
                    } else {
                        $msg = array(
                            'status' => 'danger',
                            'info' => 'Maaf',
                            'message' => 'Terjadi kesalahan ketika melakukan update',
                            'last' => comp\FUNC::HTMLchip($textLastUpdate, 2),
                        );
                    }
                }
            }

            echo json_encode($msg);
        }
    }

    protected function detail() {
        $input = $this->post(true);
        if ($input) {
            $arrRecordLap = array('sdate' => $input['sdate'], 'edate' => $input['edate'], 'pin_absen' => array($input['pin_absen'] => $input['pin_absen']));
            $data['dataTabel'] = $this->presensi_service->getTabelRecordPersonil($input);
            $data['dataLaporan'] = $this->presensi_service->getArrayRecord($arrRecordLap);
            $this->subView('tabel', $data);
        }
    }

    public function getDetailPersonil() {
        $input = $this->post(true);
        if ($input) {
            $data = $this->pegawai_service->getDataPersonil($input['pin_absen']);
            $data['sdate'] = date('Y-m-01');
            $data['edate'] = date('Y-m-t');
            header('Content-Type: application/json');
            echo json_encode($data);
        }
    }

    public function getDetailRecord() {
        $input = $this->post(true);
        if ($input) {
            $arrRecordLap = array('sdate' => $input['sdate'], 'edate' => $input['edate'], 'pin_absen' => array($input['pin_absen'] => $input['pin_absen']));
            $data['dataTabel'] = $this->presensi_service->getTabelRecordPersonil($input);
            $data['dataLaporan'] = $this->presensi_service->getArrayRecord($arrRecordLap);
//            comp\FUNC::showPre($data['dataLaporan']); exit;
            $this->subView('record', $data);
        }
    }

    protected function form() {
        $input = $this->post(true);
        if ($input) {
            $data['dataForm'] = $this->presensi_service->getDataKrit('tb_log_presensi', $input);
            $this->subView('form', $data);
        }
    }

    protected function simpan() {
        $input = $this->post(true);
        if ($input) {
            $data = $this->presensi_service->getDataKrit('tb_log_presensi', $input);
            if (!empty($data['pin_absen'])) {
                $data['status_log_presensi'] = $input['status_presensi'];
                $result = $this->presensi_service->save_update('tb_log_presensi', $data);
                return $result;
            } else {
                return 'data gagal';
            }
        }
    }

    protected function spinner() {
        $data['title'] = '<!-- Loading -->';
        $this->subView('spinner', $data);
    }

    protected function script() {
        $this->subView('script', array());
    }

    function create_log($data) {
        $logTemp = $this->presensi_service->getTabel('tb_log_update');
        $logTemp['mesin_id'] = $data['id_mesin'];
        $logTemp['temp_id'] = $data['temp_id'];
        $logTemp['method'] = '0';
        $logTemp['dateAdd'] = date('Y-m-d H:i:s');
        $logTemp['author'] = $this->login['username'];
        $result = $this->presensi_service->save('tb_log_update', $logTemp);
        return $result['error'];
    }

    function update_log($data) {
        $logTemp = $this->presensi_service->getDataKrit('tb_log_update', ['temp_id' => $data['temp_id']]);
        foreach ($logTemp as $key => $val) {
            if (isset($data[$key])) {
                $logTemp[$key] = $data[$key];
            }
        }
        $logTemp['dateEnd'] = date('Y-m-d H:i:s');
        $result = $this->presensi_service->save_update('tb_log_update', $logTemp);
        return $result['error'];
    }

    function get_log($data) {
        $result = $this->presensi_service->getDataKrit('tb_log_update', ['temp_id' => $data['temp_id']]);
        return $result;
    }

}
