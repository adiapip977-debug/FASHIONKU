function pilihUkuran(button, id, ukuran) {

    let tombol = button.parentElement.querySelectorAll(".ukuran-btn");

    tombol.forEach(function(btn) {
        btn.classList.remove("aktif");
    });

    button.classList.add("aktif");

    document.getElementById("ukuran-" + id).value = ukuran;

    tampilkanPilihan(id);
}


function pilihWarna(button, id, warna) {

    let tombol = button.parentElement.querySelectorAll(".warna-btn");

    tombol.forEach(function(btn) {
        btn.classList.remove("aktif");
    });

    button.classList.add("aktif");

    document.getElementById("warna-" + id).value = warna;

    tampilkanPilihan(id);
}


function tampilkanPilihan(id) {

    let ukuran = document.getElementById("ukuran-" + id).value;
    let warna = document.getElementById("warna-" + id).value;

    document.getElementById("pilihan-" + id).innerText =
        "Ukuran: " + (ukuran || "-") +
        " | Warna: " + (warna || "-");
}


function cekPilihan(id) {

    let ukuran = document.getElementById("ukuran-" + id).value;
    let warna = document.getElementById("warna-" + id).value;

    if (ukuran === "" || warna === "") {

        alert("Silakan pilih ukuran dan warna terlebih dahulu!");

        return false;
    }

    return true;
}