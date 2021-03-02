<body class="signin-page">
    <div class="loader-bg"></div>
    <div class="loader">
        <div class="preloader-wrapper big active">
            <div class="spinner-layer spinner-blue">
                <div class="circle-clipper left">
                    <div class="circle"></div>
                </div><div class="gap-patch">
                    <div class="circle"></div>
                </div><div class="circle-clipper right">
                    <div class="circle"></div>
                </div>
            </div>
            <div class="spinner-layer spinner-red">
                <div class="circle-clipper left">
                    <div class="circle"></div>
                </div><div class="gap-patch">
                    <div class="circle"></div>
                </div><div class="circle-clipper right">
                    <div class="circle"></div>
                </div>
            </div>
            <div class="spinner-layer spinner-yellow">
                <div class="circle-clipper left">
                    <div class="circle"></div>
                </div><div class="gap-patch">
                    <div class="circle"></div>
                </div><div class="circle-clipper right">
                    <div class="circle"></div>
                </div>
            </div>
            <div class="spinner-layer spinner-green">
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
    <div class="mn-content valign-wrapper">
        <main class="mn-inner container">
            <div class="valign">
                <div class="row">
                    <?php //comp\FUNC::showPre($_SESSION); ?>
                    <div class="col s12 m6 l4 offset-l4 offset-m3">
                        <div class="card white darken-1">
                            <div class="card-content ">
                                <span class="card-title">Login PNS</span>
                                <span>
                                    <select id="selUserGroup">
                                        <option disabled selected>Pilih User Group</option>
                                        <option value="<?= $this->link("pns/login")?>">PNS</option>
                                        <option value="<?= $this->link("adminopd/login")?>">Admin OPD</option>
                                        <option value="<?= $this->link("kepalaopd/login")?>">Kepala OPD</option>
                                        <option value="<?= $this->link("admin/login")?>">Admin Kota</option>
                                        <option value="<?= $this->link("kepalabkppd/login")?>">Kepala BKPPD</option>
                                    </select>
                                </span>
                                <div class="row">
                                    <form id="frmLogin" class="col s12" onsubmit="return false" autocomplete="off">
                                        <div class="input-field col s12">
                                            <?= comp\MATERIALIZE::inputText('username', 'text', $username) ?>
                                            <!--<input id="username" type="text" class="validate">-->
                                            <label for="username">Username</label>
                                        </div>
                                        <div class="input-field col s12">
                                            <!--<input id="password" type="password" class="validate">-->
                                            <?= comp\MATERIALIZE::inputText('password', 'password', $password, 'class="validate"') ?>
                                            <label for="password">Password</label>
                                        </div>
                                        <div class="col s12 right-align m-t-sm">
                                            <button class="waves-effect waves-light btn teal">Masuk</button>
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
        app.init("<?= $this->link($this->getProject() . $this->getController()) ?>");
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
