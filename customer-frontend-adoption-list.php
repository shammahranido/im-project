<?php
include_once 'admin-db.php';

try {
    $database = new Database('localhost', 'postgres', 'postgres', '0205');
    $pdo = $database->getPDO();

    $statement = $pdo->prepare('SELECT PET.*
                                FROM pet 
                                LEFT JOIN adoption ON PET.pet_id = adoption.pet_id
                                WHERE adoption.adopt_id IS NULL');
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
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Pet Adoption</title>
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <a href="index.php">
                        <img src="https://i.ibb.co/t8zddsB/dvmf-logo.jpg" alt="Logo" width="50" height="auto">
                    </a>
                </div>
                <div class="flex space-x-4">
                    <a href="index.php" class="text-green font-bold hover:text-green-900 px-4 py-2">Home</a>
                    <a href="about.html" class="text-green font-bold hover:text-green-900 px-4 py-2">About Us</a>
                    <a href="customer-frontend-adoption-list.php" class="text-green font-bold hover:text-green-900 px-4 py-2">Adoption</a>
                </div>
            </div>
        </div>
    </nav>
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-3">Meet the Angels</h1>
        <div class="mt-3 mb-8 text-xs">
            <span>Want to cancel adoption appointment? <a href="customer-frontend-cancel-adoption.php" class="text-blue-500">Click here.</a></span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php foreach ($pets as $pet): ?>
                <div class="bg-white shadow-md rounded-lg p-6 flex items-center">
                    <div class="flex-shrink-0">
                        <img class="h-24 w-24 object-cover rounded-full" src="uploads/<?= htmlspecialchars($pet['pet_image']) ?>" alt="<?= htmlspecialchars($pet['pet_name']) ?>">
                    </div>
                    <div class="ml-4">
                        <h3 class="text-xl font-bold text-green-900 uppercase"><?= htmlspecialchars($pet['pet_name']) ?></h3>
                        <p class="mt-2 text-sm text-black font-bold">Gender:</p> <span class="text-green-900"><?= htmlspecialchars($pet['pet_gender']) ?></span>
                        <p class="mt-2 text-sm text-black font-bold">About the Pet:</p> <span class="text-green-900"><?= htmlspecialchars($pet['pet_info']) ?></span>
                        <br>
                        <a href="customer-frontend-reservation-adopt-notes.php?pet_id=<?= htmlspecialchars($pet['pet_id']) ?>" class="mt-6 inline-block border border-green-900 text-green-900 font-bold py-2 px-4 rounded hover:text-white hover:bg-green-900">Adopt Me</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <footer class="bg-gray py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex justify-between items-center">
              <p class="text-gray-600 text-sm">&copy; 2024 DVMF. Xiamen Street, Cebu City, Philippines.</p>
              <a href="admin-homepage.html" class="text-sm">&#x1F464; Admin</a>
          </div>
      </div>
    </footer>
</body>
</html>



