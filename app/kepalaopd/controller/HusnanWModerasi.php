<?php

namespace app\kepalaopd\controller;

use app\kepalaopd\model\servicemain;
use app\kepalaopd\model\HusnanWModerasiModel;
use system;
use comp\FUNC;

class HusnanWModerasi extends system\Controller {
    protected $kodeLokasi = null;

    public function __construct() {
        parent::__construct();
        
        $this->servicemaster = new HusnanWModerasiModel();
        $this->servicemain = new servicemain();
        $session = $this->servicemain->cekSession();

        if ($session['status'] === true) {
            $this->setSession('SESSION_LOGIN', $session['data']);
            $this->login = $this->getSession('SESSION_LOGIN');
        } else {
            $this->setSession('SESSION_RELOAD', true);
            $this->redirect($this->link('login'));
        }

        $this->kodeLokasi = $this->login["kdlokasi"];
    }

    protected function index() {
        $this->daftarVerMod();
    }

    public function massLegitPage()
    {
        $posts = $this->post(true);
        $data["checkedMods"] = isset($posts["checkedMods"]) ? $posts["checkedMods"] : [];
        $data["checkedMods"] = implode(',', $data["checkedMods"]);
        $data["flag"] = $posts["flag"];
        $this->subView('mass-legit-page', $data);
    }

    public function massVerifPage()
    {
        $posts = $this->post(true);
        $data["checkedMods"] = isset($posts["checkedMods"]) ? $posts["checkedMods"] : [];
        $data["checkedMods"] = implode(',', $data["checkedMods"]);
        $data["flag"] = $posts["flag"];
        $this->subView('mass-verif-page', $data);
    }

    protected function daftarVerMod() {
        $data['title'] = 'Daftar Proses Verifikasi Moderasi';
        $data['listTahun'] = FUNC::numbSeries('2018', date('Y'));
        //$data["daftarVerMod"] = $this->servicemaster->getDaftarVerMod($this->kodeLokasi);
        //$this->showView('daftar-ver-mod', $data, 'theme_admin');
        $this->showView('index-proses-moderasi', $data, 'theme_admin');
    }

    protected function daftarVerModTabel() {
        $input = $this->post(true);
        if ($input) {
            $data['title'] = 'Daftar Proses Verifikasi Moderasi';
            $input['kdlokasi'] = $this->kodeLokasi;
            /* Daftar moderasi */
            $data["daftarVerMod"] = $this->servicemaster->getDaftarVerMod($input);
            $this->subView('daftar-moderasi', $data);
        }
    }

    protected function daftarVerModHasil() {
        $data['title'] = 'Daftar Hasil Verifikasi Moderasi';
        $data["daftarVerMod"] = $this->servicemaster->getDaftarVerMod($this->kodeLokasi, null, true);
        $this->showView('daftar-ver-mod-hasil', $data, 'theme_admin');
    }

    public function infoModerasi($mid)
    {
        $input['kdlokasi'] = $this->kodeLokasi;
        $data["info"] = $this->servicemaster->getInfoModerasi($input, $mid);
        $data["dok"] = $this->servicemaster->getDokumenModerasi($data["info"]["id"]);
        $this->subView('info-detail', $data);
    }

    public function infoModerasiHasil($mid)
    {
        $data["info"] = $this->servicemaster->getInfoModerasi($this->kodeLokasi, $mid, true);
        $data["dok"] = $this->servicemaster->getDokumenModerasi($data["info"]["id"]);
        $this->subView('info-detail', $data);
    }

    public function updateModerasi()
    {
        $posts = $this->post(true);
        $flags = $this->servicemaster->getFlags($posts["mid"]);

       // if (($posts["flag"] === "0" || $posts["flag"] === "1") && ($flags["flag_operator_kota"] === "2" || $flags["flag_kepala_kota"] === "2")) { --> ori -> edited by daniek
        if (($posts["flag"] === "0" || $posts["flag"] === "1") && (is_null($flags["flag_operator_opd"]) || $flags["flag_operator_kota"] === "2" || $flags["flag_kepala_kota"] === "2")) {
            echo json_encode(["status" => "fail", "reload" => "1"]);
            return;
        }

        if ($this->servicemaster->updateModerasi($posts, $this->kodeLokasi) > 0) {
            echo json_encode(["status" => "success", "date" => $this->servicemaster->getDateModified($posts["mid"])]);
            return;
        }
        echo json_encode(["status" => "fail", "reload" => "0"]);
    }

    public function updateModerasiMassLegit()
    {
        $posts = $this->post(true);
        $mids = explode(',', $posts["mids"]);

        if (count($mids) === 0) {
            echo json_encode(["status" => "fail", "message" => "Tidak ada moderasi yang diproses!", "reload" => "1"]);
            return;
        }

        $validMids = [];

        foreach ($mids as $mid) {
            $flags = $this->servicemaster->getFlags($mid);

            if (($posts["flag"] !== "2" && $posts["flag"] !== "3") || (is_null($flags["flag_operator_kota"]) || is_null($flags["flag_kepala_kota"]))) {
                continue;
            } else {
                $validMids[] = $mid;
            }
        }

        $posts = ["mids" => $validMids, "flag" => $posts["flag"], "catatan" => $posts["catatan"]];
        
        if ($this->servicemaster->updateModerasi($posts, $this->kodeLokasi, true) > 0) {
            echo json_encode(["status" => "success"]);
            return;
        }

        echo json_encode(["status" => "fail", "reload" => "0"]);
        
    }

    public function updateModerasiMassVerif()
    {
        $posts = $this->post(true);
        $mids = explode(',', $posts["mids"]);
        
        if (count($mids) === 0) {
            echo json_encode(["status" => "fail", "message" => "Tidak ada moderasi yang diproses!", "reload" => "1"]);
            return;
        }

        $validMids = [];

        foreach ($mids as $mid) {
            $flags = $this->servicemaster->getFlags($mid);

            //if (($posts["flag"] !== "0" && $posts["flag"] !== "1") || (!is_null($flags["flag_operator_kota"]))) { --> ori -> edited by daniek
            if (($posts["flag"] !== "0" && $posts["flag"] !== "1") || (!is_null($flags["flag_operator_kota"]) || is_null($flags["flag_operator_opd"]))) {
                continue;
            } else {
                $validMids[] = $mid;
            }
        }

        $posts = ["mids" => $validMids, "flag" => $posts["flag"], "catatan" => $posts["catatan"]];
        //FUNC::husnanWVarDump($posts);
        if ($this->servicemaster->updateModerasi($posts, $this->kodeLokasi, true) > 0) {
            echo json_encode(["status" => "success"]);
            return;
        }

        echo json_encode(["status" => "fail", "reload" => "0"]);
        
    }

    public function script() {
        $data['title'] = '<!-- Script -->';
        $this->subView('script', $data);
    }

}
