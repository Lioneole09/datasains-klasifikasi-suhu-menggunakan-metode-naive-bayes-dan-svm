const hamBurger = document.querySelector(".toggle-btn");
const sidebar = document.querySelector("#sidebar");

// Membuka sidebar secara otomatis saat halaman dibuka
window.addEventListener("load", function () {
  sidebar.classList.add("expand"); // Tambahkan kelas 'expand' saat halaman dimuat
});

hamBurger.addEventListener("click", function () {
  sidebar.classList.toggle("expand"); // Toggle sidebar ketika tombol ditekan
});

$(document).ready(function () {
  // Inisialisasi untuk Tabel 2
  $("#table2").DataTable({
    lengthMenu: [
      [10, 50, 100],
      [10, 50, 100],
    ],
    pageLength: 10,
    language: {
      lengthMenu: "Show _MENU_",
      zeroRecords: "Tidak ada data yang ditemukan",
      infoEmpty: "Tidak ada entri yang tersedia",
      infoFiltered: "(disaring dari _MAX_ entri keseluruhan)",
      search: "Cari:",
      paginate: {
        first: "Pertama",
        last: "Terakhir",
        next: "Selanjutnya",
        previous: "Sebelumnya",
      },
    },
  });
});
