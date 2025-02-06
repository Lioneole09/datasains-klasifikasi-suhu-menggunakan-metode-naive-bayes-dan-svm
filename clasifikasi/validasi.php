<?php  
require 'function.php';

$validasi_data = dataValidasi();

$nilaiAsli = [];
$nbHasil = [];
$svmHasil = [];

// Hitung probabilitas hujan
$hujanYa = hujan('Ya') / (hujan('Ya') + hujan('Tidak'));
$hujanTidak = hujan('Tidak') / (hujan('Ya') + hujan('Tidak'));

// Bobot fitur untuk SVM

  // Nilai bobot awal
$weights = calculateWeightsByEntropy($validasi_data);

foreach($validasi_data as $data) {

    $nilaiAsli[] = $data['Hujan'];

    $probYa = (hitung('Langit', $data['Langit'], 'Ya') / hujan('Ya')) *
              (hitung('Suhu', $data['Suhu'], 'Ya') / hujan('Ya')) *
              (hitung('Kelembapan', $data['Kelembapan'], 'Ya') / hujan('Ya')) *
              (hitung('K_Angin', $data['K_Angin'], 'Ya') / hujan('Ya')) *
              (hitung('Curah_Hujan', $data['Curah_Hujan'], 'Ya') / hujan('Ya')) *
              $hujanYa;

    $probTidak = (hitung('Langit', $data['Langit'], 'Tidak') / hujan('Tidak')) *
                 (hitung('Suhu', $data['Suhu'], 'Tidak') / hujan('Tidak')) *
                 (hitung('Kelembapan', $data['Kelembapan'], 'Tidak') / hujan('Tidak')) *
                 (hitung('K_Angin', $data['K_Angin'], 'Tidak') / hujan('Tidak')) *
                 (hitung('Curah_Hujan', $data['Curah_Hujan'], 'Tidak') / hujan('Tidak')) *
                 $hujanTidak;

    $nbHasil[] = $probYa > $probTidak ? 'Ya' : 'Tidak';
    

    // SVM
    $normalizedValues = [
        'Langit' => $data['Langit'] == 'Cerah' ? 1 : ($data['Langit'] == 'Berawan' ? 0 : -1),
        'Suhu' => $data['Suhu'] == 'Tinggi' ? 1 : ($data['Suhu'] == 'Normal' ? 0 : -1),
        'Kelembapan' => $data['Kelembapan'] == 'Tinggi' ? 1 : ($data['Kelembapan'] == 'Normal' ? 0 : -1),
        'K_Angin' => $data['K_Angin'] == 'Kencang' ? 1 : ($data['K_Angin'] == 'Sedang' ? 0 : -1),
        'Curah_Hujan' => $data['Curah_Hujan'] == 'Hujan' ? 1 : ($data['Curah_Hujan'] == 'Gerimis' ? 0 : -1)
    ];

    $svmTotal = 0;
    foreach ($normalizedValues as $feature => $value) {
        $svmTotal += $value * $weights[$feature];
    }

    $svmHasil[] = $svmTotal > 0 ? 'Ya' : 'Tidak';
}

// Hitung metrik evaluasi
$nbMetrics = EvolusiMatrik($nilaiAsli, $nbHasil);
$svmMetrics = EvolusiMatrik($nilaiAsli, $svmHasil);
?>

<?php include './template/header.php'; ?>
<?php include './template/sidebar.php'; ?>

<div class="main">
    <main class="content px-3 py-4">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-6">
                    <h4 class="fw-bold mb-3">Naive Bayes</h4>
                    <?php hasilEvolusiMatrik($nbMetrics); ?>
                </div>
                <div class="col-6">
                    <h4 class="fw-bold mb-3">SVM</h4>
                    <?php hasilEvolusiMatrik($svmMetrics); ?>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <h5 class="fw-bold fs-4">DATA VALIDASI</h5>
                    <table id="table2" class="table table-striped data-table">
                        <thead class="table-primary">
                            <tr class="highlight">
                                <th scope="col">No</th>
                                <th scope="col">Langit</th>
                                <th scope="col">Suhu</th>
                                <th scope="col">Kelembapan</th>
                                <th scope="col">K_Angin</th>
                                <th scope="col">Curah_Hujan</th>
                                <th scope="col">Hujan</th>
                                <th scope="col">Naive Bayes</th>
                                <th scope="col">SVM</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $no = 1;
                        foreach ($validasi_data as $index => $data) : ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $data['Langit'] ?></td>
                                <td><?= $data['Suhu'] ?></td>
                                <td><?= $data['Kelembapan'] ?></td>
                                <td><?= $data['K_Angin'] ?></td>
                                <td><?= $data['Curah_Hujan'] ?></td>
                                <td><?= $data['Hujan'] ?></td>
                                <td><span class="badge <?= $nbHasil[$index] === 'Ya' ? 'bg-success' : 'bg-danger' ?>">
                                    <?= $nbHasil[$index] ?></span></td>
                                <td><span class="badge <?= $svmHasil[$index] === 'Ya' ? 'bg-success' : 'bg-danger' ?>">
                                    <?= $svmHasil[$index] ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include './template/footer.php' ?>
