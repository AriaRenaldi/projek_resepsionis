<?php
include '../koneksi.php';

$id = $_GET['id'] ?? '';

if ($id) {
    $query = "DELETE FROM admin WHERE id = '$id'";
    mysqli_query($conn, $query);
}

header("Location: ../tamu/tamu.php");
exit;
