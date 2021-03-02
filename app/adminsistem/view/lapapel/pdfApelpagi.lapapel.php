<?php
ob_start();
?>

<style>
    .header {
        /*font-weight: bold;*/
        margin: auto;
        padding: 0px;
        text-align: center;
    }
    .header h1, .header h2, .header h3, .header h4 {
        margin: 0px;
    }
    th {
        background-color: #00acc1;
        text-align: center;
        font-weight: bold;
    }
    td {
        margin: 5px 10px;
    }
    .nama {
        font-size: 10px;
        font-weight: bold;
    }
    .tabellaporan {
        font-size: 9px;
        border-collapse: collapse;
        border: 1px solid black;
        vertical-align: middle;
    }
    .tabellaporan tr th{ 
        border-right: 1px solid black;
        border-bottom: 1px solid black;
        padding: 2px;
        vertical-align: middle;
    }
    .tabellaporan tr {
        vertical-align: middle;
    }
    .tabellaporan tr td{
        border: 0.5px solid black;
    }

    .even{ background-color: beige; height: 0px; }
    .odd{ background-color: #acdffe; height: 0px; }
    .libur{ background-color:rgb(101, 121, 212); }
    .center { text-align: center; }
</style>

<div class="header">
    <h5 style="margin: 0px">PEMERINTAH KOTA PEKALONGAN</h5>
    <h4 style="margin: 0px"><?= $dataSatker['nmlokasi'] ?></h4>
    <small><?= $dataSatker['almt_kantor'] ?>, Telp. <?= $dataSatker['no_telp_kantor'] ?> email: <?= $dataSatker['email_kantor'] ?></small>
    <hr>
    <h5 style="margin: 0px">LAPORAN APEL PAGI PEGAWAI</h5>
    Periode <?= comp\FUNC::tanggal($sdate, 'long_date') ?> s/d <?= comp\FUNC::tanggal($edate, 'long_date') ?> <br />
</div><br />

<?php
foreach ($dataUser as $val_user) {
    $sumTelatIn = 0;
    $sumTelatOut = 0;
    ?>
    <table border="0" class="nama" width="100%">
        <tr>
            <td width="70px">Pin Absen</td>
            <td width="210px">: <?php echo $val_user['pin_absen'] ?></td>
            <td width="40px">Jabatan</td>
            <td width="220px">: </td>
        </tr><tr>
            <td>NIP / NIK</td>
            <td>: <?php echo $val_user['nipbaru'] ?></td>
            <td>Bidang</td>
            <td>: <?php // echo $val_user->namadiv             ?></td>
        </tr><tr>
            <td>Nama Pegawai</td>
            <td>: <?php echo $val_user['nama'] ?></td>
        </tr>
    </table>

    <table class="tabellaporan" width="100%" border="0">
        <tr>
            <th width="90px">Tanggal</th>
            <th width="50px">Hari</th>
            <th width="60px">Finger Apel</th>
            <th>Keterangan</th>
        </tr>
        <?php
        for ($a = strtotime($sdate); $a <= strtotime($edate);) {
            $tgl = date('Y-m-d', $a);
            $tanggal = comp\FUNC::tanggal(date('Y-m-d', $a), 'long_date');
            $jam = (isset($dataApel[$val_user['pin_absen']][$tgl]['jam_apel'])) ? $dataApel[$val_user['pin_absen']][$tgl]['jam_apel'] : '-';

            if ($jam == '-')
                $odd_class = "class='odd'";
            else
                $odd_class = '';
            ?>
            <tr <?= $odd_class ?>>
                <td><?= $tanggal ?></td>
                <td><?= comp\FUNC::$namahari[date('D', $a)] ?></td>
                <td><?= $jam ?></td>
                <td><?= 'keterangan' ?></td>
            </tr>
            <?php
            $a = $a + 86400;
        }
        ?>
    </table>

    <table width="680px" nobr='true'>
        <tr>
            <td rowspan="5">&nbsp;</td>
            <td width="250px"><p>Pekalongan, <?= comp\FUNC::tanggal(date('Y-m-d'), 'long_date') ?></p></td>
        </tr><tr>
            <td><p><?= $dataSatker['kepalaskpd'] ?></p></td>
        </tr><tr>
            <td><br><br><br></td>
        </tr><tr>
            <td>
                <p><?= '' ?></p>
            </td>
        </tr><tr>
            <td>NIP. <?= '' ?></td>
        </tr>
    </table>
    <?php
}
//comp\FUNC::showPre($data);
//comp\FUNC::showPre($_SESSION);
require_once ('comp/mPDF610/vendor/autoload.php');
$html = ob_get_contents();
ob_end_clean();
$pdf = new mPDF('UTF8', 'A4-P');
$pdf->SetDisplayMode('fullpage');
$pdf->setFooter('[' . date('Y-m-d H:i:s') . ']' . $dataSatker['singkatan_lokasi'] . '|ID: ' . $filename_f_id . '|Hal. {PAGENO}');
$pdf->WriteHTML(utf8_encode($html));

$pdf->Output($filename_f, 'F');
//$pdf->Output($filename_d, 'D');
?>
