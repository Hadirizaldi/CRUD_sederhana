<?php 
require_once 'db_koneksi.php';


function query($query) {
	global $conn;
	$result = mysqli_query($conn, $query);
	$rows = [];
	while( $row = mysqli_fetch_assoc($result) ) {
		$rows[] = $row;
	}
	return $rows;
}


function tambah($data) {
	global $conn;

	$nrp = htmlspecialchars($data["nrp"]);
	$nama = htmlspecialchars($data["nama"]);
	$email = htmlspecialchars($data["email"]);
	$jurusan = htmlspecialchars($data["jurusan"]);
    
    //upload gambar
    $gambar = upload();
    if (!$gambar) {
        return false;
    }

	$query = "INSERT INTO mahasiswa
			    VALUES
			    ('', '$nrp', '$nama', '$email', '$jurusan', '$gambar')
			";
	mysqli_query($conn, $query);

	return mysqli_affected_rows($conn);
}

function upload() {
    $namaFile = $_FILES['gambar']['name'];
    $ukuranFile = $_FILES['gambar']['size'];
    $error = $_FILES['gambar']['error'];
    $tmpName = $_FILES['gambar']['tmp_name'];

    //  cek ada gambar di upload
    if ($error === 4) {
        echo "
            <script>
                alert('pilih gambar terlebih dahulu');
                document.location.href = 'tambah.php';
            </script>
        ";
        return false;
    }

    //cek upload = gambar
    $extensionValid = ['jpg', 'jpeg', 'png'];
    $extensionGambar = explode('.', $namaFile);
    $extensionGambar = strtolower(end($extensionGambar));

    if (!in_array($extensionGambar, $extensionValid)) {
        echo "
            <script>
                alert('Yang anda Upload bukan gambar');
                document.location.href = 'tambah.php';
            </script>
        ";
        return false;
    }

    // cek ukuran gambar
    if ($ukuranFile > 1000000) {
        echo "
            <script>
                alert('Yang anda Upload bukan gambar');
                document.location.href = 'tambah.php';
            </script>
        ";
    return false;
    }

    // pengecekan lolos
	// generate nama baru
	$namaBaru = uniqid(). '.' . $extensionGambar;

    move_uploaded_file($tmpName, 'img/'. $namaBaru);
    return $namaBaru;
}


function hapus($id) {
	global $conn;
	mysqli_query($conn, "DELETE FROM mahasiswa WHERE id = $id");
	return mysqli_affected_rows($conn);
}


function ubah($data) {
	global $conn;

	$id = $data["id"];
	$nrp = htmlspecialchars($data["nrp"]);
	$nama = htmlspecialchars($data["nama"]);
	$email = htmlspecialchars($data["email"]);
	$jurusan = htmlspecialchars($data["jurusan"]);
	$gambarLama = $data["gambarLama"];

	// cek user milih gambar baru
	if ($_FILES['gambar']['error'] === 4) {
		$gambar = $gambarLama;
	}
	else {
		$gambar = upload();
	}


	$query = "UPDATE mahasiswa SET
				nrp = '$nrp',
				nama = '$nama',
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
			        nrp LIKE '%$keyword%' OR
			        email LIKE '%$keyword%' OR
			        jurusan LIKE '%$keyword%'
			";
	return query($query);
}


function registrasi($data) {
	global $conn;
	
	$username = trim(strtolower(stripslashes($data['username'])));
	$password = mysqli_escape_string($conn, $data['password']);
	$confirmPassw = mysqli_escape_string($conn, $data['confirmpassw']);

	// cek username sudah ada atau belum
	$result = mysqli_query($conn, "SELECT username FROM users 
							WHERE username = '$username'");
	
	if(mysqli_fetch_assoc($result)) { // bernilai true berarti username ada
		echo "<script>
				alert('username sudah terdaftar');
			</script>";
		return false;
	}

	// cek confirmasi password
	if ($password !== $confirmPassw) {
		echo "<script>
				alert('konfirmasi password tidak sesuai');
			</script>";

		return false;
	}

	// enkripsi password
	$password = password_hash($password, PASSWORD_DEFAULT);
	
	// tambahkan user baru ke database
	$query = "INSERT INTO users 
				VALUES('', '$username', '$password')
			";
	mysqli_query($conn, $query);

	return mysqli_affected_rows($conn);
}