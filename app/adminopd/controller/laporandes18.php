<?php

namespace app\adminopd\controller;

use app\adminopd\model\servicemain;
use app\adminopd\model\laporan_service;
use app\adminopd\model\pegawai_service;
use system;

class laporandes18 extends system\Controller {

	public function __construct() {
        parent::__construct();
        $this->servicemain = new servicemain();
        $session = $this->servicemain->cekSession();

        if ($session['status'] === true) {
            $this->laporan_service = new laporan_service();
            $this->pegawai_service = new pegawai_service();

            $this->setSession('SESSION_LOGIN', $session['data']);
            $this->login = $this->getSession('SESSION_LOGIN');
            $satker = $this->laporan_service->getPilLokasi();
            $this->satker = $satker[$this->login['kdlokasi']];
        } else {
            $this->setSession('SESSION_RELOAD', true);
            $this->redirect($this->link('login'));
        }
    }

    protected function cetak() {
        $data['title'] = 'Cetak Laporan Desember 2018';
        $data['breadcrumb'] = '<a href="'.$this->link().'" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Cetak Laporan Desember 2018</a>';

        $data['satker'] = $this->satker;
        $this->showView('cetak', $data, 'theme_admin');
    }

    protected function tabelpresensi() {
        $input = $this->post(true);
        if ($input) {
            $input['kdlokasi'] = $this->login['kdlokasi'];
            $input['satker'] = $this->satker;
            foreach ($input as $key => $i)
                $data[$key] = $i;

            $data['pegawai'] = $this->laporan_service->getDataPersonilSatker($input);
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

            $data['laporan'] = $this->laporan_service->getLaporan($data);
            $data['rekap'] = $this->getRekapAll($data, $data['laporan'], true);
            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];
            $data['kepala'] = $this->laporan_service->getKepala($input['kdlokasi'], false);
            $data['adminopd'] = $this->laporan_service->getAdminopd($input['kdlokasi'], $this->login['nipbaru']);
            $data['allverified'] = $this->cekmod($data);

            $this->subView($view, $data);
        }
    }

    protected function cekmod($data) {
        $moderasi = $this->laporan_service->getArraymodAll($data, []);

        $hitungtgl = 14; $allverified = true;
        foreach ($data['pegawai']['value'] as $peg) {
            $key = $peg['pin_absen'];
            for ($i = 1; $i <= $hitungtgl; $i++) {
                if (isset($moderasi[$key][$i])) {
                    foreach ($moderasi[$key][$i] as $jnsmod => $modr) {
                        $ver = $moderasi[$key][$i][$jnsmod]['verified'];
                        if ($ver != null && ($ver == 0 || $ver == 3))
                            continue;

                        if ($ver == null)
                            $allverified = false;
                    }
                }
            }
        }
        return $allverified;
    }

    protected function tpp() {
        $data['title'] = 'Penerimaan TPP';
        $data['breadcrumb'] = '<a href="'.$this->link().'" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Penerimaan TPP</a>';

        $data['satker'] = $this->satker;
        $data['bendahara'] = $this->laporan_service->getBendahara($this->login['kdlokasi']);
        $this->showView('tpp', $data, 'theme_admin');
    }

    protected function tabeltpp() {
        $input = $this->post(true);
        if ($input) {
            $input['kdlokasi'] = $this->login['kdlokasi'];
            $input['satker'] = $this->satker;
            foreach ($input as $key => $i)
                $data[$key] = $i;
            
            $data['pegawai'] = $this->laporan_service->getDataPersonilTpp($input);
            $data['personil'] = '';
            if ($data['pegawai']['count'] > 0) {
                $personil = array_map(function ($i) {
                    return $i['pin_absen'];
                }, $data['pegawai']['value']);

                $data['personil'] = implode(',', $personil);
            }

            //ambil tambahan data pilih bendahara
            $data['pilbendahara'] = [];
            $get = $this->pegawai_service->getData('SELECT kdlokasi_parent FROM tref_lokasi_kerja WHERE kdlokasi = "'.$input['kdlokasi'].'" LIMIT 1', []);
            if ($get['count'] == 1 && !empty($get['value'][0]['kdlokasi_parent']) && $get['value'][0]['kdlokasi_parent']) {
                $parent = $get['value'][0]['kdlokasi_parent'];
                $data['pilbendahara'] = $this->laporan_service->getDataPersonilSatker(['kdlokasi' => $parent])['value'];
            }

            //bulan jan dn feb masih uji coba
            $period = $data['bulan'].$data['tahun'];
            if ($period == '12018' || $period == '22018')
                $data['tingkat'] = 6;

            $data['pajak'] = $this->laporan_service->getArraypajak();
            $data['laporan'] = $this->laporan_service->getLaporan($data);
            $data['rekap'] = $this->getRekapAll($data, $data['laporan'], true);
            $data['bendahara'] = $this->laporan_service->getBendahara($input['kdlokasi']);
            $data['kepala'] = $this->laporan_service->getKepala($input['kdlokasi']);
            $data['allverified'] = $this->cekmod($data);

            $this->subView('tabeltpp', $data);
        }
    }

    public function getPilLokasiFromKelLokasi() {
        $input = $this->post(true);
        if ($input) {
            $valData = $this->laporan_service->getPilLokasi($input);
            header('Content-Type: application/json');
            echo json_encode($valData);
        }
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

    protected function getRekapAll($data, $laporan, $hitungpot = false) {
        $moderasi = $this->laporan_service->getArraymodAll($data, $laporan);
        $libur = $this->laporan_service->getLibur($data);
        $data_pot = $this->laporan_service->getArraypot();
        //$hitungtgl = cal_days_in_month(CAL_GREGORIAN, $data['bulan'], $data['tahun']);
        $hitungtgl = 14;
        $hitungmod = $moderasi['hitung'];

        $data['pin_absen'] = $data['personil'];       
        $log = $this->laporan_service->getLogPersonil($data, ($data['format'] == 'TPP' ? 'A' : $data['format']));
        $masuk = $log['masuk'];
        $pulang = $log['pulang'];

        //apel
        $data['format'] = ($data['format'] == 'TPP' ? 'A' : $data['format']);
        //$apel = $this->laporan_service->getRecordPersonil($data);
        $apel = $this->laporan_service->apelpagi_service->getRecordApel($data);

        $allverified = true;
        foreach ($data['pegawai']['value'] as $peg) {
            $tot = 0; $key = $peg['pin_absen'];
            $sum_mk = 0; $sum_ap = 0; $sum_pk = 0;
            $pot_penuh = []; 
            $jumlah_tk = 0;
            for ($i = 1; $i <= $hitungtgl; $i++) {
                $tgl = $data['tahun'] . '-'. $data['bulan'] . '-' . $i;
                $hari = date("l", strtotime($tgl));

                $kd_masuk = ''; $kd_apel = ''; $kd_pulang = '';
                $pot_masuk = 0; $pot_apel = 0; $pot_pulang = 0;
                $color1 = ''; $color2 = ''; $color3 = '';
                $hl = false;
                if (isset($masuk[$key][$i])) {
                    if ($masuk[$key][$i] == 'HL')
                        $hl = true;
                    else 
                        $kd_masuk = $masuk[$key][$i];

                    if (in_array($kd_masuk, ['M2', 'M3', 'M4', 'M5', 'M0']))
                        $color1 = 'yellow accent-2';

                } elseif (!in_array($i, $libur) && strtotime($tgl) <= strtotime(date('Y-m-d'))) {
                    $color1 = 'yellow accent-2';
                    $kd_masuk = 'M0';
                }

                if (isset($apel[$key][$i])) {
                    if ($apel[$key][$i] != 'HL')
                        $kd_apel = $apel[$key][$i];

                    if ($kd_apel == 'A0')
                        $color2 = 'yellow accent-2';

                } elseif (!in_array($i, $libur) && strtotime($tgl) <= strtotime(date('Y-m-d'))) {
                    $color2 = 'yellow accent-2';
                    $kd_apel = 'A0';
                }

                if (isset($pulang[$key][$i])) {
                    if ($pulang[$key][$i] != 'HL')
                        $kd_pulang = $pulang[$key][$i];

                    if (in_array($kd_pulang, ['P2', 'P3', 'P4', 'P5', 'P0']))
                        $color3 = 'yellow accent-2';

                } elseif (!in_array($i, $libur) && strtotime($tgl) <= strtotime(date('Y-m-d'))) {
                    $color3 = 'yellow accent-2';                    
                    $kd_pulang = 'P0';
                }

                $gabung = false; $tampil_mod = true;
                if (in_array($i, $libur)) {
                    $tampil_mod = false;
                    $kd_masuk = 'HL'; $kd_apel = 'HL'; $kd_pulang = 'HL';
                    $color1 = ''; $color2 = ''; $color3 = '';
                    //libur nasional tpi finger
                    if (isset($masuk[$key][$i]) && $masuk[$key][$i] != 'HL') {
                        $tampil_mod = true;
                        $kd_masuk = $masuk[$key][$i];
                        if (in_array($kd_masuk, ['M2', 'M3', 'M4', 'M5', 'M0']))
                            $color1 = 'yellow accent-2';
                    }
                    if (isset($pulang[$key][$i]) && $pulang[$key][$i] != 'HL') {
                        $tampil_mod = true;
                        $kd_pulang = $pulang[$key][$i];
                        if (in_array($kd_pulang, ['P2', 'P3', 'P4', 'P5', 'P0']))
                            $color3 = 'yellow accent-2';
                    }
                } elseif (strtotime($tgl) > strtotime(date('Y-m-d'))) {
                    $tampil_mod = false;
                    $kd_masuk = ''; $kd_pulang = '';
                    $color1 = ''; $color2 = ''; $color3 = '';
                }

                if ($tampil_mod && isset($moderasi[$key][$i])) {
                    foreach ($moderasi[$key][$i] as $jnsmod => $modr) {
                        $ver = $moderasi[$key][$i][$jnsmod]['verified'];
                        if ($ver != null && ($ver == 0 || $ver == 3))
                            continue;

                        if ($ver == null)
                            $allverified = false;

                        if ($kd_masuk && ($jnsmod == 'JNSMOD04' || $jnsmod == 'JNSMOD01')) {
                            $color1 = 'red accent-3';
                            $kd_masuk = $modr['kode'];
                        }
                        if ($kd_apel && ($jnsmod == 'JNSMOD04' || $jnsmod == 'JNSMOD02')) {
                            $color2 = 'red accent-3';
                            $kd_apel = $modr['kode'];
                        }
                        if ($kd_pulang && ($jnsmod == 'JNSMOD04' || $jnsmod == 'JNSMOD03')) {
                            $color3 = 'red accent-3';
                            $kd_pulang = $modr['kode'];
                        }

                        //jk jenisnya semuanya atau kode moderasi masuk, apel, pulang sama dalam 1 hari maka potongan dijasikan 1
                        if ($jnsmod == 'JNSMOD04' || ($kd_apel == $kd_masuk && $kd_pulang == $kd_masuk))
                            $gabung = true;                        
                        /*
                        //jk jenisnya semuanya, potongan dijadikan 1
                        if ($jnsmod == 'JNSMOD04')
                            $gabung = true;
                        */
                    }
                }

                //jk M0 && A0 && P0 ---> jadi TK (tidak masuk kerja tanpa alasan yg sah)
                if ($kd_masuk == 'M0' && ($kd_apel == 'A0' || $kd_apel == 'NR') 
                    && $kd_pulang == 'P0') {
                    $kd_masuk = 'TK'; $kd_apel = 'TK'; $kd_pulang = 'TK';
                    $color2 = 'yellow accent-2';
                }

                if ($hitungpot) {
                    $hitung = 1;
                    if ($kd_masuk != 'M0')
                        $hitung = isset($hitungmod[$key][$kd_masuk]) ? $hitungmod[$key][$kd_masuk] : 1;
                    
                    if ($kd_masuk && isset($data_pot[$kd_masuk])) {                    
                        foreach ($data_pot[$kd_masuk] as $p) {
                            if ($hitung >= $p['minimal']) {
                                $pot_masuk = $p['pot'];
                                break;
                            }
                        }

                        if ($pot_masuk == 100)
                            $pot_penuh[] = $kd_masuk;
                    }

                    if ($kd_apel != 'A0')
                        $hitung = isset($hitungmod[$key][$kd_apel]) ? $hitungmod[$key][$kd_apel] : 1;
                    
                    if ($kd_apel && isset($data_pot[$kd_apel])) {
                        foreach ($data_pot[$kd_apel] as $p) {
                            if ($hitung >= $p['minimal']) {
                                $pot_apel = $p['pot'];
                                break;
                            }
                        }

                        if ($pot_apel == 100)
                            $pot_penuh[] = $kd_apel;

                        //jk kode sama, potongan jadi 1
                        if ($kd_apel == $kd_masuk)
                            $pot_apel = 0;
                    }

                    if ($kd_pulang != 'P0')
                        $hitung = isset($hitungmod[$key][$kd_pulang]) ? $hitungmod[$key][$kd_pulang] : 1;
                    
                    if ($kd_pulang && isset($data_pot[$kd_pulang])) {
                        foreach ($data_pot[$kd_pulang] as $p) {
                            if ($hitung >= $p['minimal']) {
                                $pot_pulang = $p['pot'];
                                break;
                            }
                        }

                        if ($pot_pulang == 100) 
                            $pot_penuh[] = $kd_pulang;

                        //jk kode sama, potongan jadi 1
                        if ($kd_pulang == $kd_masuk || $kd_pulang == $kd_apel)
                            $pot_pulang = 0;
                    }
                }

                if ($gabung) {
                    $pot_apel = 0; $pot_pulang = 0;
                }

                $subtot = $pot_masuk+$pot_apel+$pot_pulang;
                $all[$key][$i] = [
                    'mk' => [
                        'kode' => $kd_masuk,
                        'pot' => ($pot_masuk > 0 ? $pot_masuk : ''),
                        'color' => $color1
                    ], 
                    'ap' => [
                        'kode' => $kd_apel,
                        'pot' => ($pot_apel > 0 ? $pot_apel : ''),
                        'color' => $color2
                    ],
                    'pk' => [
                        'kode' => $kd_pulang,
                        'pot' => ($pot_pulang > 0 ? $pot_pulang : ''),
                        'color' => $color3
                    ],
                    'all' => ($subtot > 0 ? $subtot : '')
                ];

                $sum_mk += $pot_masuk; $sum_ap += $pot_apel; $sum_pk += $pot_pulang;

                if ($kd_masuk == 'TK')
                    $jumlah_tk++;

                if ($jumlah_tk >= 7)
                    $pot_penuh[] = 'TK';
            }

            $all[$key]['pot_penuh'] = array_unique($pot_penuh);
            
            if (count($pot_penuh) == 0) {
                $tot = ($sum_mk+$sum_ap+$sum_pk);
            } else {
                $implode = implode(",", $all[$key]['pot_penuh']);
                $tot = "100% (".$implode.")";
            }

            $all[$key]['sum_pot'] = [
                'mk' => $sum_mk, 'ap' => $sum_ap, 'pk' => $sum_pk,
                'all' => $tot
            ];
        }

        $all['allverified'] = $allverified;
        return $all;
    }
}