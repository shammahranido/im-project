<?php
include_once 'admin-db.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['confirm_check']) && $_POST['confirm_check'] == 'yes') {
        $pet_id = isset($_GET['pet_id']) ? htmlspecialchars($_GET['pet_id']) : '';
        header("Location: customer-frontend-reservation-adopt.php?pet_id=" . $pet_id);
        exit;
    } else {
        $message = "Please check the checkbox to proceed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Adoption Information</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-green-100">
    <nav class="bg-white shadow-md mb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center">
            <a href="index.html">
                <img src="https://i.ibb.co/t8zddsB/dvmf-logo.jpg" alt="Logo" width="50" height="auto">
            </a>
            </div>
            <div class="flex space-x-4">
            <a href="index.php" class="text-green-700 font-bold hover:text-green-900 px-4 py-2">Home</a>
            <a href="about.html" class="text-green-700 font-bold hover:text-green-900 px-4 py-2">About Us</a>
            <a href="customer-frontend-adoption.php" class="text-green-700 font-bold hover:text-green-900 px-4 py-2">Adoption</a>
            </div>
        </div>
        </div>
    </nav>
<div class="max-w-4xl mx-auto p-8">
    <h1 class="text-3xl font-bold mb-4 text-center text-green-900">Process of Adopting a Pet</h1>
    <p class="mb-6 text-lg text-center text-green-800">These angels would be glad to be part of your family. But to ensure their new home is secure, these processes must be completed.</p>

    <div class="bg-white p-6 rounded-lg shadow-md mb-6 flex items-center">
        <img src="https://i.ibb.co/VYJT4N6/1.png" alt="Interview and Screening" class="w-24 mr-8">
        <div>
            <h2 class="text-xl font-bold mb-4 text-green-900">1. Interview and Screening</h2>
            <p class="text-green-800">As part of the adoption process, you will undergo an interview and screening to ensure a suitable match between you and your new pet.</p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md mb-6 flex items-center">
        <img src="https://i.ibb.co/XyqYyNQ/2.png" alt="Adoption Agreement Form" class="w-24 mr-8">
        <div>
            <h2 class="text-xl font-bold mb-4 text-green-900">2. Fill out the Adoption Agreement Form</h2>
            <p class="text-green-800">Ensure that you come to the department on the chosen appointment date to fill out the adoption agreement form.</p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md mb-6 flex items-center">
        <img src="https://i.ibb.co/fYNV0sQ/3.png" alt="Payment for Adoption" class="w-24 mr-8 ">
        <div>
            <h2 class="text-xl font-bold mb-4 text-green-900">3. Payment for Adoption</h2>
            <p class="text-green-800">The department charges an adoption fee to help cover the cost of caring for the animals. The adoption fee typically includes vaccinations, spaying/neutering, and microchipping.</p>
            <p class="font-semibold text-green-900 mt-2">Adoption Fee: 200 PHP</p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md mb-6 flex items-center">
        <img src="https://i.ibb.co/MZdm2SM/4.png" alt="Finalize Adoption" class="w-24 mr-8">
        <div>
            <h2 class="text-xl font-bold mb-4 text-green-900">4. Finalize Adoption</h2>
            <p class="text-green-800">Once your application is approved and the adoption fee is paid, you can finalize the adoption and bring your new pet home!</p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md mb-6 flex items-center">
        <img src="https://i.ibb.co/jJDbfTh/5.png" alt="Post-Adoption Support" class="w-24 mr-8">
        <div>
            <h2 class="text-xl font-bold mb-4 text-green-900">5. Post-Adoption Support <small>(Optional)</small> </h2>
            <p class="text-green-800">Share the good news! This is done to encourage others to adopt too.</p>
        </div>
    </div>
    
    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <form method="post">
            <p class="text-green-800 mb-3">For now, the form you will be filling out will stand as an appointment form.</p>
            <input class="mr-2" type="checkbox" id="confirm_check" name="confirm_check" value="yes" required>
            <label for="confirm_check" class="mr-4 text-red-700">I have read the important notes before proceeding with filling out the form</label>
            <?php if (isset($error_message)) : ?>
                <p class="text-red-500"><?php echo $message; ?></p>
            <?php endif; ?>
            <div>
                <button type="submit" class="inline-block px-6 py-3 rounded-lg bg-green-500 text-white font-semibold mt-5 hover:bg-green-700">Proceed to Appointment Form</button>
            </div>
        </form>
            <div class="mt-5 text-xs">
                <span>Want to cancel adoption appointment? <a href="customer-frontend-cancel-adoption.php" class="text-blue-500">Click here.</a></span>
            </div>
    </div>
</div>
</body>
</html>
