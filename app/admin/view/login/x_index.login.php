<body class="signin-page">
    <div class="mn-content valign-wrapper">
        <main class="mn-inner container">
            <div class="valign">
                
    <div class="row">
        <div class="col s12 m8 l8 offset-l2 offset-m2">
            <!--div class="card-panel blue">
                <span class="white-text" style="font-size:12px;">Pemberitahuan:<br>Kami mengadakan pembaharuan proses login untuk memudahkan Anda menggunakan aplikasi ini. Sekarang Anda cukup memasukkan username dan password dan sistem akan mendeteksi secara otomatis user grup yang sesuai untuk Anda.<br>Password baru akan efektif berlaku mulai tanggal 23 Februari 2018.<br>Apabila Anda mengalami kesulitan login, harap hubungi kami, terima kasih.<hr><span style="text-align:right; width:100%;">Team Dev. SIM e-presensi@dinkominfo</span>
                </span>
            </div>
            <div class="card-panel pink accent-3">
                <span class="white-text" style="font-size:14px;"><b>Pemberitahuan:</b><br>Yth Bapak/Ibu...saat ini Admin Sistem sedang melakukan proses maintenance SIM E Presensi modul Moderasi dan akan kami informasikan segera setelah fungsi modul berjalan. Mohon maaf atas ketidaknyamanannya. Terima kasih.<hr><span style="text-align:right; width:100%;">Team Dev. SIM e-presensi@dinkominfo</span>
                </span>
            </div>
            <div class="card-panel green darken-3">
                <span class="white-text" style="font-size:14px;"><b>Pemberitahuan:</b><br>Yth Bapak/Ibu... Modul Moderasi dapat digunakan kembali. Terima kasih.<hr><span style="text-align:right; width:100%;">Team Dev. SIM e-presensi@dinkominfo</span>
                </span>
            </div-->
            <!--div class="card-panel cyan accent-3">
                <span class="" style="font-size:14px;"><b>Pemberitahuan:</b><br>Yth Bapak/Ibu... 
                    Terkait dengan implementasi SIM E-Presensi dengan pertimbangan bahwa:
                    a) bulan Februari adalah bulan pertama Implementasi, dan b) masih banyak ASN yang belum memahami mekanisme melakukan moderasi (memberi keterangan atas kondisi ketidakhadiran, misal DL, Bintek dll). Tanpa moderasi DL, Bintek dll akan terkena potongan TPP), <br> <b>*maka proses moderasi khusus bulan Februari yang SOPnya paling lambat tanggal 3 bulan berikutnya, telah dibuka kembali. <br> Terkait dengan hal tersebut agar admin OPD, Kepala OPD, Admin Kota, dan Ka. BKPPD melakukan verifikasi / pengesahan ulang laporan rekap bulanan e-presensi.</b><br>
                    Terima kasih.<hr><span style="text-align:right; width:100%;">Team Dev. SIM e-presensi@dinkominfo</span>
                </span>
            </div-->
            <div class="card-panel yellow lighten-1">
                <span class="black-text" style="font-size:14px;"><b>Pemberitahuan:</b><br>Yth Bapak/Ibu... 
                    Sesuai dengan surat Sekretaris Daerah Nomor 555/0976 tanggal 13 Maret 2018 ( <a href="../../upload/Operasional Implementasi SIM E Presensi Bulan Februari 2018.pdf" class="red-text" target="_blank"><b>lihat surat</b></a> ) 
                    bahwa Implementasi Aplikasi SIM_Epre....., <br>
                    Sehubungan dengan hal tersebut kami beritahukan bahwa untuk 
                    <span class="red-text"><b>TPP Bulan Februari 2018 masih diterimakan 100%</b></span>. <br>
                    Terima kasih.<hr><span style="text-align:right; width:100%;">Team Dev. SIM e-presensi@dinkominfo</span>
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
