<body class="search-app quick-results-off">
    <?php $this->getView('anggaran', 'main', 'loading', ''); ?>
    <div class="mn-content fixed-sidebar">
      <?php $this->getView('anggaran', 'main', 'header', ''); ?>    
      <?php $this->getView('anggaran', 'main', 'menu', ''); ?>

      <main class="mn-inner" style="padding-top: 10px">
          <div class="row">
              <?= $breadcrumb;?>
              <div class="col s12">
                  <div class="page-title">
                    <?= $title;?>
                    <small><?= $subtitle;?></small>
                  </div>
              </div>

              <div class="col s12">
                  <div class="card stats-card">
                    <div class="card-content">
                    <?php foreach ($teks['tempel'] as $kd_teks => $t) { ?>
                    <div class="card-panel <?= $t['bg_color'] ?>">
                        <span style="color: #3d3d3d"><?= $t['isi_teks'] ?></span>
                    </div>
                    <?php } ?>
                    </div>
                    <div id="sparkline-bar"></div>
                  </div>
              </div>
          </div>
      </main>
      <?php $this->getView('anggaran', 'main', 'footer', ''); ?>
    </div>
    <!-- ./wrapper -->
    <input type="hidden" id="text_popup" value="<?= count($teks['popup']) ?>">
    <?php 
    $no_popup = 1;
    foreach ($teks['popup'] as $kd_teks => $t) { ?>
        <div id="modal<?= $no_popup ?>" class="modal" style="font-weight: 400">
            <div class="modal-content <?= $t['bg_color'] ?> black-text">
                <?= $t['isi_teks'] ?>
            </div>
            <div class="modal-footer <?= $t['bg_color'] ?>">
                <button class="modal-action modal-close waves-effect waves-grey btn black">Tutup</button>
            </div>
        </div>
    <?php
        $no_popup++; 
    } ?>
    <script src="<?= $this->link($this->getProject() . $this->getController() . '/script.php'); ?>"></script>
    <script>
        (function ($) {
            "use strict";
            var popup = $('#text_popup').val();
            for (var i = 1; i <= popup; i++) {
                $('#modal' + i).openModal();
            }
        })(jQuery);
    </script>
</body>
