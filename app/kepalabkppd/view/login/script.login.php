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
                   alert(data.pesan);
                    window.location.reload();

                } else {
                    alert(data.pesan);
                }
            }, "json" );
        }        
    };
<!--</script>-->
