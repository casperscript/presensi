<?php header('application/javascript');?>
<!--<script>-->
    app = {
        init: function (url) {
            url_login_submit = url + "/submit";
        },
        
        // Submit Login
        submitLogin: function () {
            $.post(url_login_submit, $("#frmLogin").serialize(), function (data) {
                if (data.status === 1) {
                    var targetApp = "pns";

                    switch (data.data.grup_pengguna_kd.toUpperCase()) {
                        case "KDGRUP01": targetApp = "adminopd"; break;
                        case "KDGRUP02": targetApp = "kepalaopd"; break;
                        case "KDGRUP03": targetApp = "admin"; break;
                        case "KDGRUP04": targetApp = "kepalabkppd"; break;
                        default: targetApp = "pns"; break;
                    }

                    alert(data.pesan);
                    targetApp = url_login_submit.replace("login/submit", targetApp);
                    window.location.href=targetApp;
                } else {
                    alert(data.pesan);
                }
            } , "json");
        }        
    };
<!--</script>-->
