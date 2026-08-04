<?php 

include_once 'admin-db.php';
include_once 'all-process.php'; 

if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

$database = new Database('localhost', 'postgres', 'postgres', '0205');
$pdo = $database->getPDO();

if (isset($_GET['appoint_id'])) {
  $appoint_id = $_GET['appoint_id'];
  
  $getAppoint = new AllProcess($pdo);
  $reservation = $getAppoint->getAppointment($appoint_id);

  if ($reservation) {
      $customer_name = $reservation['customer_name'];
      $customer_contactnum = $reservation['customer_contactnum'];
      $customer_email = $reservation['customer_email'];
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
  <title>View Form</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
  <nav class="bg-green-500 p-4 mb-7">
    <div class="container mx-auto flex justify-between items-center">
        <a href="#" class="text-white font-bold"></a>
        <a href="admin-menu.php?service=list-rabies-appointment" class="text-white font-bold">X</a>
    </div>
  </nav>
  <div class="flex items-center justify-center">
    <div class="bg-blue-200 shadow-md rounded-lg p-6 w-full max-w-md">
      <h2 class="text-2xl font-semibold mb-4 text-gray-800">User Information</h2>
      <?php if (isset($_SESSION['message'])): ?>
        <div class="mt-4">
            <p class="text-red-500 mb-4"><?php echo $_SESSION['message']; ?></p>
        </div>
        <?php unset($_SESSION['message']); ?>
      <?php endif; ?>
      <div class="mb-4">
        <label class="block text-gray-600 text-sm font-medium mb-1">Name</label>
        <input type="text" name="customer_name" value="<?php echo htmlspecialchars($customer_name); ?>" class="w-full border border-gray-300 rounded-lg p-2 bg-gray-100 text-gray-700" readonly>
      </div>
      <div class="mb-4">
        <label class="block text-gray-600 text-sm font-medium mb-1">Contact Number</label>
        <input type="text" name="customer_contactnum" value="<?php echo htmlspecialchars($customer_contactnum); ?>" class="w-full border border-gray-300 rounded-lg p-2 bg-gray-100 text-gray-700" readonly>
      </div>
      <div class="mb-4">
        <label class="block text-gray-600 text-sm font-medium mb-1">Email</label>
        <input type="email" name="customer_email" value="<?php echo htmlspecialchars($customer_email); ?>" class="w-full border border-gray-300 rounded-lg p-2 bg-gray-100 text-gray-700" readonly>
      </div>
      <div class="mb-4">
        <label class="block text-gray-600 text-sm font-medium mb-1">Status</label>
        <input type="text" name="customer_status" value="<?php echo htmlspecialchars($appoint_status); ?>" class="w-full border border-gray-300 rounded-lg p-2 bg-gray-100 text-gray-700" readonly>
      </div>
    </div>
  </div>
</body>
</html>
