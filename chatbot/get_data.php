<?php
$jsonFilePath = '../data/university_data.json'; 

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-Type: application/json'); // Beritahu browser bahwa ini adalah file JSON

// Cek apakah file ada, lalu baca dan tampilkan isinya
if (file_exists($jsonFilePath)) {
    readfile($jsonFilePath);
} else {
    // Jika file tidak ditemukan, kirim response error
    http_response_code(404);
    echo json_encode(["error" => "Data tidak ditemukan."]);
}

exit();
?>
