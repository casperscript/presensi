<table class="responsive-table bordered striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Nama</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>1</td>
            <td>Jadwal1</td>
            <td>
                <a class="btn-floating waves-effect waves-light orange btn-small"><i class="material-icons">create</i></a>
                <a class="btn-floating waves-effect waves-light red btn-small"><i class="material-icons">delete</i></a>
            </td>
        </tr>
        <tr>
            <td>2</td>
            <td>Jadwal2</td>
            <td>
                <a class="btn-floating waves-effect waves-light orange btn-small"><i class="material-icons">create</i></a>
                <a class="btn-floating waves-effect waves-light red btn-small"><i class="material-icons">delete</i></a>
            </td>
        </tr>
    </tbody>
</table>
<!--pagging($aktif, $batas, $jml_data)-->
<?= comp\MATERIALIZE::pagging(1, 5, 10); ?>
