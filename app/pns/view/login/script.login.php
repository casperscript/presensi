<?php header('application/javascript');?>
<!--<script>-->
    app = {
        init: function (url) {
            //url_login_submit = url + "/submitAPI";
            url_login_submit = "http://new-presensi.pekalongankota.go.id/pns/login/submitAPI";
        },
        
        // Submit Login
        submitLogin: function () {
            $.post(url_login_submit, $("#frmLogin").serialize(), function (data) {
                alert("test");
                if (data.status === "success") {
                    alert(data.message);
                    //window.location.reload();
                    window.location.href = data.url;

                } else {
                    alert(data.message);
                }
            }, "json" );
        }        
    };
<!--</script>-->
