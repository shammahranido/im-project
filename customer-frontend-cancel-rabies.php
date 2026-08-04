<?php
session_start();
include_once 'admin-db.php';
include_once 'all-process.php';

$database = new Database('localhost', 'postgres', 'postgres', '0205');
$pdo = $database->getPDO();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['name'], $_POST['email'], $_POST['contact'])) {
        $name = strtoupper($_POST['name']);
        $email = $_POST['email'];
        $contact = $_POST['contact'];

        $customerQuery = 'SELECT c.customer_id 
                          FROM customer c
                          INNER JOIN appointment a ON c.customer_id = a.customer_id
                          WHERE UPPER(c.customer_name) = :cname 
                          AND c.customer_email = :email 
                          AND c.customer_contactnum = :contact
                          AND a.service_id = 1'; 

        $customerStmt = $pdo->prepare($customerQuery);
        $customerStmt->execute([
            ':cname' => $name,
            ':email' => $email,
            ':contact' => $contact
        ]);

        $customer = $customerStmt->fetch(PDO::FETCH_ASSOC);

        if ($customer) {
            $customerId = $customer['customer_id'];

            $appointQuery = 'SELECT appoint_id 
                             FROM appointment 
                             WHERE customer_id = :customer_id 
                             AND service_id = 1
                             AND appoint_status = \'In Progress\'';

            $appointStmt = $pdo->prepare($appointQuery);
            $appointStmt->execute([':customer_id' => $customerId]);

            $appointment = $appointStmt->fetch(PDO::FETCH_ASSOC);

            if ($appointment) {
                $appointId = $appointment['appoint_id'];

                $cancelQuery = 'UPDATE appointment 
                                SET appoint_status = \'Cancelled\' 
                                WHERE appoint_id = :appoint_id';

                $cancelStmt = $pdo->prepare($cancelQuery);
                $cancelStmt->execute([':appoint_id' => $appointId]);

                if ($cancelStmt->rowCount() > 0) {
                    $_SESSION['success'] = "Appointment successfully cancelled.";
                } else {
                    $_SESSION['error'] = "Failed to cancel appointment.";
                }
            } else {
                $_SESSION['error'] = "No 'In Progress' appointments found for the customer with service_id = 1.";
            }
        } else {
            $_SESSION['error'] = "No matching customer found with an appointment for service_id = 1.";
        }
    } else {
        $_SESSION['error'] = "Please provide name, email, and contact number.";
    }
    
    header('Location: customer-frontend-cancel-rabies.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rabies Vaccination Cancellation Appointment</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-blue-200 flex items-center justify-center min-h-screen">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Rabies Vaccination Cancellation Appointment</h2>
            <a href="index.php" class="font-bold text-gray-500 hover:text-gray-800">X</a>
        </div>
        <p class="text-gray-700">We're sorry to hear that you've decided to cancel your rabies vaccination appointment. We hope to assist you in the future. Thank you for your understanding.</p>
        <p class="text-xs text-red-500 mb-5">Make sure that the name, email, and contact number matches with the one you registered.</p>
        <form action="customer-frontend-cancel-rabies.php" method="post" class="space-y-4">
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