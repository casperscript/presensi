<body class="search-app quick-results-off">
    <?php $this->getView('pengawas', 'main', 'loading', ''); ?>
    <div class="mn-content fixed-sidebar">
      <?php $this->getView('pengawas', 'main', 'header', ''); ?>    
      <?php $this->getView('pengawas', 'main', 'menu', ''); ?>

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
                      <!-- content -->
                      <?php //comp\FUNC::showPre($session);?>
                      <div class="pengumuman">
                        Sehubungan banyaknya pertanyaan terkait kode <b>NR</b> pada laporan
                        jam Apel, dapat kami informasikan hal-hal sebagai berikut :
                        <ol>
                          <li>Pada aplikasi e-presensi saat ini, jam kerja pegawai dibedakan dalam 2 jenis/ kelompok : </li>
                          <ul class="normal">
                           <li>Pertama, Jam Kerja Reguler , yaitu  Jam Kerja Umum sesuai Perwal Nomor 8 Tahun 2018 dan Surat Sekretaris Daerah Nomor 800/1054 tanggal 14 Mei 2018 (Masuk kerja Jam 07.15 dan 08.00 untuk bulan Ramadhan)</li>
                           <li>Kedua, Jam Kerja Non Reguler (NR), yakni jam kerja khusus diluar Jam Kerja Reguler (Jam Masuk bukan jam 07.15).  Misal jam shif siang petugas jaga kantor.</li>
                          </ul>
                          <li>Kewajiban pelaksanaan apel pagi (termasuk Finger Apel pagi), pada  aplikasi hanya ditujukan untuk pegawai dengan jam kerja Reguler.  Bagi pegawai kelompok Jam kerja Non Reguler, Aplikasi tidak menyediakan pencatatan finger apel pagi (tidak perlu finger apel pagi), dan secara otomatis pada laporan akhir bulan ada diberi keterangan NR pada laporan apelnya (artinya Non Reguler, tidak wajib apel pagi, dan oleh sistem tidak terkena potongan)</li>
                        </ol>
                      </div>
                    </div>
                    <div id="sparkline-bar"></div>
                  </div>
              </div>
            </div>
      </main>
      <?php $this->getView('pengawas', 'main', 'footer', ''); ?>
    </div>
    <!-- ./wrapper -->

    <script src="<?= $this->link($this->getProject() . $this->getController() . '/script.php'); ?>"></script>
</body>
