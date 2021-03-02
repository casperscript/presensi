<?php header('application/javascript');?>
<!--<script>-->
    app = {
        init: function (url) {
            url_tabel = url + "/tabel";
        },
        
        loadTabel: function () {
            $("#data-tabel").html("");
            $("#detail-tabel").hide();
            $.post(url_tabel, $("#frmData").serialize(), function (data) {
                $("#data-tabel").html(data);
                $("#data-tabel").show();
                $("#frmData").show();
            });
            console.clear();
        },
        
        tabelPagging: function (number) {
            $("#page").val(number);
            this.loadTabel();
            console.clear();
        },
        
    };
<!--</script>-->