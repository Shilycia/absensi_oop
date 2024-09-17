<?php

    class DataSource {
        public function source() {
            $getdata = file_get_contents('data/data.json');
            $data = json_decode($getdata, true);
            return $data;
        }

        public function saveData($data) {
            file_put_contents('data/data.json', json_encode($data, JSON_PRETTY_PRINT));
        }
    }

    class Add extends DataSource {

        private $dataSiswa;

        public function loadData() {
            $data = $this->source();
            if ($data) {
                foreach ($data as $value) {
                    if ($value['status'] == 200 && $value['message'] == "Success") {
                        $this->dataSiswa = $value['data'];
                        return true;
                    }
                }
            }
            return false;
        }

        public function tambahSiswa() {
            // Retrieve form inputs
            $nama = $_POST['nama'];
            $kelas = $_POST['kelas'];
            $no_siswa = $_POST['no_siswa'];
            $gender = $_POST['gender'];
            $tanggal_lahir = $_POST['tanggal_lahir'];
            $status = $_POST['status'];
            $keterangan = $_POST['keterangan'];

            // Check if any field is empty
            if (empty($nama) || empty($kelas) || empty($no_siswa) || empty($gender) || empty($tanggal_lahir) || empty($status) || empty($keterangan)) {
                echo "<script>alert('Data tidak valid. Semua field harus diisi.')</script>";
                return false;
            }

            // Create a new student entry
            $siswaBaru = [
                'nama' => $nama,
                'kelas' => $kelas,
                'no_siswa' => $no_siswa,
                'gender' => $gender,
                'tanggal_lahir' => $tanggal_lahir,
                'status' => $status,
                'keterangan' => $keterangan
            ];

            // Read data from the JSON file
            $data = $this->source();
            if (!$data) {
                echo "Data tidak ditemukan atau gagal dibaca.";
                return false;
            }

            // Add new student to the relevant data block
            $updated = false;
            foreach ($data as &$item) {
                if ($item['status'] == 200 && $item['message'] == "Success") {
                    $item['data'][] = $siswaBaru;
                    $updated = true;
                    break;
                }
            }

            if (!$updated) {
                echo "Objek dengan status 200 dan pesan 'Success' tidak ditemukan.";
                return false;
            }

            // Save the updated data back to the file
            $this->dataSiswa = $data;
            return $this->simpanData();
        }

        public function showSuccess() {
            return $this->simpanData();
        }

        private function simpanData() {
            if ($this->dataSiswa !== null) {
                $jsonData = json_encode($this->dataSiswa, JSON_PRETTY_PRINT);
                if (file_put_contents('data/data.json', $jsonData)) {
                    return true;
                } else {
                    return false;
                }
            }
            return false;
        }
    }

    $obj = new Add();

    if (isset($_POST['back'])) {
        header("Location: index.php");
        exit;
    }

    if (isset($_POST['update'])) {
        if ($obj->tambahSiswa()) {
            header("Location: index.php");
            exit;
        }
    }

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Add Siswa</title>
        <link rel="stylesheet" href="../css/bootstrap.css">
        <link rel="stylesheet" href="../js/bootstrap.js">
        <link rel="stylesheet" href="../js/popper.min.js">
</head>
<body class="d-flex flex-column align-items-center justify-content-center vh-100 bg-warning">
    <label for="" class="h3 text-light">Form Add Siswa</label>
    <form action="" method="post" class="col-5 py-3 px-5 border-1 border border-dark rounded-2 bg-light">
        <input type="hidden" name="index" value="<?= $index ?>">
        
        <div class="mb-3">
            <label for="nama" class="form-label">Nama:</label>
            <input type="text" name="nama" class="form-control" id="nama">
        </div>
        
        <div class="mb-3">
            <label for="kelas" class="form-label">Kelas:</label>
            <input type="text" name="kelas" class="form-control" id="kelas">
        </div>
        
        <div class="mb-3">
            <label for="no_siswa" class="form-label">No siswa:</label>
            <input type="text" name="no_siswa" class="form-control" id="no_siswa">
        </div>
        
        <div class="mb-3">
            <label for="gender" class="form-label">Gender:</label>
            <select name="gender" id="gender" class="form-select">
                <option value="L">Laki -Laki</option>
                <option value="P">Perempuan</option>
            </select>
        </div>
        
        <div class="mb-3">
            <label for="tanggal_lahir" class="form-label">Tanggal Lahir:</label>
            <input type="date" name="tanggal_lahir" class="form-control" id="tanggal_lahir">
        </div>
        
        <div class="mb-3">
            <label for="status" class="form-label">Status:</label>
            <select name="status" id="status" class="form-select" onchange="ganti()">
                <option value="H">Hadir</option>
                <option value="S">Sakit</option>
                <option value="I">Izin</option>
                <option value="A">Alpha</option>
            </select>
        </div>
        
        <div class="mb-3">
            <label for="keterangan" class="form-label">Keterangan:</label>
            <input type="text" name="keterangan" class="form-control" id="keterangan">
        </div>

        <button type="submit" name="back" class="btn btn-danger me-1">Back</button>
        <button type="submit" name="update" class="btn btn-primary ms-1">Add</button>
    </form>
    <script src="js/main.js"></script>
</body>
</html>
