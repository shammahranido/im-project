<?php
include_once 'admin-db.php';
include_once 'admin-auth.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $password = $_POST['password'];
    
    $database = new Database('localhost', 'postgres', 'postgres', '0205');
    $pdo = $database->getPdo();
    $auth = new Auth();
    
    $isAuthenticated = $auth->login($pdo, $id, $password);

    if ($isAuthenticated) {
        unset($_SESSION['form_data']);
        header('Location: admin-menu.php');
        exit;
    } else {
        $_SESSION['form_data'] = [
            'id' => $id,
            'password' => $password,
        ];
        header('Location: admin-frontend-login.php');
        exit;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 h-screen flex flex-col">
    <nav class="bg-green-500 p-4 w-full">
        <div class="container mx-auto flex justify-between items-center">
            <a href="admin-homepage.html" class="text-white font-bold">Welcome</a>
        </div>
    </nav>
    <div class="flex flex-grow items-center justify-center">
        <div class="w-full max-w-xs">
            <form action="admin-frontend-login.php" method="post" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                <h2 class="text-2xl font-bold mb-2">Admin Login</h2>
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="mt-4 text-xs m">
                        <p class="text-red-500"><?php echo $_SESSION['error']; ?></p>
                        <?php unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>
                <div class="mb-4">
                    <label for="id" class="block text-gray-700 text-sm font-bold mb-2">ID Number</label>
                    <input type="text" id="id" name="id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required value="<?php echo isset($_SESSION['form_data']['id']) ? htmlspecialchars($_SESSION['form_data']['id']) : ''; ?>">
                </div>
                <div class="mb-6">
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                    <input type="password" id="password" name="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required value="<?php echo isset($_SESSION['form_data']['password']) ? htmlspecialchars($_SESSION['form_data']['password']) : ''; ?>">
                </div>
                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Login</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
