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
              <h3 class="fw-bold fs-4 mb-3">SVM METHOD</h3>

              <?php if (!empty(dataTesting())) { ?>
              <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#exampleModal">
                <i class="bx bx-plus"></i> Input Kasus
              </button>
              <?php } ?>
              
              <div class="row">
                <div class="col-12">
                  <div class="mb-3"></div>
                  <h5 class="fw-bold fs-4 mb-3">DATA TESTING</h5>
                  <table id="table2" class="table table-striped">
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
          if (!empty(dataTesting())) :
              // Training data
              $training_data = data();
              $Hasil = [];
              
              // Transformasi data untuk SVM
              // Nilai bobot awal
              $weights = calculateWeightsByEntropy($training_data);
          ?>
            <!-- Tabel Normalisasi Data -->
            <div class="row mb-3">
              <div class="col-12">
                <h5 class="fw-bold fs-4 mb-3">PERHITUNGAN DATA KASUS</h5>
                <table class="table table-striped">
                  <thead class="table-primary">
                    <tr>
                      <th>No</th>
                      <th>Fitur</th>
                      <th>Nilai Asli</th>
                      <th>Nilai Normalisasi</th>
                      <th>Bobot</th>
                      <th>Hasil Perkalian</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 
                    $no = 1;
                    foreach (dataTesting() as $test) :
                      // Array untuk menyimpan hasil normalisasi
                      $normalisasi = [
                        'Langit' => $test['Langit'] == 'Cerah' ? 1 : ($test['Langit'] == 'Berawan' ? 0 : -1),
                        'Suhu' => $test['Suhu'] == 'Tinggi' ? 1 : ($test['Suhu'] == 'Normal' ? 0 : -1),
                        'Kelembapan' => $test['Kelembapan'] == 'Tinggi' ? 1 : ($test['Kelembapan'] == 'Normal' ? 0 : -1),
                        'K_Angin' => $test['K_Angin'] == 'Kencang' ? 1 : ($test['K_Angin'] == 'Sedang' ? 0 : -1),
                        'Curah_Hujan' => $test['Curah_Hujan'] == 'Hujan' ? 1 : ($test['Curah_Hujan'] == 'Gerimis' ? 0 : -1)
                      ];

                      $hasil_perkalian = [];
                      $total = 0;
                      
                      foreach ($normalisasi as $fitur => $nilai) :
                        $perkalian = $nilai * $weights[$fitur];
                        $hasil_perkalian[$fitur] = $perkalian;
                        $total += $perkalian;
                    ?>
                        <tr>
                          <?php if ($fitur === 'Langit'): ?>
                            <td rowspan="5"><?= $no ?></td>
                          <?php endif; ?>
                          <td><?= $fitur ?></td>
                          <td><?= $test[$fitur] ?></td>
                          <td><?= $nilai ?></td>
                          <td><?= $weights[$fitur] ?></td>
                          <td><?= number_format($perkalian, 4) ?></td>
                        </tr>
                    <?php 
                      endforeach;
                      // Simpan total untuk prediksi
                      $Hasil[$no-1] = $total;
                    ?>
                        <!-- Baris Total -->
                        <tr class="table-secondary">
                          <td colspan="5" class="text-end fw-bold">Total:</td>
                          <td class="fw-bold"><?= number_format($total, 4) ?></td>
                        </tr>
                    <?php 
                    $no++;
                    endforeach; 
                    ?>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Tabel Ringkasan Hasil -->
            <div class="row mb-3">
              <div class="col-12">
                <h5 class="fw-bold fs-4 mb-3">HASIL PERHITUNGAN</h5>
                <table class="table table-striped">
                <thead class="table-primary">
                  <tr>
                    <th>No</th>
                    <th>Langit</th>
                    <th>Suhu</th>
                    <th>Kelembapan</th>
                    <th>K_Angin</th>
                    <th>Curah_Hujan</th>
                    <th>Total</th>
                    <th>Hasil</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                $no = 1;
                foreach (dataTesting() as $index => $test) :
                    $total = $Hasil[$index];
                ?>
                  <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $test['Langit'] ?></td>
                    <td><?= $test['Suhu'] ?></td>
                    <td><?= $test['Kelembapan'] ?></td>
                    <td><?= $test['K_Angin'] ?></td>
                    <td><?= $test['Curah_Hujan'] ?></td>
                    <td><?= number_format($total, 4) ?></td>
                    <td>
                      <span class="badge <?= $total > 0 ? 'bg-success' : 'bg-danger' ?>">
                        <?= $total > 0 ? 'Ya' : 'Tidak' ?>
                      </span>
                    </td>
                  </tr>
                <?php endforeach; ?>
                </tbody>
                </table>
              </div>
            </div>
          <?php endif; ?>

          <!-- Modal -->
          <?php include './template/modal.php' ?>

        </main>
      </div>
<?php include './template/footer.php' ?>