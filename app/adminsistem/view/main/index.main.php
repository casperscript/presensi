<body class="search-app quick-results-off">
    <?php $this->getView('adminsistem', 'main', 'loading', ''); ?>
    <div class="mn-content fixed-sidebar">
      <?php $this->getView('adminsistem', 'main', 'header', ''); ?>    
      <?php $this->getView('adminsistem', 'main', 'menu', ''); ?>

      <main class="mn-inner" style="padding-top: 10px">
          <div class="row">
              <?= $breadcrumb ?>
              <div class="col s12">
                  <div class="page-title">
                    <?= $title;?>
                    <small><?= $subtitle;?></small>
                  </div>
              </div>

              <div class="col s12">
                  <div class="card stats-card">
                    <div class="card-content">
                    <!-- content -->
                    </div>
                    <div id="sparkline-bar"></div>
                  </div>
              </div>
            </div>
      </main>
      <?php $this->getView('adminsistem', 'main', 'footer', ''); ?>
    </div>
    <!-- ./wrapper -->

    <script src="<?= $this->link($this->getProject() . $this->getController() . '/script.php'); ?>"></script>
</body>
