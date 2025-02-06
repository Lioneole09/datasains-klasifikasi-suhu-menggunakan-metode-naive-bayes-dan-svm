<?php

require 'function.php';

$c = ["Langit", "Suhu", "Kelembapan", "K_Angin", "Curah_Hujan"];

if (isset($_POST['submit'])) {
  if (tambah($_POST) > 0) {
    header("Location:" . $_SERVER['PHP_SELF']);
  }
}

?>

<?php include './template/header.php'; ?>
<?php include './template/sidebar.php'; ?>
      <div class="main">
        <main class="content px-3 py-4">
          <div class="container-fluid">
            <div class="mb-3">
              <h3 class="fw-bold fs-4 mb-3">NAIVE BAYES METHOD</h3>

              <?php if (!empty(dataTesting())) { ?>
              <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#exampleModal"><i class="bx bx-plus"></i> Input Kasus</button>
              <?php } ?>
              <div class="row">
                <div class="col-12">
                  <div class="mb-3"></div>
                  <h5 class="fw-bold fs-4 mb-3">DATA TESTING</h5>
                  <table id="table2" class="table table-striped data-table">
                    <thead class="table-primary">
                      <tr>
                        <th scope="col">No</th>
                        <th scope="col">Langit</th>
                        <th scope="col">Suhu</th>
                        <th scope="col">Kelembapan</th>
                        <th scope="col">K_Angin</th>
                        <th scope="col">Curah_Hujan</th>
                        <th scope="col">Hujan</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $no = 1;
                      foreach (dataTesting() as $tes) : ?>
                        <tr>
                          <td><?= $no++; ?></td>
                          <td><?= $tes['Langit'] ?></td>
                          <td><?= $tes['Suhu'] ?></td>
                          <td><?= $tes['Kelembapan'] ?></td>
                          <td><?= $tes['K_Angin'] ?></td>
                          <td><?= $tes['Curah_Hujan'] ?></td>
                          <td>?</td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

          <?php
          if (!empty(dataTesting())) : ?>
            <div class="row mb-3">
              <div class="col-12">
                <h5 class="fw-bold fs-4 mb-3">PROBABILITAS HUJAN</h5>
                <table class="table table-striped">
                <thead class="table-primary">
                  <tr>
                    <th rowspan="2" class="align-middle">Hujan</th>
                    <th colspan="2" class="text-center">Jumlah Kejadian</th>
                    <th colspan="2" class="text-center">Probabilitas</th>
                  </tr>
                  <tr>
                    <th class="text-center">Ya</th>
                    <th class="text-center">Tidak</th>
                    <th class="text-center">Ya</th>
                    <th class="text-center">Tidak</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>Probabilitas Hujan</td>
                    <td class="text-center"><?= hujan('Ya') ?></td>
                    <td class="text-center"><?= hujan('Tidak') ?></td>
                    <?php
                      $hujanYa = hujan('Ya') / (hujan('Ya') + hujan('Tidak'));
                      $hujanTidak = hujan('Tidak') / (hujan('Ya') + hujan('Tidak'));
                      $hujanYa = number_format($hujanYa, 4, '.', '');
                      $hujanTidak = number_format($hujanTidak, 4, '.', '');
                    ?>
                    <td class="text-center"><?= $hujanYa ?></td>
                    <td class="text-center"><?= $hujanTidak ?></td>
                  </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-12">
                <?php foreach ($c as $kriteria) : ?>
                  <h5 class="fw-bold fs-4 mb-3">PROBABILITAS <?= strtoupper($kriteria)?></h5>
                  <table class="table table-striped">
                  <thead class="table-primary">
                    <tr>
                      <th rowspan="2" class="align-middle"><?= strtoupper($kriteria) ?></th>
                      <th colspan="2" class="text-center">Jumlah Kejadian</th>
                      <th colspan="2" class="text-center">Probabilitas</th>
                    </tr>
                    <tr>
                      <th class="text-center">Ya</th>
                      <th class="text-center">Tidak</th>
                      <th class="text-center">Ya</th>
                      <th class="text-center">Tidak</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php
                  foreach (c($kriteria) as $nilai) : ?>
                    <tr>
                      <td><?= $nilai[$kriteria] ?></td>
                      <td class="text-center"><?= hitung($kriteria, $nilai[$kriteria], 'Ya') ?></td>
                      <td class="text-center"><?= hitung($kriteria, $nilai[$kriteria], 'Tidak')?></td>
                      <td class="text-center"><?= hitung($kriteria, $nilai[$kriteria], 'Ya') / hujan('Ya') ?></td>
                      <td class="text-center"><?= hitung($kriteria, $nilai[$kriteria], 'Tidak') / hujan('Tidak')?></td>
                    </tr>
                  <?php endforeach;  ?>
                  </tbody>
                  </table>
                <?php endforeach; ?>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-12">
                <h5 class="fw-bold fs-4 mb-3">PERHITUNGAN DATA KASUS</h5>
                <table class="table table-striped">
                <thead class="table-primary">
                  <tr>
                    <th>No</th>
                    <th>Langit</th>
                    <th>Suhu</th>
                    <th>Kelembapan</th>
                    <th>K_Angin</th>
                    <th>Curah_Hujan</th>
                    <th>Prob_Ya</th>
                    <th>Prob_Tidak</th>
                    <th>Hasil</th>
                    <th>Kesimpulan</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                $no = 1;
                foreach (dataTesting() as $tes) :
                  $langit = $tes['Langit'];
                  $suhu = $tes['Suhu'];
                  $kelembapan = $tes['Kelembapan'];
                  $k_angin = $tes['K_Angin'];
                  $curah_hujan = $tes['Curah_Hujan'];

                  $probYa = (hitung('Langit', $langit, 'Ya') / hujan('Ya')) *
                            (hitung('Suhu', $suhu, 'Ya') / hujan('Ya')) *
                            (hitung('Kelembapan', $kelembapan, 'Ya') / hujan('Ya')) *
                            (hitung('K_Angin', $k_angin, 'Ya') / hujan('Ya')) *
                            (hitung('Curah_Hujan', $curah_hujan, 'Ya') / hujan('Ya')) *
                            (hujan('Ya') / (hujan('Ya') + hujan('Tidak')));

                  $probTidak =  (hitung('Langit', $langit, 'Tidak') / hujan('Tidak')) *
                                (hitung('Suhu', $suhu, 'Tidak') / hujan('Tidak')) *
                                (hitung('Kelembapan', $kelembapan, 'Tidak') / hujan('Tidak')) *
                                (hitung('K_Angin', $k_angin, 'Tidak') / hujan('Tidak')) *
                                (hitung('Curah_Hujan', $curah_hujan, 'Tidak') / hujan('Tidak')) *
                                (hujan('Tidak') / (hujan('Ya') + hujan('Tidak')));

                  $kesimpulan = $probYa > $probTidak ? 'Ya' : 'Tidak';
                ?>
                  <tr>
                    <td><?= $no++; ?></td>
                    <td><?= $langit ?></td>
                    <td><?= $suhu ?></td>
                    <td><?= $kelembapan ?></td>
                    <td><?= $k_angin ?></td>
                    <td><?= $curah_hujan ?></td>
                    <td><?= number_format($probYa, 4) ?></td>
                    <td><?= number_format($probTidak, 4) ?></td>
                    <td><strong><?= number_format(max($probYa, $probTidak), 4) ?></strong></td>
                    <td><span class="badge <?= $kesimpulan === 'Ya' ? 'bg-success' : 'bg-danger' ?>">
                      <?= $kesimpulan ?></span></td>
                  </tr>
                <?php endforeach; ?>
                </tbody>
                </table>
              </div>
            </div>
          <?php endif; ?>
          </div>

          <!-- Modal -->
          <?php include './template/modal.php' ?>
        </main>
      </div>
<?php include './template/footer.php' ?>