<?php
$conn = mysqli_connect('localhost', 'root', '', 'db_klasifikasi');

function tambah() {
    global $conn;

    $langit = $_POST['Langit'];
    $suhu = $_POST['Suhu'];
    $kelembapan = $_POST['Kelembapan'];
    $k_angin = $_POST['K_Angin'];
    $curah_hujan = $_POST['Curah_Hujan'];

    $query = "INSERT INTO tb_testing VALUES ('', '$langit', '$suhu', '$kelembapan', '$k_angin', '$curah_hujan')";
    mysqli_query($conn, $query);

    return mysqli_affected_rows($conn);
}

function data() {
    global $conn;
    $query = "SELECT * FROM tb_training";
    $result = mysqli_query($conn, $query);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

function dataTesting() {
    global $conn;
    $query = "SELECT * FROM tb_testing";
    $result = mysqli_query($conn, $query);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

function dataValidasi() {
    global $conn;
    $query = "SELECT * FROM tb_validasi";
    $result = mysqli_query($conn, $query);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

function c($c) {
    global $conn;
    $query = "SELECT DISTINCT $c FROM tb_training";
    $result = mysqli_query($conn, $query);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[]= $row;
    }
    return $rows;
}

function hujan($hujan) {
    global $conn;
    $query = "SELECT COUNT(hujan) FROM tb_training WHERE hujan = '$hujan'";
    $res = mysqli_query($conn, $query);
    $result = mysqli_fetch_row($res);
    return $result[0];
}

function hitung($c, $nilai, $hujan) {
    global $conn;
    $query = "SELECT COUNT($c) FROM tb_training WHERE $c = '$nilai' AND hujan ='$hujan'";
    $res = mysqli_query($conn, $query);
    $result = mysqli_fetch_row($res);
    return $result[0];
}

function validasiDataCSV($data) {
    $allowed_values = [
        'Langit' => ['Cerah', 'Berawan', 'Mendung'],
        'Suhu' => ['Tinggi', 'Normal', 'Rendah'],
        'Kelembapan' => ['Tinggi', 'Normal', 'Rendah'],
        'K_Angin' => ['Kencang', 'Sedang', 'Pelan'],
        'Curah_Hujan' => ['Hujan', 'Gerimis', 'Tidak'],
        'Hujan' => ['Ya', 'Tidak']
    ];
    
    if (count($data) !== 6) {
        return false;
    }
    
    if (!in_array($data[0], $allowed_values['Langit']) ||
        !in_array($data[1], $allowed_values['Suhu']) ||
        !in_array($data[2], $allowed_values['Kelembapan']) ||
        !in_array($data[3], $allowed_values['K_Angin']) ||
        !in_array($data[4], $allowed_values['Curah_Hujan']) ||
        !in_array($data[5], $allowed_values['Hujan'])) {
        return false;
    }
    
    return true;
}

function setUploadCSV($file) {
    global $conn;
    
    $response = [
        'status' => false,
        'message' => ''
    ];
    
    if ($file['error'] !== 0) {
        $response['message'] = 'Error: Terjadi kesalahan saat upload file';
        return $response;
    }
    
    $fileName = $file['tmp_name'];
    $fileType = pathinfo($file['name'], PATHINFO_EXTENSION);
    
    if ($fileType !== 'csv') {
        $response['message'] = 'Error: File harus berformat CSV';
        return $response;
    }
    
    if (($handle = fopen($fileName, "r")) !== FALSE) {

        fgetcsv($handle);
        
        mysqli_begin_transaction($conn);
        
        try {
            $successCount = 0;
            $errorCount = 0;
            $row = 2;
            
            while (($data = fgetcsv($handle)) !== FALSE) {
                
                if (validasiDataCSV($data)) {
                    $langit = mysqli_real_escape_string($conn, trim($data[0]));
                    $suhu = mysqli_real_escape_string($conn, trim($data[1]));
                    $kelembapan = mysqli_real_escape_string($conn, trim($data[2]));
                    $k_angin = mysqli_real_escape_string($conn, trim($data[3]));
                    $curah_hujan = mysqli_real_escape_string($conn, trim($data[4]));
                    $hujan = mysqli_real_escape_string($conn, trim($data[5]));
                    
                    $query = "INSERT INTO tb_training (Langit, Suhu, Kelembapan, K_Angin, Curah_Hujan, Hujan) 
                             VALUES ('$langit', '$suhu', '$kelembapan', '$k_angin', '$curah_hujan', '$hujan')";
                    
                    if (mysqli_query($conn, $query)) {
                        $successCount++;
                    } else {
                        $errorCount++;
                    }
                } else {
                    throw new Exception("Data pada baris $row tidak valid");
                }
                $row++;
            }
            
            if ($errorCount === 0 && $successCount > 0) {
                mysqli_commit($conn);
                $response['status'] = true;
                $response['message'] = "Berhasil mengupload $successCount data";
            } else {
                throw new Exception("Tidak ada data valid yang dapat diupload");
            }
            
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $response['message'] = 'Error: ' . $e->getMessage();
        }
        
        fclose($handle);
    } else {
        $response['message'] = 'Error: Gagal membaca file CSV';
    }
    
    return $response;
}

// membagi data
function upload_BagiCSV($file) {
    global $conn;

    $response = [
        'status' => false,
        'message' => ''
    ];

    if ($file['error'] !== 0) {
        $response['message'] = 'Error: Terjadi kesalahan saat upload file';
        return $response;
    }

    $fileName = $file['tmp_name'];
    $fileType = pathinfo($file['name'], PATHINFO_EXTENSION);

    if ($fileType !== 'csv') {
        $response['message'] = 'Error: File harus berformat CSV';
        return $response;
    }

    if (($handle = fopen($fileName, "r")) !== FALSE) {

        fgetcsv($handle);

        $dataRows = [];
        while (($data = fgetcsv($handle)) !== FALSE) {
            if (validasiDataCSV($data)) {
                $dataRows[] = $data;
            }
        }

        fclose($handle);

        if (empty($dataRows)) {
            $response['message'] = 'Error: Tidak ada data valid di file CSV.';
            return $response;
        }

        shuffle($dataRows);

        // bagi data 70% 10% 20%
        $totalData = count($dataRows);
        $trainingData = array_slice($dataRows, 0, floor(0.7 * $totalData));
        $validasiData = array_slice($dataRows, floor(0.7 * $totalData), floor(0.2 * $totalData));
        $testingData = array_slice($dataRows, floor(0.9 * $totalData));

        mysqli_begin_transaction($conn);

        try {

            foreach ($trainingData as $row) {
                $langit = mysqli_real_escape_string($conn, trim($row[0]));
                $suhu = mysqli_real_escape_string($conn, trim($row[1]));
                $kelembapan = mysqli_real_escape_string($conn, trim($row[2]));
                $k_angin = mysqli_real_escape_string($conn, trim($row[3]));
                $curah_hujan = mysqli_real_escape_string($conn, trim($row[4]));
                $hujan = mysqli_real_escape_string($conn, trim($row[5]));

                $query = "INSERT INTO tb_training (langit, Suhu, Kelembapan, K_Angin, Curah_Hujan, Hujan) 
                          VALUES ('$langit', '$suhu', '$kelembapan', '$k_angin', '$curah_hujan', '$hujan')";
                mysqli_query($conn, $query);
            }

            foreach ($validasiData as $row) {
                $langit = mysqli_real_escape_string($conn, trim($row[0]));
                $suhu = mysqli_real_escape_string($conn, trim($row[1]));
                $kelembapan = mysqli_real_escape_string($conn, trim($row[2]));
                $k_angin = mysqli_real_escape_string($conn, trim($row[3]));
                $curah_hujan = mysqli_real_escape_string($conn, trim($row[4]));
                $hujan = mysqli_real_escape_string($conn, trim($row[5]));

                $query = "INSERT INTO tb_validasi (Langit, Suhu, Kelembapan, K_Angin, Curah_Hujan, Hujan) 
                          VALUES ('$langit', '$suhu', '$kelembapan', '$k_angin', '$curah_hujan', '$hujan')";
                mysqli_query($conn, $query);
            }

            foreach ($testingData as $row) {
                $langit = mysqli_real_escape_string($conn, trim($row[0]));
                $suhu = mysqli_real_escape_string($conn, trim($row[1]));
                $kelembapan = mysqli_real_escape_string($conn, trim($row[2]));
                $k_angin = mysqli_real_escape_string($conn, trim($row[3]));
                $curah_hujan = mysqli_real_escape_string($conn, trim($row[4]));

                $query = "INSERT INTO tb_testing (Langit, Suhu, Kelembapan, K_Angin, Curah_Hujan) 
                          VALUES ('$langit', '$suhu', '$kelembapan', '$k_angin', '$curah_hujan')";
                mysqli_query($conn, $query);
            }

            mysqli_commit($conn);
            $response['status'] = true;
            $response['message'] = "Berhasil mengupload data.";

        } catch (Exception $e) {
            mysqli_rollback($conn);
            $response['message'] = 'Error: ' . $e->getMessage();
        }
    } else {
        $response['message'] = 'Error: Gagal membaca file CSV';
    }

    return $response;

}


// function evaluasi matriks
function EvolusiMatrik($nilaiAsli, $nilaiHasil) {

    $YY = 0;
    $TT = 0;
    $TY = 0;
    $YT = 0;
    
    for($i = 0; $i < count($nilaiAsli); $i++) {
        if($nilaiAsli[$i] == 'Ya' && $nilaiHasil[$i] == 'Ya') {
            $YY++;
        } elseif($nilaiAsli[$i] == 'Tidak' && $nilaiHasil[$i] == 'Tidak') {
            $TT++;
        } elseif($nilaiAsli[$i] == 'Tidak' && $nilaiHasil[$i] == 'Ya') {
            $TY++;
        } elseif($nilaiAsli[$i] == 'Ya' && $nilaiHasil[$i] == 'Tidak') {
            $YT++;
        }
    }
    
    $accuracy = ($YY + $TT) / count($nilaiAsli);
    
    $precision = $YY + $TY > 0 ? 
        $YY / ($YY + $TY) : 0;
        
    $recall = $YY + $YT > 0 ? 
        $YY / ($YY + $YT) : 0;
        
    $f1Score = $precision + $recall > 0 ? 
        2 * ($precision * $recall) / ($precision + $recall) : 0;
    
    return [
        'accuracy' => round($accuracy * 100, 2),
        'precision' => round($precision * 100, 2),
        'recall' => round($recall * 100, 2),
        'f1_score' => round($f1Score * 100, 2)
    ];
}

function hasilEvolusiMatrik($metrics) {
    ?>
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Matriks Evaluasi</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead class="table-primary">
                    <tr>
                        <th>Metrik</th>
                        <th>Nilai (%)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Accuracy</td>
                        <td><?= $metrics['accuracy'] ?></td>
                    </tr>
                    <tr>
                        <td>Precision</td>
                        <td><?= $metrics['precision'] ?></td>
                    </tr>
                    <tr>
                        <td>Recall</td>
                        <td><?= $metrics['recall'] ?></td>
                    </tr>
                    <tr>
                        <td>F1-Score</td>
                        <td><?= $metrics['f1_score'] ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}

function calculateWeightsByEntropy($training_data) {
    $total_records = count($training_data);
    $features = ['Langit', 'Suhu', 'Kelembapan', 'K_Angin', 'Curah_Hujan'];
    $weights = [];

    foreach ($features as $feature) {
        // Hitung frekuensi setiap kategori
        $kategori = array_column($training_data, $feature);
        $unique_values = array_unique($kategori);
        
        // Hitung entropi
        $entropy = 0;
        foreach ($unique_values as $value) {
            $count = array_count_values($kategori)[$value];
            $probability = $count / $total_records;
            $entropy -= $probability * log($probability, 2);
        }
        
        // Simpan bobot berdasarkan entropi
        $weights[$feature] = $entropy;
    }

    // Normalisasi bobot
    $total_entropy = array_sum($weights);
    foreach ($weights as &$weight) {
        $weight = $weight / $total_entropy;
    }

    return $weights;
}

?>