<!-- Show Tabel Personal -->
<div class="card-action" style="padding-bottom: 0px">
    <form id="frmData" class="navbar-search expanded" onsubmit="return false" role="search" >
        <div class="row">
            <div class="input-field col s4">
                <?= comp\MATERIALIZE::inputSelect('kd_kelompok_lokasi_kerja', $pil_kel_satker, '') ?>
                <label>Kelompok Lokasi Kerja</label>
            </div>
            <div class="input-field col s7">
                <?= comp\MATERIALIZE::inputSelect('kdlokasi', $pil_satker, '') ?>
                <label>Satuan Kerja</label>
            </div>
            <div class="input-field col s1">
                <button class="btn-floating btn waves-effect waves-light green" title="Kirim" type="submit">
                    <i class="material-icons left">search</i>
                </button>
            </div>
        </div>
    </form>
</div>
<!-- End Tabel -->

<script>
    $(document).ready(function () {
        $("#kd_kelompok_lokasi_kerja").material_select("update");
        $("#kdlokasi").material_select("update");
    });
</script>