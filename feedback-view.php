<?php
session_start();

$host = 'localhost';
$dbname = 'postgres';
$user = 'postgres';
$password = '0205';

try {
    $dsn = "pgsql:host=$host;dbname=$dbname";
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $stmt = $pdo->query("SELECT fb_id, fb_name, fb_email, fb_message FROM feedback");
    $feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $_SESSION['message'] = 'Connection failed: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="bg-gray-100">
    <nav class="bg-green-500 p-4 w-full">
        <div class="container mx-auto flex justify-between items-center">
            <a href="admin-menu.php" class="text-white font-bold">ADMIN</a>
            <a href="admin-frontend-login.php" class="text-white font-bold">LogOut</a>
        </div>
    </nav>
    
    <div class="container mx-auto p-6">
        <div class="flex justify-between mb-4">
            <a href="admin-menu.php" class="bg-green-500 text-white font-bold py-2 px-4 rounded hover:bg-green-700">Back to Admin Menu</a>
        </div>
        <div class="bg-white p-6 rounded shadow-md">
            <h2 class="text-2xl text-right font-bold my-6 text-yellow-400">Customer Feedback</h2>
            <?php if (isset($_SESSION['message'])): ?>
                <p class="text-red-500"><?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?></p>
            <?php endif; ?>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead>
                        <tr class="w-full bg-green-200 text-left">
                            <th class="py-2 px-4 border-b">Name</th>
                            <th class="py-2 px-4 border-b">Email</th>
                            <th class="py-2 px-4 border-b">Message</th>
                            <th class="py-2 px-4 border-b text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($feedbacks as $feedback): ?>
                            <tr class="hover:bg-green-200 bg-green-50">
                                <td class="py-2 px-4 border-b whitespace-nowrap"><?php echo htmlspecialchars_decode($feedback['fb_name']); ?></td>
                                <td class="py-2 px-4 border-b whitespace-nowrap"><?php echo htmlspecialchars($feedback['fb_email']); ?></td>
                                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars_decode($feedback['fb_message']); ?></td>
                                <td class="pl-8">
                                    <form action="feedback-delete.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this feedback?');">
                                        <input type="hidden" name="fb_id" value="<?php echo $feedback['fb_id']; ?>">
                                        <button type="submit"><i class="fas fa-trash" style="color:red;"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
