<?php
header('application/javascript');
?>
<!--<script>-->
    app = {
        init: function (url) {
            url_tabel = url + "/daftarVerModDesktop";
            url_simpan = url + "/simpan";
        },
        loadTabel: function () {
            $.post(url_tabel, $("#frmData").serialize(), function (data) {
                $("#divTabelVerMod").html(data);
                Material.updateTextFields();
            });
        }
    };