<?php 
session_start();
require "function.php";

// CEK COOKIE
if ( isset($_COOKIE['id']) && isset($_COOKIE['key'])) {
    $id = $_COOKIE['id'];
    $key = $_COOKIE['key'];

    // AMBIL USERNAME BEDASARKAN ID
    $result = mysqli_query($conn, "SELECT username FROM user WHERE id = $id");
    $row = mysqli_fetch_assoc($result);

    // CEK COOKIE DAN USERNAME
    if ( $key === hash('sha256', $row['username']) ) {
        $_SESSION['login'] = true;
    }


}



if( isset($_SESSION["login"]) ) {
    header("Location: index.php");
    exit;
}

    if( isset($_POST["login"]) ) {

        $username = $_POST["username"];
        $password = $_POST["password"];

        $result = mysqli_query($conn, "SELECT * FROM user WHERE username = '$username'");

        // CEK USERNAME
        if( mysqli_num_rows($result) === 1 ) {

            // CEK PASSWORD
            $row = mysqli_fetch_assoc($result);
            if( password_verify($password, $row["password"]) ) {
                // SET SESSION
                $_SESSION["login"] = true;

                // CEK REMEMBER ME
                if( isset($_POST['remember']) ) {
                    // BUAT COOKIE
                    setcookie('id', $row['id'], time()+60);
                    setcookie('key', hash('sha256', $row['username']), time()+60);
                }
                
                header("Location: index.php");
                exit;
            }
        }
        $error = true;
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Login</title>

    <style>
        label {
            display: block;
        }
        ul li {
            padding-bottom: 5px; 
        }

    </style>
</head>
<body>
    <h1>Halaman Login</h1>

        <?php if( isset($error) ) : ?>
            <p style="color: red; font-style: italic">Username / Password Salah Cuy</p>
        <?php endif; ?>

    <form action="" method="POST">

        <ul>
            <li>
                <label for="username">Username :</label>
                <input type="text" name="username" id="username" autocomplete="off">
            </li>
            <li>
                <label for="password">Password :</label>
                <input type="password" name="password" id="password" autocomplete="off">
            </li>
            <li>
                <input type="checkbox" name="remember" id="remember">
                <label for="remember">Remember Me</label>
            </li>
            <li>
                <button type="submit" name="login">Login</button>
            </li>
        </ul>

    </form>
</body>
</html>