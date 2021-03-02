<body class="signin-page">
    <div class="mn-content valign-wrapper">
        <main class="mn-inner container">
            <div class="valign">
                
    <div class="row">
        <div class="col s12 m9 l9 offset-l2 offset-m1">
            <div class="card-panel blue">
                <span class="white-text" style="font-size:12px;">Pemberitahuan:<br>Kami mengadakan pembaharuan proses login untuk memudahkan Anda menggunakan aplikasi ini. Sekarang Anda cukup memasukkan username dan password dan sistem akan mendeteksi secara otomatis user grup yang sesuai untuk Anda.<br>Password baru akan efektif berlaku mulai tanggal 23 Februari 2018.<br>Apabila Anda mengalami kesulitan login, harap hubungi kami, terima kasih.<hr><span style="text-align:right; width:100%;">Team Dev. SIM e-presensi@dinkominfo</span>
                </span>
            </div>
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
                                            <?= comp\MATERIALIZE::inputText('username', 'text', $username) ?>
                                            <!--<input id="username" type="text" class="validate">-->
                                            <label for="username" class="red-text">Username</label>
                                        </div>
                                        <div class="input-field col s12 red-text">
                                            <!--<input id="password" type="password" class="validate">-->
                                            <?= comp\MATERIALIZE::inputText('password', 'password', $password, 'class="validate"') ?>
                                            <label for="password" class="red-text">Password</label>
                                        </div>
                                        <div class="col s12 right-align m-t-sm">
                                            <button class="waves-effect waves-light btn red"><i class="material-icons left">send</i> Masuk</button>
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
    <script src="<?= $this->link($this->getProject() . $this->getController() . '/script.php'); ?>"></script>
    <script>
                                        app.init("<?= $this->link($this->getController()) ?>");
                                        (function ($) {
                                            "use strict";
                                            $("#selUserGroup").on("change", function () {
                                                window.location.href=$(this).val();
                                            });

                                            $(document).on("submit", "#frmLogin", function (e) {
                                                app.submitLogin();
                                            });

                                        })(jQuery);
//        $("#username").focus();
    </script>
</body>
