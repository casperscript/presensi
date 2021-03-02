<body>
    <?php $this->getView('adminsistem', 'main', 'loading', ''); ?>
    <div class="mn-content fixed-sidebar">
      <?php $this->getView('adminsistem', 'main', 'header', ''); ?>    
      <?php $this->getView('adminsistem', 'main', 'menu', ''); ?>

      <main class="mn-inner">
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
                        <div class="card-options">
                           <button class="btn-floating waves-effect waves-light blue btnForm modal-trigger" data-target="frmInputModal"><i class="material-icons">add</i></button>
                        </div>
                        <span class="card-title"><?= $table_title;?></span>
                        <form id="frmData" class="navbar-search expanded" onsubmit="return false" role="search" >
                            <div class="input-field col s3 custom-prefix" style="float:right">
                                <i class="material-icons prefix">search</i>
                                <input name="cari" id="cari" class="validate" type="text" placeholder="Pencarian Data">
                              </div>
                              <?= comp\MATERIALIZE::inputKey('page', '1'); ?>
                        </form>
                        <div id="data-tabel"></div>
                    </div>
                    </div>
                    <div id="sparkline-bar"></div>
                  </div>
              </div>
            </div>
      </main>

    <div id="frmInputModal" class="modal">
        <form id="frmInput" class="form-horizontal" role="form" onsubmit="return false" autocomplete="off">
            <div class="modal-content">
                <div id="data-form-input"></div>
            </div>
            <div class="modal-footer right-align">
                <button type="submit" class="modal-close waves-effect waves-light btn green">Simpan</button>
                <button type="button" class="modal-close waves-effect waves-light btn red">Batal</button>
            </div>
        </form>
    </div>
    <!-- /.modal -->
      <?php $this->getView('adminsistem', 'main', 'footer', ''); ?>
    </div>
    <!-- ./wrapper -->

    <script src="<?= $this->link($this->getProject() . $this->getController() . '/script.php'); ?>"></script>
    <script>
        (function ($) {
            "use strict";
            app.init("<?= $this->link($this->getProject() . $this->getController()); ?>");
            app.loadTabel();
            $(document).on("submit", "#frmData", function () {
                $("#page").val(1);
                app.loadTabel();
            });
            
            $(document).on("click", ".btnForm", function () {
                app.showForm(this.id);
            });
        
        })(jQuery);
    </script>
</body>