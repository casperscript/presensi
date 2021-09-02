<?php header('application/javascript');?>
<!--<script>-->
    app = {
        init: function (url) {
            url_tabelrekap = url + "/tabelrekap";
        },

        loadRekap: function (id) {
            if ($('#format').val() == 'C1')
                $('#jenis').val(1);
            else
                $('#jenis').val(2);

            $("#data-tabel").html("");
            $("#progress").removeAttr('style');

            var url = url_tabelrekap + 'c' + $('#jenis').val();
            $.post(url, $("#frmData").serialize(), function (data) {
                $('#progress').attr('style', 'display: none');
                $('#data-tabel').html(data);
            });
        },
    }