        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h4 class="modal-title" id="exampleModalLabel">Input Kasus</h4>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
    
                  <div class="modal-body">
                    <form method="post" action="">
                    <?php
                    $c = [
                      "Langit" => array("Cerah", "Berawan", "Mendung"),
                      "Suhu" => array("Tinggi", "Normal", "Rendah"),
                      "Kelembapan" => array("Tinggi", "Normal", "Rendah"),
                      "K_Angin" => array("Kencang", "Sedang", "Pelan"),
                      "Curah_Hujan" => array("Hujan", "Gerimis", "Tidak")
                    ];

                      foreach ($c as $label => $values) : ?>
                        <div class="row mb-3">
                          <label class="col-sm-2 col-form-label"><?= $label ?></label>
                          <div class="col-sm-10">
                            <select name="<?= $label ?>" class="form-select">
                              <?php foreach ($values as $value) : ?>
                                <option value="<?= $value ?>"><?= $value ?></option>
                              <?php endforeach; ?>
                            </select>
                          </div>
                        </div>
                      <?php endforeach; ?>
                      <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>


          <!-- Upload Modal -->
          <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h4 class="modal-title" id="uploadModalLabel">Upload File</h4>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <form method="post" action="" enctype="multipart/form-data">
                    <div class="mb-3">
                      <label for="csvFile" class="form-label">Pilih File:</label>
                      <input type="file" class="form-control" id="csvFile" name="csvFile" accept=".csv" required>
                      <div class="form-text">File harus berformat CSV dengan kolom: Langit, Suhu, Kelembapan, Kecepatan Angin, Curah Hujan, Hujan</div>
                    </div>
                    <button type="submit" name="uploadCsv" class="btn btn-primary">Upload</button>
                  </form>
                </div>
              </div>
            </div>
          </div>