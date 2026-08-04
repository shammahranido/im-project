<?php
include_once 'admin-db.php';
include_once 'admin-auth.php';
include_once 'all-process.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin-login.php');
    exit;
}

$admin_id = $_SESSION['admin_id'];

try {
    $database = new Database('localhost', 'postgres', 'postgres', '0205');
    $pdo = $database->getPDO();

    $processPet = new AllProcess($pdo);

    if (isset($_GET['pet_id'])) {
        $petId = $_GET['pet_id'];
        $deleted = $processPet->deletePet($petId);

        if ($deleted) {
            header('Location: admin-menu.php?page=adoption');
            exit();
        } else {
            echo "Pet deletion failed";
            exit();
        }
    }

    $statement = $pdo->prepare('SELECT * FROM pet');
    $statement->execute(); 
    $pets = $statement->fetchAll(PDO::FETCH_ASSOC);
    
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
</head>
<a href="admin-frontend-pet-create.php" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">+ Add Pet</a>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <div class="overflow-x-auto">
            <table class="table-auto bg-purple-300 min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Pet ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Gender</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Information</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Image</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Settings</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($pets as $pets): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($pets['pet_id']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap uppercase"><?= htmlspecialchars($pets['pet_name']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($pets['pet_type']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($pets['pet_gender']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($pets['pet_info']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <img src="uploads/<?= htmlspecialchars($pets['pet_image']) ?>" alt="<?= htmlspecialchars($pets['pet_name']) ?>" class="h-16 w-16 object-cover">
                            </td>
                            <td class="pl-8">
                                <a href="admin-frontend-pet-edit.php?pet_id=<?=htmlspecialchars($pets['pet_id']) ?>"><i class="fas fa-edit" style="color:green; margin-right:15px;"></i></a>
                                <a href="list-pet-for-adoption.php?pet_id=<?=htmlspecialchars($pets['pet_id']) ?>" onclick="return confirm('Are you sure you want to remove pet?')"><i class="fas fa-trash" style="color:red;"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
