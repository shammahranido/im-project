<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include_once 'admin-db.php';
include_once 'all-process.php';

$database = new Database('localhost', 'postgres', 'postgres', '0205');
$pdo = $database->getPDO();

$pet_id = isset($_GET['pet_id']) ? htmlspecialchars($_GET['pet_id']) : null;

if (!$pet_id) {
    header('Location: customer-frontend-reservation-adopt.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['name'], $_POST['email'], $_POST['contactnum'], $_POST['appointment_date'], $_POST['appointment_time'], $_POST['pet_id'], $_POST['ques_1'], $_POST['ques_2'], $_POST['ques_3'])) {
        $name = htmlspecialchars($_POST['name']);
        $email = htmlspecialchars($_POST['email']);
        $contactnum = htmlspecialchars($_POST['contactnum']);
        $adopt_date = htmlspecialchars($_POST['appointment_date']);
        $adopt_time = htmlspecialchars($_POST['appointment_time']);
        $pet_id = htmlspecialchars($_POST['pet_id']);
        $ques_one = htmlspecialchars($_POST['ques_1']);
        $ques_two = htmlspecialchars($_POST['ques_2']);
        $ques_three = htmlspecialchars($_POST['ques_3']);
        $check_input = true;

        for ($i = 0; $i < strlen($name); $i++) {
            $char = $name[$i];
            if (!((ord($char) >= 65 && ord($char) <= 90) || (ord($char) >= 97 && ord($char) <= 122) || ord($char) == 32)) {
                $_SESSION['name_message'] = "Invalid name format. Name should contain only letters.";
                $check_input = false;
                $_SESSION['form_data'] = [
                    'name' => $name,
                    'email' => $email,
                    'contactnum' => $contactnum,
                    'adopt_date' => $adopt_date,
                    'adopt_time' => $adopt_time,
                    'pet_id' => $pet_id,
                    'ques_1' => $ques_one,
                    'ques_2' => $ques_two,
                    'ques_3' => $ques_two,
                ];
                break;
            }
        }

        if ($check_input) {
            if (strlen($contactnum) != 11 || !is_numeric($contactnum)) {
                $_SESSION['contact_message'] = "Invalid contact number format. Contact number should be exactly 11 digits.";
                header('Location: customer-frontend-reservation-adopt.php?pet_id=' . urlencode($_POST['pet_id']));
                $_SESSION['form_data'] = [
                    'name' => $name,
                    'email' => $email,
                    'contactnum' => $contactnum,
                    'adopt_date' => $adopt_date,
                    'adopt_time' => $adopt_time,
                    'pet_id' => $pet_id,
                    'ques_1' => $ques_one,
                    'ques_2' => $ques_two,
                    'ques_two' => $ques_two,
                ];
                exit(0);
            }

            $processAppoint = new AllProcess($pdo);
            $customer_id = $processAppoint->insertCustomer($name, $email, $contactnum);
            
            if (!$customer_id) {
                $_SESSION['message'] = "Error... Failed to insert customer.";
                header('Location: customer-frontend-reservation-adopt.php?pet_id=' . urlencode($_POST['pet_id']));
                $_SESSION['form_data'] = [
                    'name' => $name,
                    'email' => $email,
                    'contactnum' => $contactnum,
                    'adopt_date' => $adopt_date,
                    'adopt_time' => $adopt_time,
                    'pet_id' => $pet_id,
                    'ques_1' => $ques_one,
                    'ques_2' => $ques_two,
                    'ques_3' => $ques_three,
                ];
                exit(0);
            }

            $isAdoptionInserted = $processAppoint->insertAdoption($customer_id, $pet_id, $adopt_date, $adopt_time, $ques_one, $ques_two, $ques_three);
            if ($isAdoptionInserted) {
                unset($_SESSION['form_data']);
                header('Location: success.php');
                exit(0);
            } else {
                $delete_query = 'DELETE FROM CUSTOMER WHERE customer_id = :customer_id';
                $statement = $pdo->prepare($delete_query);
                $statement->execute([':customer_id' => $customer_id]);
                $_SESSION['message'] = "Failed to book appointment.";
                
                $_SESSION['form_data'] = [
                    'name' => $name,
                    'email' => $email,
                    'contactnum' => $contactnum,
                    'adopt_date' => $adopt_date,
                    'adopt_time' => $adopt_time,
                    'pet_id' => $pet_id,
                    'ques_1' => $ques_one,
                    'ques_2' => $ques_two,
                    'ques_3' => $ques_three,
                ];
            }
        }
        
        header('Location: customer-frontend-reservation-adopt.php?pet_id=' . urlencode($pet_id));
        exit(0);
     
    } else {
        $_SESSION['message'] = "Please fill all required fields.";
        header('Location: customer-frontend-reservation-adopt.php?pet_id=' . urlencode($_POST['pet_id']));
        exit(0);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Adoption Form</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
</head>
<body>
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
    <div class="container mx-auto mt-5 px-4 max-w-md">
        <div class="bg-white shadow-md rounded-lg p-8">
            <h1 class="text-3xl font-bold mb-5">Appointment Form</h1>
            <form action="customer-frontend-reservation-adopt.php?pet_id=<?= urlencode($pet_id) ?>" method="POST">
                <input type="hidden" name="pet_id" value="<?= htmlspecialchars($pet_id) ?>">
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2" for="name">Full Name</label>
                    <input
                        class="w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring focus:ring-indigo-200"
                        type="text" id="name" name="name" value="<?php echo htmlspecialchars($_SESSION['form_data']['name'] ?? ''); ?>"  required>
                    <?php if (isset($_SESSION['name_message'])): ?>
                        <div class="mt-4 text-xs">
                            <p class="text-red-500"><?php echo $_SESSION['name_message']; ?></p>
                        </div>
                        <?php unset($_SESSION['name_message']); ?>
                    <?php endif; ?>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2" for="email">Email</label>
                    <input
                        class="w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring focus:ring-indigo-200"
                        type="email" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['form_data']['email'] ?? ''); ?>"  required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2" for="contactnum">Contact Number</label>
                    <input class="w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring focus:ring-indigo-200" type="tel" id="contactnum" name="contactnum" value="<?php echo htmlspecialchars($_SESSION['form_data']['contactnum'] ?? ''); ?>"  required>
                    <?php if (isset($_SESSION['contact_message'])): ?>
                        <div class="mt-4 text-xs">
                            <p class="text-red-500"><?php echo $_SESSION['contact_message']; ?></p>
                        </div>
                        <?php unset($_SESSION['contact_message']); ?>
                    <?php endif; ?>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2" for="appointment_date">Appointment Date</label>
                    <input class="w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring focus:ring-indigo-200" type="date" id="appointment_date" name="appointment_date" value="<?php echo htmlspecialchars($_SESSION['form_data']['adopt_date'] ?? ''); ?>" required>
                    <?php if (isset($_SESSION['date_message'])): ?>
                        <div class="mt-4 text-xs">
                            <p class="text-red-500"><?php echo $_SESSION['date_message']; ?></p>
                        </div>
                        <?php unset($_SESSION['date_message']); ?>
                    <?php endif; ?>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2" for="appointment_time">Appointment Time</label>
                    <input class="w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring focus:ring-indigo-200" type="time" id="appointment_time" name="appointment_time" value="<?php echo htmlspecialchars($_SESSION['form_data']['adopt_time'] ?? ''); ?>" required>
                    <?php if (isset($_SESSION['time_message'])): ?>
                        <div class="mt-4 text-xs">
                            <p class="text-red-500"><?php echo $_SESSION['time_message']; ?></p>
                        </div>
                        <?php unset($_SESSION['time_message']); ?>
                    <?php endif; ?>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Are you ready to provide a loving home for a pet?</label>
                    <label class="inline-flex items-center">
                        <input type="radio" class="form-radio" name="ques_1" value="yes" <?php echo ($_SESSION['form_data']['ques_1'] ?? '') === 'yes' ? 'checked' : ''; ?> required>
                        <span class="ml-2">Yes</span>
                    </label>
                    <label class="inline-flex items-center ml-6">
                        <input type="radio" class="form-radio" name="ques_1" value="no" <?php echo ($_SESSION['form_data']['ques_1'] ?? '') === 'no' ? 'checked' : ''; ?> required>
                        <span class="ml-2">No</span>
                    </label>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Do you have experience with pet care?</label>
                    <label class="inline-flex items-center">
                        <input type="radio" class="form-radio" name="ques_2" value="yes" <?php echo ($_SESSION['form_data']['ques_2'] ?? '') === 'yes' ? 'checked' : ''; ?> required>
                        <span class="ml-2">Yes</span>
                    </label>
                    <label class="inline-flex items-center ml-6">
                        <input type="radio" class="form-radio" name="ques_2" value="no" <?php echo ($_SESSION['form_data']['ques_2'] ?? '') === 'no' ? 'checked' : ''; ?> required>
                        <span class="ml-2">No</span>
                    </label>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Are you committed to providing veterinary care for your pet?</label>
                    <label class="inline-flex items-center">
                        <input type="radio" class="form-radio" name="ques_3" value="yes" <?php echo ($_SESSION['form_data']['ques_3'] ?? '') === 'yes' ? 'checked' : ''; ?> required>
                        <span class="ml-2">Yes</span>
                    </label>
                    <label class="inline-flex items-center ml-6">
                        <input type="radio" class="form-radio" name="ques_3" value="no" <?php echo ($_SESSION['form_data']['ques_3'] ?? '') === 'no' ? 'checked' : ''; ?> required>
                        <span class="ml-2">No</span>
                    </label>
                </div>  
                <div class="mb-4">
                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Submit</button>
                </div>
            </form>
            <?php if (isset($_SESSION['message'])): ?>
                <div class="mt-4 text-xs">
                    <p class="text-red-500"><?php echo $_SESSION['message']; ?></p>
                </div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
