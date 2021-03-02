<?php header('application/javascript');?>
<!--<script>-->
    app = {
        init: function (url) {
            url_simpan = url + "/simpan";
            url_keluar = url + "/keluar";
        },
        
        simpan: function () {
            $.post(url_simpan, $("#frmInput").serialize(), function(data){
                if(data=="ok"){
                    alert("Kata sandi berhasil diubah. Silahkan masuk dengan kata sandi yang baru.");
                    window.location.href = url_keluar;
                }
                else if(data=="beda"){
                    alert("Maaf, kata sandi baru dan konfirmasi tidak sama.");
                }
                else{
                    alert("Maaf, kata sandi lama tidak cocok.");
                }
            }, "json");
            //console.clear();
        },
        
    };
<!--</script>-->