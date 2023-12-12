<?php
if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SESSION['username'])) {
    header("Location: index.php?page=loginUser");
    exit;
}
include_once("koneksi.php");
$id_periksa = $_GET['id'];
$data_user = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT * FROM user WHERE username = '" . $_SESSION['username'] . "' LIMIT 1"));
$data_periksa = mysqli_fetch_assoc(mysqli_query($mysqli, "
                SELECT 
                    pr.*,
                    pa.nama AS 'nama_pasien',
                    pa.alamat AS 'alamat_pasien',
                    pa.no_hp AS 'no_hp_pasien',
                    d.nama AS 'nama_dokter',
                    d.alamat AS 'alamat_dokter',
                    d.no_hp AS 'no_hp_dokter'
                FROM periksa pr
                INNER JOIN pasien pa ON pr.id_pasien = pa.id
                INNER JOIN dokter d ON pr.id_dokter = d.id
                WHERE pr.id = '" . $id_periksa . "' LIMIT 1
            "));
//Biaya Obat

$query = "
    SELECT 
        o.nama_obat,
        o.harga
    FROM periksa p
    INNER JOIN detail_periksa dp ON p.id = dp.id_periksa
    INNER JOIN obat o ON dp.id_obat = o.id
    WHERE p.id = '" . $id_periksa . "'
";
$result = mysqli_query($mysqli, $query);

//Biaya Layanan Dokter
//Statis, tiap dokter harganya sama
$biaya_layanan_dokter = 150000;
$total_biaya_obat = 0;
function rupiah($angka){
    $hasil_rupiah = "Rp " . number_format($angka,2,',','.');
    return $hasil_rupiah;
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="css/invoice.css" type="text/css">
    <title>Document</title>
</head>
<body>
<div class="container mt-6 mb-7">
    <div class="row justify-content-center">
        <div class="col-lg-12 col-xl-7">
            <div class="card">
                <div class="card-body p-5">
                    <h2>
                        Nota Pembayaran
                    </h2>

                    <div class="border-top border-gray-200 pt-4 mt-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="text-muted mb-2">No. Periksa</div>
                                <strong>#<?= $data_periksa['id'] ?></strong>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <div class="text-muted mb-2">Tanggal Periksa</div>
                                <strong><?= $data_periksa['tgl_periksa'] ?></strong>
                            </div>
                        </div>
                    </div>

                    <div class="border-top border-gray-200 mt-4 py-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="text-muted mb-2">Pasien</div>
                                <strong>
                                    <?= $data_periksa['nama_pasien'] ?>
                                </strong>
                                <p class="fs-sm">
                                    <?= $data_periksa['alamat_pasien'] ?>
                                    <br>
                                    <a href="#!" class="text-purple"><?= $data_periksa['no_hp_pasien']?>
                                    </a>
                                </p>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <div class="text-muted mb-2">Dokter</div>
                                <strong>
                                    <?= $data_periksa['nama_dokter']?>
                                </strong>
                                <p class="fs-sm">
                                    <?= $data_periksa['alamat_dokter'] ?>
                                    <br>
                                    <a href="#!" class="text-purple"><?= $data_periksa['no_hp_dokter']?>
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>

                    <table class="table border-bottom border-gray-200 mt-3">
                        <thead>
                        <tr>
                            <th scope="col" class="fs-sm text-dark text-uppercase-bold-sm px-0">Deskripsi</th>
                            <th scope="col" class="fs-sm text-dark text-uppercase-bold-sm text-end px-0">Harga</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="px-0">Jasa Dokter</td>
                            <td class="text-end px-0"><?= rupiah($biaya_layanan_dokter) ?></td>
                        </tr>
                        <?php
                            while($data_obat = mysqli_fetch_array($result)) {
                                $total_biaya_obat += $data_obat['harga'];
                        ?>
                            <tr>
                                <td class="px-0"><?= $data_obat['nama_obat'] ?></td>
                                <td class="text-end px-0"><?= rupiah($data_obat['harga']) ?></td>
                            </tr>
                        <?php
                            }
                        ?>
                        </tbody>
                    </table>

                    <div class="mt-5">
                        <div class="d-flex justify-content-end">
                            <p class="text-muted me-3">Jasa Dokter:</p>
                            <span><?= rupiah($biaya_layanan_dokter) ?></span>
                        </div>
                        <div class="d-flex justify-content-end">
                            <p class="text-muted me-3">Subtotal Obat:</p>
                            <span><?= rupiah($total_biaya_obat) ?></span>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            <h5 class="me-3">Total:</h5>
                            <h5 class="text-success"><?= rupiah($biaya_layanan_dokter + $total_biaya_obat) ?></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
</body>
</html>