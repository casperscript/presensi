<?php

namespace app\adminopd\controller;

use app\adminopd\model\servicemain;
use app\adminopd\model\laporan_service;
use app\adminopd\model\pegawai_service;
use system;

class laporan extends system\Controller {

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

    protected function index() {
        $data['title'] = 'Verifikasi Laporan';
        $data['breadcrumb'] = '<a href="'.$this->link().'" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Verifikasi Laporan</a>';

        $data['satker'] = $this->satker;
        $data['bendahara'] = $this->laporan_service->getBendahara($this->login['kdlokasi']);
        $this->showView('index', $data, 'theme_admin');
    }

    protected function verifikasi() {
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

            $data['format'] = 'A'; $data['jenis'] = '';
            $data['laporan'] = $this->laporan_service->getLaporan($data);
            $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], true);
            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];

            $this->subView('verifikasi', $data);
        }
    }

    protected function laporanfinal() {
        $data['title'] = 'Laporan Final';
        $data['breadcrumb'] = '<a href="'.$this->link().'" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Laporan Final</a>';

        $data['satker'] = $this->satker;
        $data['bendahara'] = $this->laporan_service->getBendahara($this->login['kdlokasi']);
        $this->showView('final', $data, 'theme_admin');
    }

    protected function indexold() {
        $data['title'] = 'Laporan';
        $data['breadcrumb'] = '<a href="'.$this->link().'" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Laporan</a>';

        $data['satker'] = $this->satker;
        $data['bendahara'] = $this->laporan_service->getBendahara($this->login['kdlokasi']);
        $this->showView('indexold', $data, 'theme_admin');
    }

    protected function cetak() {
        $data['title'] = 'Cetak Laporan';
        $data['breadcrumb'] = '<a href="'.$this->link().'" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Cetak Laporan</a>';

        $data['satker'] = $this->satker;
        $this->showView('cetak', $data, 'theme_admin');
    }

    protected function tabelapelold() {
        $input = $this->post(true);
        if ($input) {
            $input['kdlokasi'] = $this->login['kdlokasi'];
            $data['pegawai'] = $this->laporan_service->getDataPersonilSatker($input);
            $input['personil'] = '';
            if ($data['pegawai']['count'] > 0) {
                $personil = array_map(function ($i) {
                    return $i['pin_absen'];
                }, $data['pegawai']['value']);

                $input['personil'] = implode(',', $personil);
            }

            foreach ($input as $key => $i)
                $data[$key] = $i;

            $data['satker'] = $this->satker;
            //$data['rekap'] = $this->apelpagi_service->getRecordPersonil($input);
            $data['rekap'] = $this->apelpagi_service->getRecordApel($input);
            $data['libur'] = $this->laporan_service->getLibur($input);

            //ambil ttd
            if ($data['tingkat'] == 6 && $data['bulan'] == 1 && $data['tahun'] == 2018)
                $data['tingkat'] = 3;

            $data['laporan'] = $this->laporan_service->getLaporan($input);
            //ambil moderasi
            $data['moderasi'] = $this->laporan_service->getArraymod($input, $data['laporan']);
            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];
            
            // added by husnanw
            // You may add the other officers like kepala opd, admin kota, and kep bkppd

            // CAUTION: Remove these comment lines for real implementation
            //$data["hwTtdAdminOpd"] = $this->laporan_service->getHusnanWTtd($this->login["nipbaru"]);
            //$data["hwStempelAdminOpd"] = $this->laporan_service->getHusnanWStempel($this->login["kdlokasi"]);

            // CAUTION: These scripts are only for example. Remove the scripts when implementing real implementation
            $data["hwTtdAdminOpd"] = $this->laporan_service->getHusnanWTtd("196112301986111001");
            $data["hwStempelAdminOpd"] = $this->laporan_service->getHusnanWStempel("G12002");

            $this->subView('tabelapelold', $data);
        }
    }

    protected function tabelmasukold() {
        $input = $this->post(true);
        if ($input) {
            $input['kdlokasi'] = $this->login['kdlokasi'];
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
            
            $data['rekap'] = $this->laporan_service->getLogSatker($data);
            $data['libur'] = $this->laporan_service->getLibur($input);
            $data['satker'] = $this->satker;

            //ambil ttd
            if ($data['tingkat'] == 6 && $data['bulan'] == 1 && $data['tahun'] == 2018)
                $data['tingkat'] = 3;
           
            $data['laporan'] = $this->laporan_service->getLaporan($input);
            //ambil moderasi
            $data['moderasi'] = $this->laporan_service->getArraymod($input, $data['laporan']);
            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];

            $this->subView('tabelmasukold', $data);
        }
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
            $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], true);
            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];

            $this->subView($view, $data);
        }
    }

    protected function tabelpulangold() {
        $input = $this->post(true);
        if ($input) {
            $input['kdlokasi'] = $this->login['kdlokasi'];
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
            
            $data['rekap'] = $this->laporan_service->getLogSatker($data);
            $data['libur'] = $this->laporan_service->getLibur($input);
            $data['satker'] = $this->satker;

            //ambil ttd
            if ($data['tingkat'] == 6 && $data['bulan'] == 1 && $data['tahun'] == 2018)
                $data['tingkat'] = 3;

            $data['laporan'] = $this->laporan_service->getLaporan($input);
            //ambil moderasi
            $data['moderasi'] = $this->laporan_service->getArraymod($input, $data['laporan']);
            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];
            
            $this->subView('tabelpulangold', $data);
        }
    }

    protected function individu() {
        $data['title'] = 'Laporan Individu';
        $data['breadcrumb'] = '<a href="'.$this->link().'" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Laporan Individu</a>';

        $data['satker'] = $this->satker;
        $this->showView('individu', $data, 'theme_admin');
    }

    protected function tabelpersonil() {
        $input = $this->post(true);
        if ($input) {
            $input['kdlokasi'] = $this->login['kdlokasi'];
            foreach ($input as $key => $i)
                $data[$key] = $i;

            $data['dataTabel'] = $this->laporan_service->getTabelPersonil($input);
            $data['laporan'] = $this->laporan_service->getLaporan($data);
            $data['satker'] = $this->satker;
            $this->subView('tabelpersonil', $data);
        }
    }

    protected function tabelrekapc1() {
        $input = $this->post(true);
        if ($input) {
            $input['kdlokasi'] = $this->login['kdlokasi'];
            $input['satker'] = $this->satker;
            foreach ($input as $key => $i)
                $data[$key] = $i;

            $data['pegawai'] = $this->laporan_service->getDataPersonilBatch($input['pin_absen'], true);
            $data['personil'] = '';
            if ($data['pegawai']['count'] > 0) {
                $personil = array_map(function ($i) {
                    return $i['nipbaru'];
                }, $data['pegawai']['value']);

                $data['personil'] = implode(',', $personil);
            }
            $data['tpp_pegawai'] = $this->laporan_service->getTpp($data['personil']);

            $data['format'] = 'A'; $data['jenis'] = '';
            $data['personil'] = $input['pin_absen'];

            //ambil ttd
            if ($data['tingkat'] == 6 && $data['bulan'] == 1 && $data['tahun'] == 2018)
                $data['tingkat'] = 3;

            $data['laporan'] = $this->laporan_service->getLaporan($input);

            $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], true);
            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];

            $this->subView('tabelrekapc1', $data);
        }
    }

    protected function tabelrekapc2() {
        $input = $this->post(true);
        if ($input) {
            $input['kdlokasi'] = $this->login['kdlokasi'];
            $input['satker'] = $this->satker;
            foreach ($input as $key => $i)
                $data[$key] = $i;

            $data['pegawai'] = $this->laporan_service->getDataPersonilBatch($input['pin_absen'], true);

            $data['format'] = 'B'; $data['jenis'] = '';
            $data['personil'] = $input['pin_absen'];

            //ambil ttd
            if ($data['tingkat'] == 6 && $data['bulan'] == 1 && $data['tahun'] == 2018)
                $data['tingkat'] = 3;

            $data['laporan'] = $this->laporan_service->getLaporan($input);
            
            $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan']);
            $data['kode'] = $this->laporan_service->getData("SELECT * FROM tb_kode_presensi ORDER BY kode_presensi ASC", [])['value'];

            $this->subView('tabelrekapc2', $data);
        }
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
            $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], true);
            $data['bendahara'] = $this->laporan_service->getBendahara($input['kdlokasi']);
            $data['kepala'] = $this->laporan_service->getKepala($input['kdlokasi']);

            $this->subView('tabeltpp', $data);
        }
    }

    protected function tpp13() {
        $data['title'] = 'Penerimaan TPP Ke-13';
        $data['breadcrumb'] = '<a href="'.$this->link().'" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Penerimaan TPP Ke-13</a>';

        $data['satker'] = $this->satker;
        $data['bendahara'] = $this->laporan_service->getBendahara($this->login['kdlokasi']);
        $this->showView('tpp13', $data, 'theme_admin');
    }

    protected function tabeltpp13() {
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

            if ($data['tahun'] == 2018)
                $data['bulan'] = 5; //tpp13 thn 2018 bln mei
            $data['tingkat'] = 6;

            $data['pajak'] = $this->laporan_service->getArraypajak();
            $data['laporan'] = $this->laporan_service->getLaporan($data);
            $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], true);
            $data['bendahara'] = $this->laporan_service->getBendahara($input['kdlokasi']);
            $data['kepala'] = $this->laporan_service->getKepala($input['kdlokasi']);

            $this->subView('tabeltpp13', $data);
        }
    }

    protected function tpp14() {
        $data['title'] = 'Penerimaan TPP Ke-14';
        $data['breadcrumb'] = '<a href="'.$this->link().'" class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Index</a><a class="breadcrumb white-text" style="font-size: 13px;">'
                . 'Penerimaan TPP Ke-14</a>';

        $data['satker'] = $this->satker;
        $data['bendahara'] = $this->laporan_service->getBendahara($this->login['kdlokasi']);
        $this->showView('tpp14', $data, 'theme_admin');
    }

    protected function tabeltpp14() {
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

            if ($data['tahun'] == 2018)
                $data['bulan'] = 6; //tpp14 thn 2018 bln juni
            $data['tingkat'] = 6;

            $data['pajak'] = $this->laporan_service->getArraypajak();
            $data['laporan'] = $this->laporan_service->getLaporan($data);
            $data['rekap'] = $this->laporan_service->getRekapAll($data, $data['laporan'], true);
            $data['bendahara'] = $this->laporan_service->getBendahara($input['kdlokasi']);
            $data['kepala'] = $this->laporan_service->getKepala($input['kdlokasi']);

            $this->subView('tabeltpp14', $data);
        }
    }
    protected function checkmod() {
        $input = $this->post(true);

        if ($input) {
            $input['satker'] = $this->satker;
            $input['kdlokasi'] = $this->login['kdlokasi'];
            foreach ($input as $key => $i)
                $data[$key] = $i;
            
            $nomod = $this->laporan_service->getModerasi($input);
            //$nomod = true;
            if ($nomod['count'] > 0) {
                $data['title'] = 'Daftar Proses Pengajuan Moderasi';
                $data['daftarVerMod'] = $this->laporan_service->getDaftarVerMod($input);
                $data['dateLimit'] = $this->dateLimit;
                $this->subView('daftarmod', $data);
                exit;
            }

            //simpan verifikasi
        }
    }

    public function updateVerifikasi() {
        $input = $this->post(true);

        if ($input) {
            //simpan verifikasi
            $idKeys = [
                'kdlokasi' => $this->login['kdlokasi'],
                //'format' => $input['format'] == 'TPP' ? $input['format'] : $input['format'].$input['jenis'],
                //'format' => $input['format'] == 'C' ? $input['format'].$input['jenis'] : $input['jenis'],
                'bulan' => $input['bulan'],
                'tahun' => $input['tahun']
            ];

            if ($input['format'] == 'C')
                $idKeys['pin_absen'] = $input['pin_absen'];

            $ver = [
                'ver_admin_opd' => $this->login['nipbaru'],
                'dt_ver_admin_opd' => date('Y-m-d H:i:s')
            ];

            //$result = $this->laporan_service->update('tb_laporan', $ver, $idKeys);

            $idKeys = $idKeys+$ver;
            $field = implode(",", array_keys($idKeys));
            $result = $this->laporan_service->save('tb_laporan('.$field.')', $idKeys);

            $error_msg = ($result['error']) ? array('status' => 'success', 'message' => 'Terima kasih, laporan sudah disahkan secara elektronik dan sudah terkirim ke Kepala OPD.') : array('status' => 'error', 'message' => 'Maaf, laporan gagal disahkan');
            header('Content-Type: application/json');
            echo json_encode($error_msg);
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
}