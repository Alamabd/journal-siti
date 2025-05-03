<?php
require '../con.php';

// Check auth
if(!isset($_SESSION['token'])) {
    header('Location: ../login');
    exit();
}

$stmt_auth = $con->prepare("SELECT `token` FROM `admin` WHERE id = ?");
$stmt_auth->bind_param("s", $_SESSION['token']['id']);
$stmt_auth->execute();
$result_auth = $stmt_auth->get_result();
$auth_rows = $result_auth->fetch_assoc();
if($result_auth->num_rows > 0) {
    if(!password_verify($auth_rows['token'], $_SESSION['token']['token'])) {
        header('Location: ../login');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="<?php echo $host ?>/assets/css/style.css">
</head>
<body>
    <div class="m-4 sm:mx-8">
        <a href="../" class="text-2xl font-bold">Journal UMK</a>
        <div class="sm:mx-auto mt-10 p-8 border shadow border-gray-100 sm:w-[400px]">
            <form method="post" class="flex flex-col gap-4">
                <h1 class="text-xl font-bold">Tambahkan Journal UMKAL</h1>
                <p class="text-red-500 text-sm"><?php echo $message ?></p>
                <input type="text" name="username" placeholder="Username" class="px-5 py-2 border border-gray-200 w-full rounded outline-gray-300">
                <input type="text" name="password" placeholder="Password" class="px-5 py-2 border border-gray-200 w-full rounded outline-gray-300">
                <button type="submit" name="submit" class="px-5 py-2 bg-black text-white font-bold rounded cursor-pointer">Login</button>
            </form>
        </div>
    </div>
</body>
</html>