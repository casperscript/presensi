<?php
//var_dump($data["jenisModerasi"]); exit();
?>
<body class="search-app quick-results-off loaded">
    <?php $this->getView('admin', 'main', 'loading', ''); ?>
    <div class="mn-content fixed-sidebar">
        <?php $this->getView('admin', 'main', 'header', ''); ?>    
        <?php $this->getView('admin', 'main', 'menu', ''); ?>

        <main class="mn-inner">
        <div class="row" style="margin-bottom:0px;">
                <div class="col s12"><h5 class="center">FORM PENGAJUAN MODERASI ABSENSI</h5></div>
        </div>
        <hr>
            <div class="row">
                <div class="col s6 offset-s1 m6 offset-m1 l6 offset-l1">
                    <h5 class="chip">Admin Kota - Pengajuan Moderasi</h5>
                </div>
                <div class="col s3 m3 l3">
                    <h5 class="chip">Yang perlu Anda lakukan</h5>
                </div>
                <div class="col s2 m2 l2">
                    <h5 class="chip">Selanjutnya...</h5>
                </div>
            </div>
            <div class="row"> 
                <div class="col s6 offset-s1 m6 offset-m1 l6 offset-l1">   
                    <table>
                        <tbody>
                            <tr>
                                <td style="width:170px;"><div id="divFotoPemohon"></div></td>
                                <td>
                                    <table>
                                        <thead>
                                            <tr>
                                                <th colspan="3">1. Tentukan OPD PNS</th>
                                            </tr>
                                            <tr>
                                                <td colspan="3">
                                                    <select id="selDaftarOpd">
                                                    <option disabled selected>----------------</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th colspan="3">2. Pilih PNS yang ingin dimoderasi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="3">
                                                    <select id="selDaftarPns">
                                                    <option disabled selected>----------------</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 20px;">Nama</td>
                                                <td style="width: 10px;">:</td>
                                                <td><span id="spNamaPemohon"></span></td>
                                            </tr>
                                            <tr>
                                                <td>NIP</td>
                                                <td>:</td>
                                                <td><span id="spNipPemohon"></span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col s3 m3 l3">
                    <p><small>1. Tentukan OPD Pemohon<br>2. Tentukan PNS Pemohon<br>3. Tentukan Jenis Moderasinya<br>4. Tentukan tanggal awal dan akhir moderasinya<br>5. Isikan Keterangan Tambahan (bila diperlukan)<br>6. Upload Dokumen Pendukung (bila diperlukan)<br>7. Tekan tombol PROSES!<br>8. Tugas Anda Selesai. Terima kasih!</small></p>
                </div>
                <div class="col s2 m2 l2">
                    <p><small>Selanjutnya data akan diverifikasi Kepala OPD.</small></p>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col s5 m5 offset-m1 l5 offset-l1">
                    <p><strong>3. Jenis Moderasi</strong></p>
                    <?php foreach ($data["jenisModerasi"] as $index => $val): ?>
                        <p>
                            <input name="rbtJenisModerasi" type="radio" id="<?= $val["kd_jenis"] ?>" class="with-gap" value="<?= $val["kd_jenis"] ?>" />
                            <label for="<?= $val["kd_jenis"] ?>"><?= $val["nama_jenis"] ?></label>
                        </p>
                    <?php endforeach; ?>
                </div>
                <div class="col s6 m6 l6">
                    <div class="col s6 input-field">
                        <input type="text" id="txtTanggalAwalModerasi" name="txtTanggalAwalModerasi" class="bw-text datepicker" maxlength="10" value="">
                        <label for="txtTanggalAwalModerasi">3. Tanggal Awal Moderasi</label>
                    </div>
                    <div class="col s6 input-field">
                        <input type="text" id="txtTanggalAkhirModerasi" name="txtTanggalAkhirModerasi" class="bw-text datepicker" maxlength="10" value="">
                        <label for="txtTanggalAkhirModerasi">4. Tanggal Akhir Moderasi</label>
                    </div>
                    <div class="input-field col s12">
                        <textarea id="txtKeterangan" class="materialize-textarea"></textarea>
                        <label for="txtKeterangan">5. Keterangan Tambahan</label>
                    </div>
                    <div class="input-field col s12">
                        <p><strong>6. Upload Dokumen Pendukung</strong></p>
                        <form id="frmDokumenPendukung" class="dropzone" method="post" action="<?= $this->link($this->getProject().$this->getController().'/uploadDokumenModerasi') ?>">
                            <div class="fallback">
                                <input type="file" id="fileDokumenPendukung" multiple>
                            </div>
                            <input type="hidden" id="hidIdDokumenPendukung" name="hidIdDokumenPendukung" />              
                        </form>                    
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col s4 offset-s5">
                    <a class="waves-effect waves-light btn grey btn-batal">Batal</a>
                    <a id="btnProses" class="waves-effect waves-light btn red" disabled>PROSES!</a>
                </div>
            </div>         
        </main>        
        <!-- /.modal -->
        <?php $this->getView('admin', 'main', 'footer', ''); ?>
    </div>
    <!-- ./wrapper -->

    <script src="<?= $this->link('js/dropzone.js'); ?>"></script>
    <script src="<?= $this->link('js/husnanw_moderasi.js'); ?>"></script>
    <script>
            (function ($) {
                husnanw_moderasi_main("<?= $this->link($this->getProject() . $this->getController()); ?>", "<?= $data['dateLimit'] ?>");                
            })(jQuery);
    </script>
</body>