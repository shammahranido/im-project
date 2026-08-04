<?php 


include_once 'admin-db.php';

session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin-login.php');
    exit;
}

$admin_id = $_SESSION['admin_id'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .dropdown:hover .dropdown-menu {
            display: block;
        }
    </style>
</head>
<body class="bg-gray-100">
    <nav class="bg-green-500 p-4 w-full">
        <div class="container mx-auto flex justify-between items-center">
            <a href="#" class="text-white font-bold">ADMIN</a>
            <div class="flex space-x-4">
                <a href="feedback-view.php" class="text-white font-bold mr-8">See Feedbacks</a>
                <a href="admin-frontend-login.php" class="text-white font-bold">LogOut</a>
            </div>
        </div>
    </nav>
    <input type="hidden" name="admin_id" value="<?php echo htmlspecialchars($admin_id); ?>"/>
    <div class="container mx-auto p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">           
            <div class="bg-white p-6 rounded shadow-md hover:shadow-lg transition-shadow duration-300 dropdown">
                <h2 class="text-xl font-bold mb-4 text-blue-600">Appointments</h2>
                <p>View and manage services offered.</p>
                <ul class="dropdown-menu hidden bg-white p-4 rounded shadow-lg mt-2">
                    <li class="py-2">
                        <a href="?service=list-adoption-appointment" class="text-yellow-600 font-bold hover:underline">Adoption Appointments</a>
                    </li>
                    <li class="py-2">
                        <a href="?service=list-ligate-appointment" class="text-pink-600 font-bold hover:underline">Ligate Appointments</a>
                    </li>
                    <li class="py-2">
                        <a href="?service=list-rabies-appointment" class="text-blue-600 font-bold hover:underline">Rabies Vaccination Appointments</a>
                    </li>
                </ul>
            </div>      
            <a href="?page=adoption" class="bg-white p-6 rounded shadow-md hover:shadow-lg transition-shadow duration-300">
                <h2 class="text-xl font-bold mb-4 text-purple-600">Update Pet for Adoption</h2>
                <p>View and manage adoption processes.</p>
            </a>      
            <a href="?page=admin" class="bg-white p-6 rounded shadow-md hover:shadow-lg transition-shadow duration-300">
                <h2 class="text-xl font-bold mb-4 text-red-600">Admin</h2>
                <p>Access administrative functions.</p>
            </a>
        </div>
        <div class="mt-6">
            <?php
            if (isset($_GET['page'])) {
                $page = $_GET['page'];
                switch ($page) {
                    case 'adoption':
                        echo '<div class="bg-white p-6 rounded shadow-md">
                        <h2 class="text-2xl font-bold mb-4">Pets for Adoption</h2>';
                        include 'list-pet-for-adoption.php';
                        echo '</div>';
                        break;
                    case 'admin':
                        echo '<div class="bg-white p-6 rounded shadow-md"><h2 class="text-2xl font-bold mb-4">Admin Panel</h2>' ;
                        include 'list-admin.php'; 
                        echo '</div>';
                        break;
                    default:
                        echo '<p class="text-red-500">Page not found.</p>';
                        break;
                }
            } elseif (isset($_GET['service'])) {
                $service = $_GET['service'];
                $serviceFile = strtolower($service) . '.php'; 
                if (file_exists($serviceFile)) {
                    include_once $serviceFile;
                } else {
                    echo '<p class="text-red-500">Service file not found.</p>';
                }
            } else {
                echo '<p class="text-gray-700">Welcome to the Dashboard! Click on a menu item above to get started.</p>';
            }
            ?>
        </div>
    </div>
</body>
</html>