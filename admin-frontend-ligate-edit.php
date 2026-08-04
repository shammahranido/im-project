<?php 

include_once 'admin-db.php';
include 'all-process.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$database = new Database('localhost', 'postgres', 'postgres', '0205');
$pdo = $database->getPDO();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['appoint_status']) && isset($_POST['appoint_id'])) {
        $appoint_status = $_POST['appoint_status'];
        $appoint_id = $_POST['appoint_id'];

        $processPet = new AllProcess($pdo);
        $processPet->editReservation($appoint_id, $appoint_status);
    } else {
        $_SESSION['message'] = "Please fill all required fields.";
    }
}

if (isset($_GET['appoint_id'])) {
    $appoint_id = $_GET['appoint_id'];
    
    $getAppoint = new AllProcess($pdo);
    $reservation = $getAppoint->getAppointment($appoint_id);

    if ($reservation) {
        $customer_name = $reservation['customer_name'];
        $cus_num_dog = $reservation['cus_num_dog'];
        $cus_num_cat = $reservation['cus_num_cat'];
        $appoint_status = $reservation['appoint_status'];
    } else {
        $_SESSION['message'] = "Appointment not found.";
    }
} else {
    $_SESSION['message'] = "No appointment ID provided.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Ligate Appointment</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <nav class="bg-green-500 p-4 mb-5">
        <div class="container mx-auto flex justify-between items-center">
            <a href="#"></a>
            <a href="admin-menu.php?service=list-ligate-appointment" class="text-white font-bold">X</a>
        </div>
    </nav>
    <div class="container max-w-md mx-auto p-6 bg-pink-200 rounded-lg shadow-lg">
        <h1 class="text-2xl font-bold mb-4">Edit Ligate Appointment</h1>
            <?php if (isset($_SESSION['message'])): ?>
                <div class="mt-4">
                    <p class="text-red-500 mb-4"><?php echo $_SESSION['message']; ?></p>
                </div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>
        <form action="admin-frontend-ligate-edit.php?appoint_id=<?php echo htmlspecialchars($appoint_id); ?>" method="POST" class="bg-white p-6 rounded shadow-md">
            <input type="hidden" name="appoint_id" value="<?= htmlspecialchars($appoint_id) ?>">
            <div class="mb-4">
                <label for="customer_name" class="block text-gray-700">Customer Name</label>
                <input type="text" id="customer_name" name="customer_name" value="<?php echo htmlspecialchars($customer_name); ?>" class="mt-1 p-2 w-full border border-gray-300 rounded" disabled>
            </div>
            <div class="mb-4">
                <label for="cus_num_dog" class="block text-gray-700">Number of Dogs</label>
                <input type="number" id="cus_num_dog" name="cus_num_dog" value="<?php echo htmlspecialchars($cus_num_dog); ?>" class="mt-1 p-2 w-full border border-gray-300 rounded" disabled>
            </div>
            <div class="mb-4">
                <label for="cus_num_cat" class="block text-gray-700">Number of Cats</label>
                <input type="number" id="cus_num_cat" name="cus_num_cat" value="<?php echo htmlspecialchars($cus_num_cat); ?>" class="mt-1 p-2 w-full border border-gray-300 rounded" disabled>
            </div>
            <div class="mb-4">
                <label for="appoint_status" class="block text-gray-700">Status</label>
                <select id="appoint_status" name="appoint_status" class="mt-1 p-2 w-full border border-gray-300 rounded">
                    <option value="In Progress" <?= $appoint_status == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                    <option value="Completed" <?= $appoint_status == 'Completed' ? 'selected' : '' ?>>Completed</option>
                    <option value="Cancelled" <?= $appoint_status == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>
            <div>
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Save Changes</button>
            </div>
        </form>
    </div>
</body>
</html>
