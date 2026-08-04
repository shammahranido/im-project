<?php
session_start();

include_once 'admin-db.php';
include 'all-process.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $id = $_POST['id'];
    $password = $_POST['password'];

    $database = new Database('localhost', 'postgres', 'postgres', '0205');
    $pdo = $database->getPDO();

    $signUp = new AllProcess($pdo);
    $result = $signUp->insertAdmin($name, $id, $password);

    if ($result['valid']) {
        $_SESSION['message']['success'] = $result['message'];
        unset($_SESSION['form_data']);
    } else {
        $_SESSION['message']['error'] = $result['message'];

        $_SESSION['name_error'] = $result['name_error'] ?? '';
        $_SESSION['id_error'] = $result['id_error'] ?? '';
        $_SESSION['password_error'] = $result['password_error'] ?? '';
        $_SESSION['general_error'] = $result['general_error'] ?? '';

        $_SESSION['form_data'] = [
            'name' => $name,
            'id' => $id,
            'password' => $password,
        ];
    }

    header("Location: admin-frontend-signup.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
<nav class="bg-green-500 p-4 mb-10">
    <div class="container mx-auto flex justify-between items-center">
        <a href="#" class="text-white font-bold"></a>
        <a href="admin-menu.php?page=admin" class="ml-4 text-white font-bold">X</a>
    </div>
</nav>
<div class="max-w-md bg-white rounded-md p-8 shadow-md w-2/3 mx-auto">
    <h1 class="text-2xl font-bold mb-4">Admin Sign Up</h1>
    <?php if (isset($_SESSION['message']['success'])): ?>
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mb-3" role="alert">
            <strong class="font-bold">Success!</strong>
            <span class="block sm:inline"><?php echo $_SESSION['message']['success']; ?></span>
        </div>
        <?php unset($_SESSION['message']['success']); ?>
    <?php endif; ?>
    <form action="admin-frontend-signup.php" method="post" class="bg-white p-6 rounded shadow-md">
        <div class="mb-4">
            <label for="name" class="block text-gray-700">Name</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($_SESSION['form_data']['name'] ?? ''); ?>" class="mt-1 mb-3 block w-full border-gray-300 rounded-md shadow-sm">
            <?php if (isset($_SESSION['name_error'])): ?>
                <p class="text-red-500 text-xs"><?php echo $_SESSION['name_error']; ?></p>
                <?php unset($_SESSION['name_error']); ?>
            <?php endif; ?>
        </div>
        <div class="mb-4">
            <label for="id" class="block text-gray-700">ID Number</label>
            <input type="text" id="id" name="id" value="<?php echo htmlspecialchars($_SESSION['form_data']['id'] ?? ''); ?>" class="mt-1 mb-3 block w-full border-gray-300 rounded-md shadow-sm">
            <?php if (isset($_SESSION['id_error'])): ?>
                <p class="text-red-500 text-xs"><?php echo $_SESSION['id_error']; ?></p>
                <?php unset($_SESSION['id_error']); ?>
            <?php endif; ?>
        </div>
        <div class="mb-4">
            <label for="password" class="block text-gray-700">Password</label>
            <input type="password" id="password" name="password" value="<?php echo htmlspecialchars($_SESSION['form_data']['password'] ?? ''); ?>" class="mt-1 mb-3 block w-full border-gray-300 rounded-md shadow-sm">
            <?php if (isset($_SESSION['password_error'])): ?>
                <p class="text-red-500 text-xs"><?php echo $_SESSION['password_error']; ?></p>
                <?php unset($_SESSION['password_error']); ?>
            <?php endif; ?>
        </div>
        <?php if (isset($_SESSION['general_error'])): ?>
            <p class="text-red-500 text-xs"><?php echo $_SESSION['general_error']; ?></p>
            <?php unset($_SESSION['general_error']); ?>
        <?php endif; ?>
        <button type="submit" name="submit" class="bg-green-500 text-white mt-5 py-2 px-4 rounded hover:bg-green-700">Submit</button>
    </form>
</div>
</body>
</html>
