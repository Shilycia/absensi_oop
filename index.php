<?php

session_start();
if (!isset($_SESSION['login'])) {
    header("Location: page/Login.php");
    exit;
}

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
    public function loadData() {
        $data = $this->source();
        foreach ($data as $value) {
            if ($value['status'] == 200 && $value['message'] == "Success") {
                return $value['data'];
            }
        }
        return null;
    }

    public function resethari() {
        $data = $this->source(); // Memuat keseluruhan data JSON
        $today = date("Y-m-d"); // Mendapatkan tanggal hari ini
    
        if ($data) {
            foreach ($data as &$item) {
                // Proses hanya item dengan status 200 dan ada 'data'
                if ($item['status'] == 200 && isset($item['data'])) {
                    foreach ($item['data'] as &$row) {
                        // Cek apakah tanggal_hadir tidak sama dengan tanggal hari ini
                        if ($row['tanggal_hadir'] != $today) {
                            // Set 'tanggal_hadir', 'status', dan 'keterangan' menjadi null
                            $row['tanggal_hadir'] = null;
                            $row['status'] = null;
                            $row['keterangan'] = null;
                        }
                    }
                }
            }
            // Simpan data yang sudah diproses tanpa mengubah struktur aslinya
            $this->saveData($data);
        }
    }
    
    
    
    
    

    public function tableDefault() {
        $data = $this->loadData();
    
        if ($data) {
            $hasData = false; // Flag to track if any rows are output
    
            foreach ($data as $index => $row) {
                $nosiswa = ''; // Reset $nosiswa for each row
                $urutan = 1; // Track the column number
                $break = 1;
    
                if (isset($row['status']) || isset($row['keterangan']) || isset($row['tanggal_hadir'])) {
                    continue;
                }
    
                $hasData = true; // Mark that we have data to display
                echo '<tr>';
    
                foreach ($row as $cell) {
                    // Check if the current column is the "No Siswa" column
                    if ($urutan == 3) {
                        $nosiswa = htmlspecialchars($cell); // Store No Siswa
                        echo '<td>' . $nosiswa . '</td>';
                    } else {
                        echo '<td>' . htmlspecialchars($cell) . '</td>';
                    }
    
                    if ($break == 5) {
                        break;
                    }
    
                    $urutan++;
                    $break++;
                }
    
                // Tombol Edit dan Delete
                echo "<td scope='col'>
                        <form action='' method='post' style='display:inline;'>
                            <input type='hidden' name='edit_index' value='$nosiswa'>
                            <button type='submit' name='edit' class='btn btn-warning mx-1'>Edit</button>
                        </form>
                        <form action='' method='post' style='display:inline;'>
                            <input type='hidden' name='delete_index' value='$nosiswa'>
                            <button type='submit' name='delete' class='btn btn-danger mx-1'>Delete</button>
                        </form>
                    </td>";
    
                // Tombol Status Kehadiran
                echo "<td scope='col'>
                        <form action='' method='post' style='display:inline;'>
                            <input type='hidden' name='status_index' value='$nosiswa'>
                            <button type='submit' name='status_hadir' class='btn btn-success mx-1'>Hadir</button>
                            <button type='submit' name='status_sakit' class='btn btn-warning mx-1'>Sakit</button>
                            <button type='submit' name='status_izin' class='btn btn-primary mx-1'>Izin</button>
                            <button type='submit' name='status_alpha' class='btn btn-danger mx-1'>Alpha</button>
                        </form>
                    </td>";
    
                echo '</tr>';
            }
    
            if (!$hasData) {
                // Output this if no data was shown in the loop
                echo '<tr><td colspan="9">No data available</td></tr>';
            }
        } else {
            echo '<tr><td colspan="9">No data available</td></tr>';
        }
    }
    
    

    public function updateStatus($no_siswa, $status) {
        $data = $this->source();
        
        foreach ($data as &$item) {
            if ($item['status'] == 200 && isset($item['data'])) {
                // Search for the student with the matching no_siswa
                foreach ($item['data'] as &$student) {
                    if (isset($student['no_siswa']) && $student['no_siswa'] == $no_siswa) {
                        // Update the student's status if found
                        $student['status'] = $status;
                        $student['tanggal_hadir'] = date("Y-m-d");
                        
                        // Save the updated data back to the file
                        $this->saveData($data);
                        
                        return true; // Return true to indicate successful update
                    }
                }
            }
        }
        
        return false; // Return false if no student with the given no_siswa was found
    }
    

    public function Keterangan($no_siswa, $keterangan) {
        $data = $this->source();
        
        foreach ($data as &$item) {
            if ($item['status'] == 200 && isset($item['data'])) {
                // Search for the student with the matching no_siswa
                foreach ($item['data'] as &$student) {
                    if (isset($student['no_siswa']) && $student['no_siswa'] == $no_siswa) {
                        // Update the student's keterangan if found
                        $student['keterangan'] = $keterangan;
                        
                        // Save the updated data back to the file
                        $this->saveData($data);
                        
                        return true; // Return true to indicate successful update
                    }
                }
            }
        }
        
        return false; // Return false if no student with the given no_siswa was found
    }
    

    public function deleteDataByNoSiswa($no_siswa) {
        $data = $this->source();
        
        foreach ($data as &$item) {
            if ($item['status'] == 200 && isset($item['data'])) {
                foreach ($item['data'] as $key => $student) {
                    if (isset($student['no_siswa']) && $student['no_siswa'] == $no_siswa) {
                        unset($item['data'][$key]);
                        $item['data'] = array_values($item['data']); // Reindexing
                        $this->saveData($data); // Save the entire data structure
                        return true;
                    }
                }
            }
        }
        return false;
    }
    
    
    public function search() {
        $inputnama = "";
    
        // Check if the search form was submitted
        if (isset($_POST["submit"])) {
            $inputnama = trim($_POST["search"]);
        }
    
        // Load the full dataset
        $data = $this->loadData();
        $matchingData = [];
    
        // If input is empty, return to default table view
        if ($inputnama === "") {
            $this->tableDefault();
            return;
        }
    
        // If input is not empty, search the data
        if ($inputnama !== "") {
            if (in_array($inputnama, array_column($data, 'nama'))) {
                foreach ($data as $value) {
                    if ($value['nama'] === $inputnama) {
                        $matchingData[] = $value;
                    }
                }
    
                // If matching data found, display it in table
                if (!empty($matchingData)) {

                    foreach ($matchingData as $index => $row) {
                        $nosiswa = ''; // Reset $nosiswa for each row
                        $urutan = 1; // This tracks the column number
                
                        echo '<tr>';
                        foreach ($row as $cell) {
                            // Check if the current column is the "No Siswa" column
                            if ($urutan == 3) {
                                $nosiswa = htmlspecialchars($cell); // Store No Siswa
                                echo '<td>' . $nosiswa . '</td>';
                            } else {
                                echo '<td>' . htmlspecialchars($cell) . '</td>';
                            }
                            $urutan++;
                        }
                
                        // Tombol Edit dan Delete
                        echo "<td scope='col'>
                                <form action='' method='post' style='display:inline;'>
                                    <input type='hidden' name='edit_index' value='$nosiswa'>
                                    <button type='submit' name='edit' class='btn btn-warning mx-1'>Edit</button>
                                </form>
                                <form action='' method='post' style='display:inline;'>
                                    <input type='hidden' name='delete_index' value='$nosiswa'>
                                    <button type='submit' name='delete' class='btn btn-danger mx-1'>Delete</button>
                                </form>
                              </td>";
                
                        // Tombol Status Kehadiran
                        echo "<td scope='col'>
                                <form action='' method='post' style='display:inline;'>
                                    <input type='hidden' name='status_index' value='$nosiswa'>
                                    <button type='submit' name='status_hadir' class='btn btn-success mx-1'>Hadir</button>
                                    <button type='submit' name='status_sakit' class='btn btn-warning mx-1'>Sakit</button>
                                    <button type='submit' name='status_izin' class='btn btn-primary mx-1'>Izin</button>
                                    <button type='submit' name='status_alpha' class='btn btn-danger mx-1'>Alpha</button>
                                </form>
                              </td>";
                
                        echo '</tr>';
                    }
                } else {
                    // If no matching data, display "Data not found"
                    echo '<tr><td colspan="9">Data not found</td></tr>';
                }
            } else {
                // If no matching data, display "Data not found"
                echo '<tr><td colspan="9">Data not found</td></tr>';
            }
        }
    }
}

$obj = new DataBump();
$obj->resethari();

if (isset($_POST['edit'])) {
    $editIndex = $_POST['edit_index'];
    header("Location: edit.php?no_siswa=$editIndex");
    exit;
}

if (isset($_POST['delete'])) {
    $deleteIndex = $_POST['delete_index'];
    $obj->deleteDataByNoSiswa($deleteIndex);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

if (isset($_POST['status_hadir'])) {
    $index = $_POST['status_index'];
    $obj->updateStatus($index, 'H');
    $obj->keterangan($index,"Siswa Hadir");
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

if (isset($_POST['status_sakit'])) {
    $index = $_POST['status_index'];
    $obj->updateStatus($index, 'S');
    $obj->keterangan($index,"Siswa Sakit");
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

if (isset($_POST['status_izin'])) {
    $index = $_POST['status_index'];
    $obj->updateStatus($index, 'I');
    $obj->keterangan($index,"Siswa Izin");
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

if (isset($_POST['status_alpha'])) {
    $index = $_POST['status_index'];
    $obj->updateStatus($index, 'A');
    $obj->keterangan($index,"Siswa Alpha");
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/style_edit.css">
</head>
<body class="d-flex flex-column align-items-center vh-100 bg-info">
    <div class="container">
        <h1 class="fw-bold mt-5 text-center">MENU ABSENSI</h1>

        <div class="col-12 d-flex justify-content-between mt-5">
            <div>
                <button class="btn btn-success" onclick="window.location.href = 'add.php'">ADD NEW STUDENT</button>
                <button class="btn btn-danger" onclick="window.location.href = 'log_out.php'">Keluar</button>
                <button class="btn btn-primary" onclick="window.print()">Print</button>
                <button class="btn btn-secondary" onclick="window.location.href = 'hadir.php'">Siswa Hadir</button>
                <button class="btn btn-light" onclick="window.location.href = 'sakit.php'">Siswa Sakit</button>
                <button class="btn btn-warning" onclick="window.location.href = 'izin.php'">Siswa Izin</button>
                <button class="btn btn-dark" onclick="window.location.href = 'alpha.php'">Siswa Alpha</button>
            </div>
            <form action="" method="post" class="d-flex align-items-center">
                <label class="h4 me-3">Search:</label>
                <input type="text" class="form-control me-2 ps-2" name="search" placeholder="Masukkan Nama">
                <input type="submit" name="submit" class="btn btn-dark text-light rounded">
            </form>
        </div>

        <table class="table border-dark table-bordered table-hover text-center mt-4" id="tabel">
            <thead>
                <tr class="bg-dark text-light border-light">
                    <th scope="col">Nama</th>
                    <th scope="col">Kelas</th>
                    <th scope="col">No Siswa</th>
                    <th scope="col">Gender</th>
                    <th scope="col">Tanggal Lahir</th>
                    <th scope="col">Action</th>
                    <th scope="col">Set Absensi</th>
                </tr>
            </thead>
            <tbody class="bg-light">
                <?= $obj->search(); ?>
            </tbody>
        </table>
    </div>
</body>
</html>
