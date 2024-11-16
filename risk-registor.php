<?php
    include_once("config.php"); // Include config file

    $conn = mysqli_query($mysqli, "SELECT * FROM `risk_register`;"); // Fetch all risks from the database
?>

<!-- HTML sections -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Risk Register</title>
</head>
<body>
    <!-- Table Risk Register Start -->
    <table border="1" width="1600">
        <tr>
            <th colspan="21">Risk Register</th>
        </tr>
        <tr>
            <th rowspan="2">No</th>
            <th rowspan="2">Objective / Tujuan</th>
            <th rowspan="2">Proses Bisnis</th>
            <th rowspan="2">Risk Category / Jenis Kelompok Risiko</th>
            <th rowspan="2">Kode Risiko</th>
            <th rowspan="2">Risk Event / Uraian Peristiwa Risiko</th>
            <th rowspan="2">Risiko Course / Penyebab Risiko</th> 
            <th rowspan="2">Sumber Risiko (Internal / Eksternal)</th>
            <th rowspan="1" colspan="2">Severity Akibat / Potensi Kerugian</th>
            <th>Risk Owner / Pemilik Risiko</th>
            <th>Nama Dept. / Unit Terkait</th>
            <th rowspan="1" colspan="3">Score / Nilai Inherent Risk</th>
            <th rowspan="1" colspan="3">Existing Control / Pengendalian Yang Ada </th>
            <th rowspan="1" colspan="3">Score / Nilai Residual Risk </th>
            <th rowspan="1" colspan="2">Risk Treatment </th>
            <th rowspan="1" colspan="3">Score / Nilai Target Risk After Mitigation </th>
        </tr>
        <tr>
            <th>Qualitative</th>
            <th>Rp</th>
            <th>Likelihood</th>
            <th>Impact</th>
            <th>Level of Risk / Tingkat Risiko</th>
            <th>Ada / Tidak Ada</th>
            <th>Memadai / Belum Memadai</th>
            <th>Dijalankan 100% / Belum Dijalankan 100%</th>
            <th>Likelihood</th>
            <th>Impact</th>
            <th>Level of Risk / Tingkat Risiko</th>
            <th>Opsi Perlakuan Risiko</th>
            <th>Deskripsi Tindakan Mitigasi</th>
            <th>Likelihood</th>
            <th>Impact</th>
            <th>Level of Risk / Tingkat Risiko</th>
        </tr>
    </table>
    <!-- Table Risk Register End -->

    
</body>
</html>