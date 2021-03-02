<?php
header('application/javascript');
?>
<!--<script>-->
    app = {
        init: function (url) {
            url_tabel = url + "/daftarVerModTabel";
        },
        loadTabel: function () {
            $("#progressView").attr("class", "indeterminate");
            $.post(url_tabel, $("#frmData").serialize(), function (data) {
                $("#btnMassLegit").removeClass("disabled").addClass("disabled");
                $("#divTabelVerMod").html(data);
                $("#progressView").attr("class", "determinate");
            });
        }
    };