<table class="responsive-table bordered striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Jenis Tunjangan</th>
            <th>Jumlah</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>1</td>
            <td>Tunjangan Lalala</td>
            <td>1000000000</td>
            <td>
                <a class="btn-floating waves-effect waves-light orange btn-small"><i class="material-icons">create</i></a>
                <a class="btn-floating waves-effect waves-light red btn-small"><i class="material-icons">delete</i></a>
            </td>
        </tr>
    </tbody>
</table>
<!--pagging($aktif, $batas, $jml_data)-->
<?= comp\MATERIALIZE::pagging(1, 5, 10); ?>
