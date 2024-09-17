let selectionauto = document.getElementById('status');
let keterangan = document.getElementById("keterangan");

window.onload = ganti();

console.log('halo halo')
function ganti() {
    if (selectionauto.value === "H") {

        keterangan.value = "Siswa Hadir";

    } else if (selectionauto.value === "S") {
        
        keterangan.value = "Siswa Sakit";

    } else if (selectionauto.value === "I") {
        
        keterangan.value = "Siswa Izin";

    } else if (selectionauto.value === "A") {
        
        keterangan.value = "Siswa Alpha";

    } else {

        console.log(selectionauto.value)
        alert("Data not found");

    }
}
