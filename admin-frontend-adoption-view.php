<?php 
include_once 'admin-db.php';
include_once 'all-process.php'; 

if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

$database = new Database('localhost', 'postgres', 'postgres', '0205');
$pdo = $database->getPDO();

if (isset($_GET['adopt_id'])) {
  $adopt_id = $_GET['adopt_id'];
  
  $getAppoint = new AllProcess($pdo);
  $adoption = $getAppoint->getAdoption($adopt_id);

  if ($adoption) {
      $customer_name = $adoption['customer_name'];
      $customer_contactnum = $adoption['customer_contactnum'];
      $customer_email = $adoption['customer_email'];
      $ques_one = $adoption['ques_one'];
      $ques_two = $adoption['ques_two'];
      $ques_three = $adoption['ques_three'];
  } else {
      $_SESSION['message'] = "Appointment not found.";
      header('Location: admin-menu.php?service=list-adoption-appointment');
      exit();
  }
} else {
  $_SESSION['message'] = "No appointment ID provided.";
  header('Location: admin-menu.php?service=list-adoption-appointment');
  exit();
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
        <a href="admin-menu.php?service=list-adoption-appointment" class="text-white font-bold">X</a>
    </div>
  </nav>
  <div class="flex items-center justify-center">
    <div class="bg-white shadow-md rounded-lg p-6 w-full max-w-md">
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
      <div class="mb-4 bg-green-100 p-5">
        <div class="mb-1">
          <label class="block text-gray-600 text-sm font-medium mb-4">User's Answers: </label>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Are you ready to provide a loving home for a pet?</label>
            <div class="flex items-center">
                <label class="inline-flex items-center text-red-500">
                    <input type="radio" class="form-radio" name="ques_1" value="yes" <?php echo ($ques_one == 'yes') ? 'checked' : ''; ?> disabled>
                    <span class="ml-2">Yes</span>
                </label>
                <label class="inline-flex items-center ml-6 text-red-500">
                    <input type="radio" class="form-radio" name="ques_1" value="no" <?php echo ($ques_one == 'no') ? 'checked' : ''; ?> disabled>
                    <span class="ml-2">No</span>
                </label>
            </div>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Do you have experience with pet care?</label>
            <div class="flex items-center">
                <label class="inline-flex items-center text-red-500">
                    <input type="radio" class="form-radio" name="ques_2" value="yes" <?php echo ($ques_two == 'yes') ? 'checked' : ''; ?> disabled>
                    <span class="ml-2">Yes</span>
                </label>
                <label class="inline-flex items-center ml-6 text-red-500">
                    <input type="radio" class="form-radio" name="ques_2" value="no" <?php echo ($ques_two == 'no') ? 'checked' : ''; ?> disabled>
                    <span class="ml-2">No</span>
                </label>
            </div>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Are you committed to providing veterinary care for your pet?</label>
            <div class="flex items-center">
                <label class="inline-flex items-center text-red-500">
                    <input type="radio" class="form-radio" name="ques_3" value="yes" <?php echo ($ques_three == 'yes') ? 'checked' : ''; ?> disabled>
                    <span class="ml-2">Yes</span>
                </label>
                <label class="inline-flex items-center ml-6 text-red-500">
                    <input type="radio" class="form-radio" name="ques_3" value="no" <?php echo ($ques_three == 'no') ? 'checked' : ''; ?> disabled>
                    <span class="ml-2">No</span>
                </label>
            </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
