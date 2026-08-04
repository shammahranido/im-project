<?php
session_start(); 

include_once 'admin-db.php';
include_once 'all-process.php';

$database = new Database('localhost', 'postgres', 'postgres', '0205');
$pdo = $database->getPDO();

if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
    if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['contact'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $contactnum = $_POST['contact'];

        $name = strtoupper($name);

        $customerQuery = 'SELECT customer_id 
                          FROM customer 
                          WHERE customer_name = :name 
                          AND customer_email = :email 
                          AND customer_contactnum = :contactnum';

        $customerStmt = $pdo->prepare($customerQuery);
        $customerStmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':contactnum' => $contactnum
        ]);

        $customer = $customerStmt->fetch(PDO::FETCH_ASSOC);

        if ($customer) {
            $customerId = $customer['customer_id'];

            $deleteQuery = 'DELETE FROM adoption 
                            WHERE customer_id = :customer_id';

            $deleteStmt = $pdo->prepare($deleteQuery);
            $deleteStmt->execute([':customer_id' => $customerId]);

            $_SESSION['success'] = "Successfully cancelled the appointment.";
            unset($_SESSION['form_data']);
        } else {
            $_SESSION['form_data'] = [
                'name' => $name,
                'email' => $email,
                'contact' => $contactnum,
            ];
            $_SESSION['error'] = "No matching customer found. Unable to cancel appointment.";
        }
    } else {
        $_SESSION['error'] = "Please provide name, email, and contact number.";
    }
    header('Location: customer-frontend-cancel-adoption.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancellation Form</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-yellow-200 flex items-center justify-center min-h-screen">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Cancellation Form</h2>
            <a href="customer-frontend-adoption-list.php" class="font-bold text-gray-500 hover:text-gray-800">X</a>
        </div>
        <p class="text-gray-700">We're sorry to hear that you've decided to cancel the adoption. We value your interest and hope to hear from you again in the future. Please fill out the form below to cancel your appointment.</p>
        <p class="text-xs text-red-500 mb-5">Make sure that the name, email, and contact number matches with the one you registered.</p>
        <form action="customer-frontend-cancel-adoption.php" method="post" class="space-y-4">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" id="name" name="name" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                value="<?php echo isset($_SESSION['form_data']['name']) ? htmlspecialchars($_SESSION['form_data']['name']) : ''; ?>">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                value="<?php echo isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : ''; ?>">
            </div>
            <div>
                <label for="contact" class="block text-sm font-medium text-gray-700">Contact Number</label>
                <input type="tel" id="contact" name="contact" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                value="<?php echo isset($_SESSION['form_data']['contact']) ? htmlspecialchars($_SESSION['form_data']['contact']) : ''; ?>">
            </div>
            <div>
                <button type="submit" class="w-full bg-red-600 text-white py-2 px-4 rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Cancel my appointment
                </button>
            </div>
        </form>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="mt-4 text-xs mb-4">
                <p class="text-blue-500 font-bold"><?php echo $_SESSION['success']; ?></p>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="mt-4 text-xs mb-4">
                <p class="text-red-500"><?php echo $_SESSION['error']; ?></p>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
