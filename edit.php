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


class DataBump extends DataSource {
    // Load student by no_siswa
    public function loadStudentByNoSiswa($no_siswa) {
        $data = $this->source();
        foreach ($data as $block) {
            if ($block['status'] == 200 && isset($block['data'])) {
                // Search for the student with the matching no_siswa
                foreach ($block['data'] as $student) {
                    if (isset($student['no_siswa']) && $student['no_siswa'] == $no_siswa) {
                        return $student;
                    }
                }
            }
        }
        return null;
    }

    // Update student by no_siswa
    public function updateStudentByNoSiswa($no_siswa, $updatedData) {
        $data = $this->source();
        foreach ($data as &$block) {
            if ($block['status'] == 200 && isset($block['data'])) {
                // Search for the student with the matching no_siswa
                foreach ($block['data'] as &$student) {
                    if (isset($student['no_siswa']) && $student['no_siswa'] == $no_siswa) {
                        // Update the student's data
                        $student = $updatedData;
                        $this->saveData($data);
                        return true; // Return true if update is successful
                    }
                }
            }
        }
        return false; // Return false if no student with the given no_siswa was found
    }
}

$obj = new DataBump();

// Handle form submission
if (isset($_POST['update'])) {
    $no_siswa = $_POST['no_siswa']; // Use no_siswa instead of index
    $updatedData = [
        'nama' => $_POST['nama'],
        'kelas' => $_POST['kelas'],
        'no_siswa' => $_POST['no_siswa'],
        'gender' => $_POST['gender'],
        'tanggal_lahir' => $_POST['tanggal_lahir'],
        'status' => $_POST['status'],
        'keterangan' => $_POST['keterangan'],
        'tanggal_hadir' => $_POST['tanggal_hadir']
    ];
    
    if ($obj->updateStudentByNoSiswa($no_siswa, $updatedData)) {
        header("Location: index.php"); // Redirect to main page after update
        exit;
    }
}

if (isset($_POST['back'])) {
    header("Location: index.php"); // Redirect to main page
    exit;
}

$no_siswa = $_GET['no_siswa']; 
$student = $obj->loadStudentByNoSiswa($no_siswa);


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../js/bootstrap.js">
    <link rel="stylesheet" href="../js/popper.min.js">
</head>
<body class="d-flex flex-column align-items-center justify-content-center vh-100 bg-warning">
    <?php if ($student): ?>
        <label for="" class="h3 text-light">Form Edit Kehadiran</label>
        <form action="" method="post" class="col-5 py-3 px-5 border-1 border border-dark rounded-2 bg-secondary">
            <input type="hidden" name="no_siswa" value="<?= htmlspecialchars($student['no_siswa']) ?>"> <!-- Use no_siswa -->
            <div class="mb-3">
                <label for="nama" class="form-label">Nama:</label>
                <input type="text" name="nama" value="<?= htmlspecialchars($student['nama']) ?>" class="form-control">
            </div>
            <div class="mb-3">
                <label for="kelas" class="form-label">Kelas:</label>
                <input type="text" name="kelas" value="<?= htmlspecialchars($student['kelas']) ?>" class="form-control">
            </div>
            <div class="mb-3">
                <label for="no_siswa" class="form-label">No siswa:</label>
                <input type="text" name="no_siswa" value="<?= htmlspecialchars($student['no_siswa']) ?>" class="form-control" readonly>
            </div>
            <div class="mb-3">
            <label for="gender" class="form-label">Gender:</label>
            <select name="gender" id="gender" class="form-select" value="<?= htmlspecialchars($student['gender'])?>">
                <option value="L">Laki -Laki</option>
                <option value="P">Perempuan</option>
            </select>
            </div>
            
            <div class="mb-3">
                <label for="tanggal_lahir" class="form-label">Tanggal Lahir:</label>
                <input type="date" name="tanggal_lahir" class="form-control" id="tanggal_lahir" value="<?= htmlspecialchars($student['tanggal_lahir'])?>">
            </div>
            
            <div class="mb-3">
                <label for="status" class="form-label">Status:</label>
                <select name="status" id="status" class="form-select" onchange="ganti()" value="<?= htmlspecialchars($student['status'])?>">
                    <option value="H">Hadir</option>
                    <option value="S">Sakit</option>
                    <option value="I">Izin</option>
                    <option value="A">Alpha</option>
                </select>
            </div>
            
            <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan:</label>
                <input type="text" name="keterangan" class="form-control" id="keterangan" value="<?= htmlspecialchars($student['keterangan'])?>">
            </div>
            <div class="mb-3">
                <label for="tanggal_lahir" class="form-label">Tanggal Hadir:</label>
                <input type="date" name="tanggal_hadir" class="form-control" id="tanggal_hadir" value="<?= htmlspecialchars($student['tanggal_hadir'])?>">
            </div>
            <button type="submit" name="back" class="btn btn-danger">Back</button>
            <button type="submit" name="update" class="btn btn-primary">Update</button>
        </form>
    <?php else: ?>
        <p>Student data not found.</p>
    <?php endif; ?>
    <script src="js/main.js"></script>

</body>
</html>
