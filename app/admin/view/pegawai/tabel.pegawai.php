<table class="responsive-table bordered striped">
    <thead>
        <tr>
            <th>#</th>
            <th>NIP</th>
            <th>Nama Lengkap</th>
            <th>OPD</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>1</td>
            <td>12345678910</td>
            <td>Lala</td>
            <td>DINKOMINFO</td>
            <td>
                <a class="btn-floating waves-effect waves-light orange btn-small"><i class="material-icons">create</i></a>
                <a class="btn-floating waves-effect waves-light red btn-small"><i class="material-icons">delete</i></a>
            </td>
        </tr>
        <tr>
            <td>2</td>
            <td>12345678911</td>
            <td>Lili</td>
            <td>DPM-PTSP</td>
            <td>
                <a class="btn-floating waves-effect waves-light orange btn-small"><i class="material-icons">create</i></a>
                <a class="btn-floating waves-effect waves-light red btn-small"><i class="material-icons">delete</i></a>
            </td>
        </tr>
        <tr>
            <td>3</td>
            <td>12345678912</td>
            <td>Lele</td>
            <td>DINDUKCAPIL</td>
            <td>
                <a class="btn-floating waves-effect waves-light orange btn-small"><i class="material-icons">create</i></a>
                <a class="btn-floating waves-effect waves-light red btn-small"><i class="material-icons">delete</i></a>
            </td>
        </tr>
    </tbody>
</table>
<!--pagging($aktif, $batas, $jml_data)-->
<?= comp\MATERIALIZE::pagging(1, 5, 10); ?>
