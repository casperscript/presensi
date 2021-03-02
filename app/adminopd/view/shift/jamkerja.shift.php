<?php
extract($dataShiftDetail);

// if ($login['username'] == 'dinkominfo') {
//         comp\FUNC::showPre($data['dataShiftDetail']);
// }

function makeTimeToClass($swork, $ework, $day, $id) {
    $start = date("G", strtotime($swork));
    $end = date("G", strtotime($ework));

    if ((empty($swork) || $swork <= 0) && empty($ework)) {
        ?>
        <a href="javascript:void(0)" class="addJam" id="<?= $id ?>"><img src="images/icons/fugue/user-business-gray.png" width="16" height="16"><strong><?= $day ?></strong></a>
        <ul id="vjam<?= $id ?>" class="zebras"></ul>
        <?php
    } else {
        ?>
        <a href="javascript:void(0)" class="addJam" id="<?= $id ?>"><img src="images/icons/fugue/user-business-gray.png" width="16" height="16"><strong><?= $day ?></strong></a>
        <ul id="vjam<?= $id ?>">

            <?php
            if ($start > $end) {
                ?>
                <li class="from-<?= $start ?> to-25">
                    <a href="javascript:void(0)" title="<?= makeWorking($swork, $ework) ?>" class="with-tip">
                        <span class="event-blue"><?= makeWorking($swork, $ework) ?></span>
                    </a>
                </li>

                <li class="from-0 to-<?= $end ?>">
                    <a href="javascript:void(0)" title="<?= makeWorking($swork, $ework) ?>" class="with-tip">
                        <span class='event-orange'><?= makeWorking($swork, $ework) ?></span>
                    </a>
                </li>
                <?php
            } else {
                ?>
                <li class="from-<?= $start ?> to-<?= $end ?>">
                    <a href="javascript:void(0)" title="<?= makeWorking($swork, $ework) ?>" class="with-tip">
                        <span class="event-blue"><?= makeWorking($swork, $ework) ?></span>
                    </a>
                </li>
                <?php
            }
            ?>
        </ul>
        <?php
    }
}

function makeWorking($swork, $ework) {
    $start = date("H:i", strtotime($swork));
    $end = date("H:i", strtotime($ework));

    $value = $start . " - " . $end;
    return $value;
}
?>

<div class="card-title grey darken-3 white-text center-align">
    <div class="small direction">Jam Kerja</div>
</div>
<div class="card-content no-s">
    <ul class="planning no-m-t" id="detailJam">
        <li class="planning-header">
            <span><b>Tanggal - hari</b></span>
            <ul>
                <li class="at-0">0</li>
                <li class="at-1">1</li>
                <li class="at-2">2</li>
                <li class="at-3">3</li>
                <li class="at-4">4</li>
                <li class="at-5">5</li>
                <li class="at-6">6</li>
                <li class="at-7">7</li>
                <li class="at-8">8</li>
                <li class="at-9">9</li>
                <li class="at-10">10</li>
                <li class="at-11">11</li>
                <li class="at-12">12</li>
                <li class="at-13">13</li>
                <li class="at-14">14</li>
                <li class="at-15">15</li>
                <li class="at-16">16</li>
                <li class="at-17">17</li>
                <li class="at-18">18</li>
                <li class="at-19">19</li>
                <li class="at-20">20</li>
                <li class="at-21">21</li>
                <li class="at-22">22</li>
                <li class="at-23">23</li>
                <li class="at-24">24</li>
            </ul>
        </li>
        <?php
        if ($jmlData > 0) {
            foreach ($dataTabel as $kol) {
                $sdate = $dataShift['tanggal_mulai_shift'];
                $ondate = date('Y-m-d', strtotime($sdate) + ($kol['startdays'] * 86400));
                $tanggal = comp\FUNC::tanggal($ondate, 'long_date') . ', ' . comp\FUNC::tanggal($ondate, 'day');
                echo '<li>';
                makeTimeToClass($kol['starttime'], $kol['endtime'], $tanggal, $kol['id_shift_detail']);
                echo '</li>';
            }
        } else {
            echo "kosong";
        }
        ?>
    </ul>
</div>
<div class="card-panel no-m-t">
    <div class="right">
        <div class="chip">
            Berlaku : 
            <?= comp\FUNC::tanggal($dataShift['tanggal_mulai_shift'], 'long_date') ?>
            s/d 
            <?= comp\FUNC::tanggal($dataShift['tanggal_akhir_shift'], 'long_date') ?>
        </div>
        <div class="chip">Unit : <?= $dataShift['unit_shift'] ?></div>
        <div class="chip">Unit : <?= $dataShift['siklus_shift'] ?></div>
    </div>
    <div class="left">
        <a id="<?= $dataShift['id_shift'] ?>" class="btn-floating red btnHapusShift"><i class="material-icons">delete</i> Hapus</a>
        <a id="<?= $dataShift['id_shift'] ?>" class="btn-floating green btnEditShift"><i class="material-icons">mode_edit</i></a>
    </div>
    <div class="mailbox-text"></div>
</div>

