<?php
//var_dump($data["jenisModerasi"]); exit();
?>

<style>
.picker {
    line-height: 1.0;
}

.disinput {
    left: 0;
    top: 0;
    bottom: 0;
    right: 0;
    position: absolute;
    cursor: no-drop;
}

.white-text {
    text-shadow: 1px 1px #000;
}

.black-text, .chip {
    text-shadow: 1px 1px #fff;
}

.dropzone {
    min-height: 130px;
}
p, label { font-size: calc(50% + 0.8vw); }
</style>
<body class="search-app quick-results-off loaded white">
    <?php $this->getView('pns', 'main', 'loading', ''); ?>
    <div class="mn-content fixed-sidebar">
        <?php $this->getView('pns', 'main', 'header', ''); ?>    
        <?php $this->getView('pns', 'main', 'menu', ''); ?>

        <main class="mn-inner green white-text">
        <div class="row brown" style="margin-bottom:0px;">
                <div class="col s12"><h5 class="center">★ FORM PENGAJUAN MODERASI KETIDAKHADIRAN ★</h5></div>
        </div>
            <div class="row" style="margin-top: 30px;">
                <div class="col s6 offset-s1 m6 offset-m1 l6 offset-l1">
                    <h5 class="chip"><i class="material-icons left" style="padding-top:4px;">person_pin</i>Identitas Diri</h5>
                </div>
            </div>
            <div class="row"> 
                <div class="col s2 offset-s3">
                    <div id="divFotoPemohon" style="border: dashed white 1px;"></div>
                </div>
                <div class="col s7">
                    <table>
                        <tbody>
                            <tr>
                                <td style="width: 50px;"><p>Nama</p></td>
                                <td>:</td>
                                <td><span id="spNamaPemohon" style="font-size: calc(50% + 0.8vw);"></span></td>
                            </tr>
                            <tr>
                                <td><p>NIP</p></td>
                                <td>:</td>
                                <td><span id="spNipPemohon" style="font-size: calc(50% + 0.8vw);"></span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>  
            </div>

            <div id="divRowNotifMandatoryInput" class="row">
                <div class="col l12 center">
                    <div id="divNotifMandatoryInput" class="chip">Anda wajib memasukan tanggal awal dan akhir dengan benar sebelum memasukkan data yang lain.</div>
                </div>
            </div>

            <div class="row">
                <div class="col s6 m6 l6 orange black-text" style="border: dashed white 1px; height:400px;">
                    <p><b>A. FORM ISIAN YANG WAJIB DIISI</b></p>
                    <div class="row" style="margin-top:48px;">
                    <p><span style="padding-left: 10px;"><b>Tanggal Moderasi</b></span></p>
                        <div class="col s6 input-field">
                            <input type="text" id="txtTanggalAwalModerasi" name="txtTanggalAwalModerasi" class="bw-text datepicker" maxlength="10" value="" cursor="pointer" style="font-size: calc(50% + 0.8vw);">
                            <label for="txtTanggalAwalModerasi" class="black-text">Tgl Awal</label>
                            <div class="tglmoderasi" title="Sedang terjadi pemrosesan input moderasi..."></div>
                        </div>
                        <div class="col s6 input-field">
                            <input type="text" id="txtTanggalAkhirModerasi" name="txtTanggalAkhirModerasi" class="bw-text datepicker-akhir" maxlength="10" value="" cursor="pointer" style="font-size: calc(50% + 0.8vw);">
                            <label for="txtTanggalAkhirModerasi" class="black-text">Tgl Akhir</label>
                            <div class="tglmoderasi" title="Sedang terjadi pemrosesan input moderasi..."></div>
                        </div>
                    </div>
                    <div class="row">
                    <p><span style="padding-left: 10px;"><b>Kategori &amp; Jenis Moderasi</b></span></p>
                        <div class="input-field col s6">                      
                            <select id="selKategoriModerasi" cursor="pointer" multiple>
                                <option value="" disabled>Pilih kategori dahulu...</option>
                                <?php foreach ($data["kategoriModerasi"] as $v): ?>
                                    <?php $kategori = $v["kd_jenis"] === "JNSMOD04" ? "semua" : "individual" ?>
                                    <option value="<?= $v["kd_jenis"] ?>" kategori="<?= $kategori ?>"><?= $v["nama_jenis"] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="statinput disinput" title="Diaktifkan jika tgl awal dan akhir dipilih dengan benar."></div>
                        </div>

                        <div class="input-field col s6">
                            <select id="selJenisModerasi" cursor="pointer">
                                
                            </select>
                            <div class="statinput disinput" title="Diaktifkan jika tgl awal dan akhir dipilih dengan benar."></div>
                        </div>
                    </div>                    
                </div>
                <div class="col s6 m6 l6 blue" style="border: dashed white 1px;height:400px;">
                    <p><b>B. FORM ISIAN YANG DISARANKAN UNTUK DILENGKAPI</b></p>
                    <div class="input-field col s12">
                        <textarea id="txtKeterangan" class="materialize-textarea" cursor="pointer"></textarea>
                        <label for="txtKeterangan" class="white-text" style="font-size: calc(50% + 0.8vw);">Keterangan Tambahan</label>
                        <div class="statinput disinput" title="Diaktifkan jika tgl awal dan akhir dipilih dengan benar."></div>
                    </div>
                    <div class="input-field col s12">
                        <p><strong>5. Upload Dokumen Pendukung</strong></p>
                        <form id="frmDokumenPendukung" class="dropzone black-text" method="post" action="<?= $this->link($this->getProject().$this->getController().'/uploadDokumenModerasi') ?>" cursor="pointer">
                            <div class="fallback">
                                <input type="file" id="fileDokumenPendukung" multiple>
                            </div>
                            <input type="hidden" id="hidIdDokumenPendukung" name="hidIdDokumenPendukung" /> 
                            <input type="hidden" id="hidLids" name="hidLids" />       
                        </form>
                        <div class="statinput disinput" title="Diaktifkan jika tgl awal dan akhir dipilih dengan benar."></div>                   
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col s11 offset-s1 m7 offset-m3">
                    <div class="row">
                        <div class="col s6">
                            <a class="waves-effect waves-light btn grey btn-batal right">Batal</a>
                        </div>
                        <div class="col s6">
                            <a id="btnProses" class="waves-effect waves-light btn red disabled left" style="cursor:not-allowed" title="Sebelum memproses pastikan tgl awal dan akhir moderasi diisi dg benar.">PROSES!</a>
                        </div>   
                    </div>                
                </div>
            </div>         
        </main>
        <input type="hidden" id="hidPin" />

        
        <!-- /.modal -->
        <?php $this->getView('pns', 'main', 'footer', ''); ?>
    </div>
    <!-- ./wrapper -->

    <script src="<?= $this->link('js/dropzone.js'); ?>"></script>
    <script src="<?= $this->link('js/husnanw_moderasi_pns.js'); ?>"></script>
    <script>
            (function ($) {
                husnanw_moderasi_main_pns("<?= $this->link('pns/'.$this->getController()); ?>", "<?= $data['dateLimit'] ?>");             
            })(jQuery);
    </script>
</body>
