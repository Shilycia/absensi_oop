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

    public function tableDefault() {
        $data = $this->loadData();
    
        if ($data) {
            foreach ($data as $index => $row) {
                $nosiswa = ''; // Reset $nosiswa for each row
                $urutan = 1; // This tracks the column number
                $break = 1;
    
                // Check if status, keterangan, or tanggal_hadir are not set to specific values
                if ($row['status'] != 'A' ||
                    $row['keterangan'] != 'Siswa Alpha' ||
                    $row['tanggal_hadir'] != date("Y-m-d")) {
                    continue; // Skip this row if conditions are not met
                }
                

                
                echo '<tr>';
                
                foreach ($row as $key => $cell) {
                        // Check if the current column is the "No Siswa" column
                        if ($key == "tanggal_lahir"){
                            continue;
                        }
                        if ($urutan == 3) {
                            $nosiswa = htmlspecialchars($cell); // Store No Siswa
                            echo '<td>' . $nosiswa . '</td>';
                        } else {
                            echo '<td>' . htmlspecialchars($cell) . '</td>';
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
        
                    echo '</tr>';
                    if($break <= 2){
                        echo '<tr><td colspan="9">No data available</td></tr>';
                    }
                }
            } else {
            echo '<tr><td colspan="9">No data available</td></tr>';
        }
    }
        

    public function deleteDataByNoSiswa($no_siswa) {
        $data = $this->source();
        
        // Check if data exists and iterate through the data
        foreach ($data as &$item) {
            if ($item['status'] == 200 && isset($item['data'])) {
                // Search for the student with the matching no_siswa
                foreach ($item['data'] as &$student) {
                    if (isset($student['no_siswa']) && $student['no_siswa'] == $no_siswa) {
                        // Set 'status', 'keterangan', and 'tanggal_hadir' to null
                        $student['status'] = null;
                        $student['keterangan'] = null;
                        $student['tanggal_hadir'] = null;
                        
                        // Save the updated data back to the file
                        $this->saveData($data);
                        
                        return true; // Return true to indicate successful update
                    }
                }
            }
        }
        
        return false; // Return false if no student with the given no_siswa was found
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
                                $nosiswa = htmlspecialchars($cell); 
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
        <h1 class="fw-bold mt-5 text-center">TABLE SISWA Alpha</h1>

        <div class="col-12 d-flex justify-content-between mt-5">
            <div>
                <button class="btn btn-success" onclick="window.location.href = 'add.php'">ADD NEW STUDENT</button>
                <button class="btn btn-danger" onclick="window.location.href = 'log_out.php'">Keluar</button>
                <button class="btn btn-primary" onclick="window.print()">Print</button>
                <button class="btn btn-secondary" onclick="window.location.href = 'index.php'">Home</button>
                <button class="btn btn-light" onclick="window.location.href = 'hadir.php'">Siswa Hadir</button>
                <button class="btn btn-warning" onclick="window.location.href = 'sakit.php'">Siswa Sakit</button>
                <button class="btn btn-dark" onclick="window.location.href = 'izin.php'">Siswa Izin</button>
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
                    <th scope="col">Status</th>
                    <th scope="col">Keterangan</th>
                    <th scope="col">tanggal hadir</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody class="bg-light">
                <?= $obj->search(); ?>
            </tbody>
        </table>
    </div>
</body>
</html>
