<?php extract($dataLogin);?>
<?php extract($dataPegawai);?>
<?php extract($dataFotoPegawai);?>
<?php extract($dataGrupPengguna);?>
<body class="search-app quick-results-off loaded">
    <?php $this->getView('admin', 'main', 'loading', ''); ?>
    <div class="mn-content fixed-sidebar">
        <?php $this->getView('admin', 'main', 'header', ''); ?>    
        <?php $this->getView('admin', 'main', 'menu', ''); ?>

        <main class="mn-inner">
            <div class="search-header">
                <div class="card card-transparent no-m">
                    <div class="card-content no-s">
                        <div class="z-depth-1 search-tabs">
                            <div class="search-tabs-container">
                                <div class="col s12 m12 l12">
                                    <div class="row search-tabs-row search-tabs-container blue-grey white-text">
                                        <div class="col s12 m6 l6">
                                            <span style="line-height: 48px;text-transform: uppercase;"><?= $title; ?></span>
                                        </div>
                                        <div class="col s12 m6 l6 right-align search-stats">
                                            <span class="secondary-stats"><?= $breadcrumb; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col s12 m4 l4">
                    <div class="card">
                        <div class="card-content center-align">
                            <img src="<?= (($foto_pegawai=='-')) ? 'assets/images/profile.png' : 'http://simpeg.pekalongankota.go.id/'.$foto_pegawai;?>" class="responsive-img circle" width="128px" alt="">
                            <br />
                            <div class="chip m-t-sm blue-grey white-text"><?= (($nama_personil=='-')) ? $username : $nama_personil;?></div>
                        </div>
                    </div>
                </div>
                <div class="col s12 m4 l4">
                    <div class="card">
                        <div class="card-content ">
                            <span class="card-title">Detail Data</span>
                            <p>NIP</p>
                            <span class="card-title"><?= $nipbaru;?></span>
                            
                            <p>NAMA PEGAWAI</p>
                            <span class="card-title"><?= $nama_personil;?></span>
                            
                            <p>PIN ABSEN</p>
                            <span class="card-title"><?= $pin_absen;?></span>
                            
                            <p>LEVEL PENGGUNA</p>
                            <span class="card-title"><?= $nama_grup_pengguna;?></span>
                        </div>
                    </div>
                </div>
                <div class="col s12 m4 l4">
                    <div class="card">
                        <div class="card-content ">
                            <span class="card-title">Ubah Kata Sandi</span>
                            <form id="frmInput" class="form-horizontal m-t-md" role="form" onsubmit="return false" autocomplete="off">
                                <div class="row">
                                    <div class="input-field col s12">
                                        <?= comp\MATERIALIZE::inputText('password_lama', 'password', '', 'required'); ?>
                                        <label for="first_name">Kata Sandi Sekarang</label>
                                    </div>
                                    <div class="input-field col s12">
                                        <?= comp\MATERIALIZE::inputText('password_baru', 'password', '', 'required'); ?>
                                        <label for="email">Kata Sandi Baru</label>
                                    </div>
                                    <div class="input-field col s12">
                                        <?= comp\MATERIALIZE::inputText('password_konfirmasi', 'password', '', 'required'); ?>
                                        <label for="email">Konfirmasi Kata Sandi</label>
                                    </div>
                                    <div class="input-field col s12">
                                        <button id="btnSubmitSimpan" type="submit" class="waves-effect waves-light btn blue">Ubah</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </main>

        <!-- /.modal -->
        <?php $this->getView('admin', 'main', 'footer', ''); ?>
    </div>
    <!-- ./wrapper -->

    <script src="<?= $this->link($this->getProject() . $this->getController() . '/script.php'); ?>"></script>
    <script>
        (function ($) {
            "use strict";
            app.init("<?= $this->link($this->getProject() . $this->getController()); ?>");
            
            $(document).on("submit", "#frmInput", function () {                
                app.simpan(this);
            });
            
        })(jQuery);
    </script>
</body>