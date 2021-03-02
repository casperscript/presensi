<?php

namespace app\pns\model;

use system;

class servicemasterpresensi extends system\Model {

    public function __construct() {
        parent::__construct();
        parent::setConnection('db_presensi');
    }
    
    public function getDataPenggunaForm($id = '') {
        set_time_limit(0);
        $data = $this->getData('SELECT * FROM tb_pengguna WHERE (username = ?)', array($id));
        if ($data['count'] > 0) {
            return $data['value'][0];
        } else {
            return $this->getTabel('tb_pengguna');
        }
    }

    public function getTabelPanduan($data) {
        set_time_limit(0);
        $cari = '%' . $data['cari'] . '%';
        $page = $data['page'];

        $q_cari = !empty($cari) ? ' WHERE ((nama_panduan LIKE ?)) ' : '';
        $query = 'SELECT * FROM tb_panduan' . $q_cari . ' AND access_level IN ("pns", "public")';
        $idKey = array();
        array_push($idKey, $cari);

        $batas = 10;
        $posisi = ($page - 1) * $batas;
        $jmlData = $this->getData($query, $idKey);
        $dataArr = $this->getData($query . ' LIMIT ' . $posisi . ',' . $batas, $idKey);
        $result['no'] = $posisi + 1;
        $result['page'] = $page;
        $result['batas'] = $batas;
        $result['jmlData'] = $jmlData['count'];
        $result['dataTabel'] = $dataArr['value'];
        $result['query'] = $dataArr['query'];
        $result['query'] = '';
        return $result;
    }
    
    // START PENGATURAN PROFIL
    public function getProfilGrupPengguna($id) {        
        set_time_limit(0);
        $data = $this->getData('SELECT * FROM tb_grup_pengguna WHERE (kd_grup_pengguna = ?)', array($id));
        return $data['value'][0];
    }
    // END MASTER PENGATURAN PROFIL

    public function getTeksBeranda()
    {
        $dataArr = $this->getData('SELECT * FROM tb_teks WHERE lokasi="BERANDA" AND pns = 1 AND tampil = 1', array());
        $tempel = []; $popup = [];
        foreach ($dataArr['value'] as $i) {
            $bentuk = $i['bentuk'];
            if ($bentuk == 'TEMPEL')
                $tempel[$i['kd_teks']] = [
                    'isi_teks' => $i['isi_teks'],
                    'bg_color' => $i['bg_color']
                ];
            else
                $popup[$i['kd_teks']] = [
                    'isi_teks' => $i['isi_teks'],
                    'bg_color' => $i['bg_color']
                ];            
        }

        $teks = [
            'tempel' => $tempel,
            'popup' => $popup
        ];

        return $teks;
    }
}

?>
