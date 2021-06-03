<?php
// Koneksi ke Database
$conn = mysqli_connect("localhost", "root", "", "mahasiswa");

function query($query) {
    global $conn;
    $result = mysqli_query($conn, $query);
    $rows = [];
    while ( $row = mysqli_fetch_assoc($result) ) {
        $rows[] = $row;
    }
    return $rows;
}



function tambah($data) {
    // ambil data dari tiap elemen dalam form.
    global $conn;
    
    $nama = htmlspecialchars($data["nama"]);
    $nim = htmlspecialchars($data["nim"]);
    $email = htmlspecialchars($data["email"]);
    $jurusan = htmlspecialchars($data["jurusan"]);

    // UPLOAD GAMBAR
    $gambar = upload();
    if( !$gambar ) {
        return false;
    }

    // query insert data
    $query = "INSERT INTO mahasiswa
                VALUES
                ('', '$nama', '$nim', '$email', '$jurusan', '$gambar')
                ";
    mysqli_query($conn, $query);

    return mysqli_affected_rows($conn);
}

function upload() {
    
    $namaFile = $_FILES['gambar']['name'];
    $ukuranFile = $_FILES['gambar']['size'];
    $error = $_FILES['gambar']['error'];
    $tmpName = $_FILES['gambar']['tmp_name'];

    // Cek apakah tidak ada gambar yang diupload
    if( $error === 4 ){
        echo "<script>
        alert('Pilih Gambar Dulu Kali Cuy!');
        </script>";
    return false;
    }

     // Cek apakah yang diupload adalah gambar
     $formatGambarValid = ['jpg', 'jpeg', 'png'];
     $formatGambar = explode('.', $namaFile);
     $formatGambar = strtolower(end($formatGambar));
     if ( !in_array($formatGambar, $formatGambarValid) )
     {
         echo "<script>
             alert('Yang Lo Upload Bukan Gambar Cuy!');
         </script>";
        return false;
     }

     // Cek jika ukurannya terlalu besar
     if( $ukuranFile > 1000000) {
        echo "<script>
            alert('Ukuran Gambar Terlalu Besar Cuy!');
        </script>";
       return false;
     }

     // Lolos pengecekan, GAMBAR siap diUPLOAD
    // Sebelumnya nama file gambar tidak boleh sama harus di generate
    $namaFileBaru = uniqid();
    $namaFileBaru .= '.';
    $namaFileBaru .= $formatGambar;

     move_uploaded_file($tmpName, 'img/' . $namaFileBaru);

     return $namaFileBaru;
}

function hapus($id) {
    global $conn;
    mysqli_query($conn, "DELETE FROM mahasiswa WHERE id = $id");
    return mysqli_affected_rows($conn);
}

function ubah($data) {
    global $conn;
    
    $id = $data["id"];
    $nama = htmlspecialchars($data["nama"]);
    $nim = htmlspecialchars($data["nim"]);
    $email = htmlspecialchars($data["email"]);
    $jurusan = htmlspecialchars($data["jurusan"]);
    $gambarLama = htmlspecialchars($data["gambarLama"]); 
    // Cek apakah USER pilh GAMBAR baru atau tidak
    if( $_FILES['gambar']['error'] === 4 ) {
        $gambar = $gambarLama;
    } else {
        $gambar = upload(); 
    }
    

    // Query UPDATE/UBAH data
    $query = "UPDATE mahasiswa SET
                nama = '$nama', 
                nim = '$nim', 
                email = '$email',
                jurusan = '$jurusan', 
                gambar = '$gambar'
            WHERE id = $id
        ";
    mysqli_query($conn, $query);

    return mysqli_affected_rows($conn); 
}


function cari($keyword) {
    $query = "SELECT * FROM mahasiswa
                WHERE
            nama LIKE '%$keyword%' OR
            nim LIKE '%$keyword%' OR
            email LIKE '%$keyword%' OR
            jurusan LIKE '%$keyword%'
            ";
    return query($query);
}

function registrasi($data) {
    global $conn;

    $username = strtolower(stripslashes($data["username"]));
    $password = mysqli_real_escape_string($conn, $data["password"]);
    $password2 = mysqli_real_escape_string($conn, $data["password2"]);

    // CEK USERNAME SUDAH ADA ATAU BELUM
    $result = mysqli_query($conn, "SELECT username FROM user WHERE username = '$username'");

    if( mysqli_fetch_assoc($result) ) {
        echo "<script>
            alert('Username Sudah Terdaftar Cuy!, Coba Dah Cari Username Lain');
            </script>";
            return false;
    }

    // CEK KONFIRMASI PASSWORD
    if ($password !== $password2) {
        echo "<script>
                alert('Konfirmasi Password Tidak Sesuai Cuy!')
                </script>";
        return false;
    }

    // enkripsi password
    $password = password_hash($password, PASSWORD_DEFAULT);

    // Tambahkan User Baru ke DATABASE
    mysqli_query($conn, "INSERT INTO user VALUES('', '$username', '$password')");

    return mysqli_affected_rows($conn);
}





?>