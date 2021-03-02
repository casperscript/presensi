<?php
//var_dump($data["jenisModerasi"]); exit();
?>
<style>
    .white-text, label, .input-field label {
        color: #fff;
        text-shadow: 1px 1px #000;
    }

    .black-text, .chip {
        text-shadow: 1px 1px #fff;
    }

    .disinput {
        left: 0;
        top: 0;
        bottom: 0;
        right: 0;
        position: absolute;
        cursor: no-drop;
    }

    .dropzone {
        min-height: 130px;
    }
</style>
<body>
    <?php $this->getView('adminopd', 'main', 'loading', ''); ?>
    <div class="mn-content fixed-sidebar">
        <?php $this->getView('adminopd', 'main', 'header', ''); ?>    
        <?php $this->getView('adminopd', 'main', 'menu', ''); ?>

        <main class="mn-inner red white-text">
            <div class="row" style="margin-bottom:0px;">
                <div class="col s12 center"><h5><i class="material-icons left">warning</i>FORM PENGAJUAN MODERASI KETIDAKHADIRAN<i class="material-icons right">warning</i></h5><small>FASILITAS INI DIGUNAKAN SEMENTARA WAKTU OLEH ADMIN OPD UNTUK PENGAJUAN MODERASI TERKAIT HAL-HAL TEKNIS TIDAK OPERASIONALNYA MESIN FINGER</small></div>
            </div>
            <div class="row" style="margin-top: 30px;">
                <div class="col s6 offset-s1 m6 offset-m1 l6 offset-l1">
                    <h5 class="chip"><i class="material-icons left" style="padding-top:4px;">person_pin</i>Identitas Diri</h5>
                </div>
                <div class="col s3 m3 l3">
                    <h5 class="chip"><i class="material-icons left" style="padding-top:4px;">info</i> Langkah Pengajuan</h5>
                </div>
                <div class="col s2 m2 l2">
                    <h5 class="chip"><i class="material-icons left" style="padding-top:4px;">keyboard_tab</i> Selanjutnya...</h5>
                </div>
            </div>
            <div class="row"> 
                <div class="col s6 offset-s1 m6 offset-m1 l6 offset-l1">   
                    <table>
                        <tbody>
                            <tr>
                                <td style="width:170px;"><div id="divFotoPemohon"></div>
                                </td>
                                <td>
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td colspan="3">
                                                    <div class="input-field col s12">
                                                        <input type="checkbox" id="chkPilihSemuaPns" />
                                                        <label for="chkPilihSemuaPns">1. Pilih Satu/Semua PNS</label><br><br>
                                                        <select id="selDaftarPns" multiple>
                                                            <option disabled>Pilih satu atau beberapa PNS sekaligus...</option>
                                                        </select>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr class="info-nama-nip">
                                                <td style="width: 20px;">Nama</td>
                                                <td style="width: 10px;">:</td>
                                                <td><span id="spNamaPemohon"></span></td>
                                            </tr>
                                            <tr class="info-nama-nip">
                                                <td>NIP</td>
                                                <td>:</td>
                                                <td><span id="spNipPemohon"></span></td>
                                            </tr>
                                            <tr class="info-list-nama" style="display:none;">
                                                <td colspan="3">
                                                    <div id="divListNama" class="col s12" style="height: 200px; overflow-y: scroll;">

                                                    </div>
                                                </td>
                                            </tr>
                                            <tr class="info-list-semua-nama" style="display:none;">
                                                <td colspan="3">
                                                    <div class="col s12" style="height: 200px; overflow-y: scroll;">
                                                        <ol>
                                                            <?php $listAllPin = []; ?>
                                                            <?php
                                                            foreach ($daftarPns as $pns) {
                                                                echo "<li>" . $pns["nama_lengkap"] . "</li>";
                                                                $listAllPin[] = $pns["pin_absen"];
                                                            }
                                                            ?>
                                                        </ol>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col s3 m3 l3">
                    <p><small>1. Tentukan PNS<br>2. Tentukan tanggal awal dan akhir moderasinya<br>3. Tentukan Kategori &amp; Jenis Moderasinya<br>4. Isikan Keterangan Tambahan (bila diperlukan)<br>5. Upload Dokumen Pendukung (bila diperlukan)<br>6. Tekan tombol PROSES!<br>7. Tugas Anda Selesai. Terima kasih!</small></p>
                </div>
                <div class="col s2 m2 l2">
                    <p><small>Selanjutnya data akan diverifikasi:<br>
                            1. Admin OPD.<br>2. Kepala OPD<br>3. Admin Kota<br>4. Kepala BKPPD</small></p>
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
                        <div class="col s6 input-field">
                            <input type="text" id="txtTanggalAwalModerasi" name="txtTanggalAwalModerasi" class="bw-text datepicker" maxlength="10" value="" cursor="pointer">
                            <label for="txtTanggalAwalModerasi" class="black-text">2. Tanggal Awal Moderasi</label>
                            <div class="tglmoderasi" title="Tentukan PNSnya dulu..."></div>
                        </div>
                        <div class="col s6 input-field">
                            <input type="text" id="txtTanggalAkhirModerasi" name="txtTanggalAkhirModerasi" class="bw-text datepicker-akhir" maxlength="10" value="" cursor="pointer">
                            <label for="txtTanggalAkhirModerasi" class="black-text">3. Tanggal Akhir Moderasi</label>
                            <div class="tglmoderasi" title="Tentukan PNSnya dulu..."></div>
                        </div>
                    </div>
                    <div class="row">
                        <p><span style="padding-left: 10px;"><b>4. Kategori &amp; Jenis Moderasi</b></span></p>
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
                        <label for="txtKeterangan" class="white-text">5. Keterangan Tambahan</label>
                        <div class="statinput disinput" title="Diaktifkan jika tgl awal dan akhir dipilih dengan benar."></div>
                    </div>
                    <div class="input-field col s12">
                        <p><strong>6. Upload Dokumen Pendukung</strong></p>
                        <form id="frmDokumenPendukung" class="dropzone black-text" method="post" action="<?= $this->link($this->getProject() . $this->getController() . '/uploadDokumenModerasi') ?>" cursor="pointer">
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
                <div class="col s5 offset-s4">
                    <a class="waves-effect waves-light btn grey btn-batal"><i class="material-icons left">close</i>Batal</a>
                    <a id="btnProses" class="waves-effect waves-light btn green disabled" style="cursor:not-allowed" title="Sebelum memproses pastikan tgl awal dan akhir moderasi diisi dg benar."><i class="material-icons left">check</i>PROSES!</a>
                </div>
            </div>         
        </main>
        <input type="hidden" id="hidPin" />
        <input type="hidden" id="hidAllPin" value="<?= implode(',', $listAllPin); ?>" />

        <!-- /.modal -->
<?php $this->getView('adminopd', 'main', 'footer', ''); ?>
    </div>
    <!-- ./wrapper -->

    <script src="<?= $this->link('js/dropzone.js'); ?>"></script>
    <script src="<?= $this->link('js/husnanw_moderasi_admin_opd.js'); ?>"></script>
    <script>
        (function ($) {
            husnanw_moderasi_main_admin_opd("<?= $this->link('adminopd/' . $this->getController()); ?>", "<?= $data['dateLimit'] ?>");
        })(jQuery);
    </script>
</body>