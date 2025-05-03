<?php
require '../con.php';

if(isset($_POST['submit'])) {  
    $ip = $_SERVER['REMOTE_ADDR'];
    $limit = 5;
    $second = 60;
    
    if (!isset($_SESSION['rate_limit'])) {
        $_SESSION['rate_limit'] = [];
    }
    if(!isset($_SESSION['rate_limit'][$ip])) {
        $_SESSION['rate_limit'][$ip] = [];
    }
    
    $_SESSION['rate_limit'][$ip] = array_filter(
        $_SESSION['rate_limit'][$ip],
        fn($timestamp) => $timestamp > time() - $second
    );
    
    if (count($_SESSION['rate_limit'][$ip]) >= $limit) {
        $message = "Terlalu banyak permintaan. Coba lagi nanti.";
    } else {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $query = "SELECT * FROM `admin` WHERE username = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result->fetch_assoc();

        if ($result->num_rows > 0 && password_verify($password, $rows['password'])) {
            $token = uniqid();
            $_SESSION['token'] = [
                'token' => password_hash($token, PASSWORD_DEFAULT),
                'id' => $rows['id']
            ];

            $update = "UPDATE `admin` SET `token`= ?";
            $stmt_update = $con->prepare($update);
            $stmt_update->bind_param("s", $token);
            $stmt_update->execute();
            header("Location: ../admin");
            exit();
        } else {
            $message = "Username atau password salah";
        }

        $_SESSION['rate_limit'][$ip][] = time();
    }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login admin</title>
    <link rel="stylesheet" href="<?php echo $host ?>/assets/css/style.css">
</head>
<body>
    <div class="m-4 sm:mx-8">
        <a href="../" class="text-2xl font-bold">Journal UMK</a>
        <div class="sm:mx-auto mt-10 p-8 border shadow border-gray-100 sm:w-[400px]">
            <form method="post" class="flex flex-col gap-4">
                <h1 class="text-xl font-bold">Admin Journal UMKAL</h1>
                <p class="text-red-500 text-sm"><?php echo $message ?></p>
                <input type="text" name="username" placeholder="Username" class="px-5 py-2 border border-gray-200 w-full rounded outline-gray-300">
                <input type="text" name="password" placeholder="Password" class="px-5 py-2 border border-gray-200 w-full rounded outline-gray-300">
                <button type="submit" name="submit" class="px-5 py-2 bg-black text-white font-bold rounded cursor-pointer">Login</button>
            </form>
        </div>
    </div>
    <footer class="absolute bottom-4 sm:bottom-8 left-1/2 text-nowrap bg-red-50 -translate-x-1/2">
        <p>Design by commit team with ♥️</p>
    </footer>
</body>
</html>