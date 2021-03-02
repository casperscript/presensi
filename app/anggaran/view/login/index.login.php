<body class="signin-page">
    <div class="mn-content valign-wrapper">
        <main class="mn-inner container">
            <div class="valign">

            <div class="row">
                <div class="col s12 m8 l8 offset-l2 offset-m2">
                    <?php foreach ($teks['tempel'] as $kd_teks => $t) { ?>
                    <div class="card-panel <?= $t['bg_color'] ?>" style="font-weight: 400">
                        <span style="color: #3d3d3d"><?= $t['isi_teks'] ?></span>
                    </div>
                    <?php } ?>
                </div>
            </div>

            <div class="row">
                <div class="col s12 m6 l4 offset-l4 offset-m3">
                    <div class="card white darken-1">
                        <div class="card-content ">
                            <span class="card-title red-text"><i class="material-icons">person_pin</i> Login NEW PRESENSI</span>

                            <div class="row">
                                <form id="frmLogin" class="col s12" onsubmit="return false" autocomplete="off">
                                    <div class="input-field col s12 red-text">
                                        <?= comp\MATERIALIZE::inputText('username', 'text', $username, 'autofocus') ?>
                                        <label for="username" class="red-text">Username</label>
                                    </div>
                                    <div class="input-field col s12 red-text">
                                        <?= comp\MATERIALIZE::inputText('password', 'password', $password, 'class="validate"') ?>
                                        <label for="password" class="red-text">Password</label>
                                    </div>
                                    <div class="col s12 center-align m-t-sm">
                                        <div id="showBtnSubmit">
                                            <button class="waves-effect waves-light btn red"><i class="material-icons left">send</i> Masuk</button>
                                        </div>
                                        <div id="showLoader" style="display: none">
                                            <div class="preloader-wrapper small active">
                                                <div class="spinner-layer spinner-green-only">
                                                    <div class="circle-clipper left">
                                                        <div class="circle"></div>
                                                    </div><div class="gap-patch">
                                                        <div class="circle"></div>
                                                    </div><div class="circle-clipper right">
                                                        <div class="circle"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>                                            
                                        <div id="showMessage" style="display: none"></div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</main>
</div>
<input type="hidden" id="text_popup" value="<?= count($teks['popup']) ?>">
<?php 
$no_popup = 1;
foreach ($teks['popup'] as $kd_teks => $t) { ?>
    <div id="modal<?= $no_popup ?>" class="modal" style="font-weight: 400">
        <div class="modal-content <?= $t['bg_color'] ?> black-text">
            <?= $t['isi_teks'] ?>
        </div>
        <div class="modal-footer <?= $t['bg_color'] ?>">
            <button class="modal-action modal-close waves-effect waves-grey btn black">Tutup</button>
        </div>
    </div>
<?php
    $no_popup++; 
} ?>
<script src="<?= $this->link($this->getProject() . $this->getController() . '/script.php'); ?>"></script>
<script>
    app.init("<?= $this->link($this->getController()) ?>");
    (function ($) {
        "use strict";
        $("#selUserGroup").on("change", function () {
            window.location.href = $(this).val();
        });

        $(document).on("submit", "#frmLogin", function (e) {
            app.submitLogin();
        });

        var popup = $('#text_popup').val();
        for (var i = 1; i <= popup; i++) {
            $('#modal' + i).openModal();
        }
        //$('#modal1').openModal();

    })(jQuery);
</script>
</body>
