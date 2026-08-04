<?php
include_once 'admin-db.php';
include_once 'all-process.php';

//kuwang ug delete admin account

try {
    $database = new Database('localhost', 'postgres', 'postgres', '0205');
    $pdo = $database->getPDO();

    $processAdmin = new AllProcess($pdo);

    if (isset($_GET['admin_id'])) {
        $adminId = $_GET['admin_id'];
        $processAdmin->deleteAdmin($adminId);
    }

    $statement = $pdo->prepare('SELECT * FROM admin');
    $statement->execute(); 
    $admin = $statement->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    echo "Query failed: " . $e->getMessage();
    die(); 
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.15/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

    <a href="admin-frontend-signup.php" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">+ Add Admin</a>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <div class="overflow-x-auto">
            <table class="table-auto bg-gray-900 min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">No.</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">ID Number</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($admin as $admin): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($admin['admin_id']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap uppercase"><?= htmlspecialchars($admin['ad_name']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($admin['admin_idnum']) ?></td>
                            <td>
                                <a href="list-admin.php?admin_id=<?=htmlspecialchars($admin['admin_id']) ?>" onclick="return confirm('Are you sure you want to remove admin?')"><i class="fas fa-trash" style="color:red;"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
