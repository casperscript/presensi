<?php

namespace app\adminsistem\controller;

use app\adminsistem\model\servicemain;
use app\adminsistem\model\pegawai_service;
use app\adminsistem\model\presensi_service;
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
            $data = $this->presensi_service->getTabelMesin($input);
//            comp\FUNC::showPre($data);exit;
            
            $arrIdMesin = implode(', ', array_column($data['dataTabel'], 'id_mesin'));
            $data['update'] = $this->presensi_service->getLastUpdate($arrIdMesin);
            $this->subView('tabel', $data);
        }
    }

    protected function update() {
        set_time_limit(0);
        $input = $this->post(true);
        if ($input) {
            
            // Render info mesin
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
//                    $message = self::update_log($logTemp);
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
                            'message' => 'Tidak ada data baru yang diintegrasikan. Jumlah data mesin ' . $jumlahRecord,
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
            } else {
                $msg = array(
                    'status' => 'error', 
                    'info' => 'Gagal', 
                    'message' => 'Koneksi ke mesin tidak dapat dilakukan',
                    'last' => 'Koneksi gagal'
                );
            }
            echo json_encode($msg);
        }
    }
    
    public function test() {
        $date = 'aa';
        $tes = (strtotime($date) == false) ? 'gagal' : 'benar';
        echo $tes;
    }

    public function formImport() {
        $input = $this->post(true);
        if ($input) {
            $data['form_title'] = 'Import Data';
            $data['id_mesin'] = $input['id_mesin'];
            $data['message'] = 'Masukkan data .dat dengan field.<br \>[pin absen], [tanggal & jam], [verifikasi], [status finger]';
            $this->subView('formImport', $data);
        }
    }

    public function import() {
        $input = $this->post(true);
        if ($input) {
            $file = $_FILES['file'];
            $data['id_mesin'] = $input['id_mesin'];
            $data['nm_log'] = date('YmdHis'). '_' .$input['id_mesin'] . '_' . substr($file['name'], 0, 10);
            $path_file = $this->dir_arsipatt . $data['nm_log'];

            $moved = move_uploaded_file($file['tmp_name'], $path_file);
            if ($moved) {
                $data['temp_id'] = date('YmdHis') . $input['id_mesin'];
                $tes = self::parse_dat($path_file, $data);
                comp\FUNC::showPre($tes);
                // $msg = array('title' => 'Sukses', 'text' => 'Data berhasil di upload', 'type' => 'success');

            } else {
                $msg = array('title' => 'Error', 'text' => 'Terjadi kesalahan ketika upload data', 'type' => 'danger');
            }
            echo json_encode($tes);
            // comp\FUNC::showPre($file);
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

    function parse_dat ($path_file, $input) {
        $logTemp = $this->presensi_service->getTabel('tb_log_update');
        $logTemp['temp_id'] = $input['temp_id'];
        $logTemp['dateAdd'] = date('Y-m-d H:i:s');
        $logTemp['author'] = $this->login['username'];
        $tempUpdate = $this->presensi_service->save('tb_log_update', $logTemp);
        
        if ($tempUpdate['error']) {
            $jumlahRecord = 0;
            $newLog = 0;
            $src = fopen($path_file, "r");
            while (!feof($src)) {
                $buffer = fgetcsv($src, 1000);
                $data = preg_split('/\s+/', $buffer[0]);

                if (isset($data[1]) && $data[1] > 0) {
                    $log['pin_absen'] = $data[1];
                    $log['tanggal_log_presensi'] = $data[2];
                    $log['jam_log_presensi'] = $data[3];
                    $log['status_log_presensi'] = $data[5];
                    $log['status_log_verified'] = $data[5];
                    $log['verifikasi'] = $data[4];
                    $log['id_mesin'] = $input['id_mesin'];
                    $jumlahRecord++;

                    $save = $this->presensi_service->save('tb_log_presensi_dat', $log);
                    if ($save['error']) {
                        $log['status'] = 'Inserted';
                        $newLog++;
                    } else {
                        $log['status'] = 'Fail';
                    }
                    $result[] = $save;
                    unset($log);
                }
            }
            
            // Resume log update
            $getTemp = $this->presensi_service->getDataKrit('tb_log_update', ['temp_id' => $input['temp_id']]);
            $logTemp['mesin_id'] = $input['id_mesin'];
            $logTemp['method'] = '2';
            $logTemp['log_count'] = $jumlahRecord;
            $logTemp['new_log'] = $newLog;
            foreach ($getTemp as $key => $val) {
                if (isset($logTemp[$key])) {
                    $getTemp[$key] = $logTemp[$key];
                }
            }
            $result = $this->presensi_service->save_update('tb_log_update', $getTemp);
            
//            $result = array('title' => 'Sukses', 'text' => 'Data berhasil di upload', 'type' => 'success');
        } else {
            $result = array('');
        }
        
//        $updTemp['temp_id'] = $input['temp_id'];
        return $buffer;
    }

}
