<?php 
require 'function.php'; 

// Ambil data training
$training_data = data();

if (isset($_POST['uploadCsv']) && isset($_FILES['csvFile'])) {
  $result = upload_BagiCSV($_FILES['csvFile']);

  session_start();
  $_SESSION['upload_message'] = $result['message'];
  $_SESSION['upload_status'] = $result['status'];

  header("Location: " . $_SERVER['PHP_SELF']);
  exit();
}

?>

<?php include './template/header.php'; ?>
<?php include './template/sidebar.php'; ?>

<div class="main">

  <main class="content px-3 py-4">
    <div class="container-fluid">
      <div class="mb-3">
        <h3 class="fw-bold fs-4 mb-3">Dashboard</h3>
          <div class="row">
            <div class="col-12 col-md-4 ">
              <div class="card border-0">
                <div class="card-body bg-info-subtle py-4 " style="border-radius: 10px;">
                  <h5 class="mb-2 fw-bold">
                    Data Training :
                  </h5>
                  <h4>
                    <?= count(data()) ?>
                  </h4>
                </div>
              </div>
            </div>
            <div class="col-12 col-md-4 ">
              <div class="card  border-0">
                <div class="card-body bg-info-subtle py-4 " style="border-radius: 10px;">
                  <h5 class="mb-2 fw-bold">
                    Data Validasi :
                  </h5>
                  <h4>
                    <?= count(dataValidasi()) ?>
                  </h4>
                </div>
              </div>
            </div>
            <div class="col-12 col-md-4 ">
              <div class="card border-0">
                <div class="card-body bg-info-subtle py-4 " style="border-radius: 10px;">
                  <h5 class="mb-2 fw-bold">
                    Data Testing :
                  </h5>
                  <h4>
                    <?= count(dataTesting()) ?>
                  </h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
  </main>
  <main class="content px-3 py-4">
    <div class="container-fluid">
      <div class="row mb-3">
        <div class="col-12">
          <button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#uploadModal">
            <i class="bx bx-upload"></i> Upload CSV
          </button>
          
          <h5 class="fw-bold fs-4 mb-3">DATA TRAINING</h5>
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
              </tr>
            </thead>
            <tbody>
            <?php
            $no = 1;
            foreach ($training_data as $data) : 
            ?>
              <tr>
                <td><?= $no++?></td>
                <td><?= $data['Langit'] ?></td>
                <td><?= $data['Suhu'] ?></td>
                <td><?= $data['Kelembapan'] ?></td>
                <td><?= $data['K_Angin'] ?></td>
                <td><?= $data['Curah_Hujan'] ?></td>
                <td><?= $data['Hujan'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Modal -->
      <?php include './template/modal.php' ?>
    </main>
  </div>
<?php include './template/footer.php' ?>