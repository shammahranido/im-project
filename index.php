<?php
include_once 'admin-db.php';

try {
    $database = new Database('localhost', 'postgres', 'postgres', '0205');
    $pdo = $database->getPDO();

    $statement = $pdo->prepare('SELECT * FROM service');
    $statement->execute(); 
    $services = $statement->fetchAll(PDO::FETCH_ASSOC);
    
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
    <title>DVMF</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.2/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-green-100">

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

<div class="bg-cover bg-center h-96" style="background-image: url('https://i.ibb.co/xhBcM4k/bg-homepage.jpg');">
    <div class="flex items-center justify-center h-full bg-gray-900 bg-opacity-50">
        <div class="text-center">
            <h1 class="text-4xl font-bold text-white">Department of Veterinary Medicine and Fisheries</h1>
            <p class="text-xl text-green-200 mt-4">The only City that provides effective and sustainable veterinary health services <br> which guarantee quality, safe fishery and food animal meat products.</p>
            <a href="about.html" class="mt-6 inline-block bg-green-500 text-white font-bold py-2 px-4 rounded hover:bg-green-600">About</a>
        </div>
    </div>
</div>
<section class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl font-bold text-green-900">Our Services</h2>
            <p class="text-xl text-green-700 mt-4">We offer a wide range of services for your pets</p>
        </div>
        <div class="mt-10 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php foreach ($services as $service): ?>
                <div class="bg-white shadow-md rounded-lg p-6 text-center">
                    <h3 class="text-xl font-bold text-green-900"><?= htmlspecialchars($service['service_name']) ?></h3>
                    <p class="mt-2 text-green-700"><?= htmlspecialchars($service['service_description']) ?></p>
                    <?php if ($service['with_reservation']): ?>
                        <?php if ($service['service_id'] == 1): ?>
                            <a href="customer-frontend-reservation-rabies.php?service_id=<?= htmlspecialchars($service['service_id']) ?>" class="mt-6 inline-block bg-green-900 text-white font-bold py-2 px-4 rounded hover:bg-green-600">Make Reservation</a>
                        <?php elseif ($service['service_id'] == 2): ?>
                            <a href="customer-frontend-reservation-ligate.php" class="mt-6 inline-block bg-green-900 text-white font-bold py-2 px-4 rounded hover:bg-green-600">Make Reservation</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="fees.html" class="mt-6 inline-block bg-green-500 text-white font-bold py-2 px-4 rounded hover:bg-green-600">Walk-in Only</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<section class="bg-green-100 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl font-bold text-gray-900">Send us your Feedback</h2>
            <p class="text-xl text-gray-600 mt-4">We would love to hear from you</p>
        </div>
        <div class="mt-10 max-w-md mx-auto">
            <form action="process-feedback.php" method="POST" class="bg-white shadow-md rounded-lg p-6">
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 font-bold mb-2">Name</label>
                    <input type="text" id="name" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:border-green-500">
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 font-bold mb-2">Email</label>
                    <input type="email" id="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:border-green-500">
                </div>
                <div class="mb-4">
                    <label for="message" class="block text-gray-700 font-bold mb-2">Message</label>
                    <textarea id="message" name="message" class="w-full px-3 py-6 border border-gray-300 rounded-md focus:outline-none focus:border-green-500"></textarea>
                </div>
                <button type="submit" class="w-full bg-green-500 text-white font-bold py-2 px-4 rounded hover:bg-green-600">Send Message</button>
            </form>
        </div>
    </div>
</section>

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