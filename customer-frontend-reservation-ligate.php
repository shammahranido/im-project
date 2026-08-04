<?php

// Check if today is Monday
$today = date('w'); // 0 (Sunday) - 6 (Saturday)
if ($today == 1 || $today == 3 || $today == 6) { // Monday is 1

  header('Location: ligate-reservation-limit.php');
} 

$host = 'localhost';
$dbname = 'postgres';
$username = 'postgres';
$password = '0205';

try {
  $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $query = "SELECT COUNT(*) AS count FROM appointment WHERE service_id = 2 AND appoint_status = 'In Progress'";
  $stmt = $pdo->prepare($query);
  $stmt->execute();
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($result['count'] >= 35) {
      header('Location: ligate-reservation-close.php');
      exit();
  }

} catch (PDOException $e) {
  echo "Error: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pet Shop - Ligation Reservation</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.2/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-green-100">

  <nav class="bg-white shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        <div class="flex items-center">
          <a href="index.html">
            <img src="https://i.ibb.co/t8zddsB/dvmf-logo.jpg" alt="Logo" width="50" height="auto">
          </a>
        </div>
        <div class="flex space-x-4">
          <a href="index.php" class="text-green font-bold hover:text-green-900 px-4 py-2">Home</a>
          <a href="about.html" class="text-green font-bold hover:text-green-900 px-4 py-2">About Us</a>
          <a href="customer-frontend-reservation-adopt.php" class="text-green font-bold hover:text-green-900 px-4 py-2">Adoption</a>
        </div>
      </div>
    </div>
  </nav>

  <section class="py-12 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <h2 class="text-3xl font-bold text-green-900 text-center">Pet Reservation - Ligation</h2>
      <p class="text-xl text-green-700 text-center mt-4">Reserve a slot for your pet's upcoming Ligation procedure</p>

      <div class="form-container relative bg-white p-6 rounded shadow-md mx-auto mt-8 max-w-md">
      <div class="message"></div>
        <form action="process-reservation-ligate.php" method="post">
          <div class="mb-4">
            <label for="owner_name" class="block text-gray-700">Owner Name</label>
            <input type="text" id="name" name="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
          </div>
          <div class="mb-4">
            <label for="email" class="block text-gray-700">Email Address</label>
            <input type="email" id="email" name="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
          </div>
          <div class="mb-4">
            <label for="contact_number" class="block text-gray-700">Contact Number</label>
            <input type="tel" id="contactnum" name="contactnum" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
          </div>
          

          <!-- Pet 1 -->
          <fieldset class="border border-gray-300 rounded p-4 mb-4">
            <legend class="text-xl font-bold text-gray-700">Dog</legend>
            <div class="flex flex-wrap -mx-3 mb-2">
              <div class="w-full md:w-1/2 px-3 mb-4">
                <label for="num_pets_1" class="block text-gray-700">Number of Pets</label>
                <input type="number" id="num_dogs" name="num_dogs" class="mt-1 block w-full border-black-300 rounded-md shadow-sm" min="0" value="0" >
              </div>
            </div>
          </fieldset>

          <!-- Pet 2 -->
          <fieldset class="border border-gray-300 rounded p-4 mb-4">
            <legend class="text-xl font-bold text-gray-700">Cat</legend>
            <div class="flex flex-wrap -mx-3 mb-2">
              <div class="w-full md:w-1/2 px-3 mb-4">
                <label for="num_pets_2" class="block text-gray-700">Number of Pets</label>
                <input type="number" id="num_cats" name="num_cats" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" min="0" value="0" >
              </div>
            </div>
          </fieldset>

          <div class="flex items-center justify-center">
            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
              Submit Reservation
            </button>
          </div>
        </form>
        <div class="mt-3 mb-8 text-xs">
            <span>Want to cancel vaccination appointment? <a href="customer-frontend-cancel-ligate.php" class="text-blue-500">Click here.</a></span>
        </div>
      </div>
    </div>
  </section>

</body>

</html>
<style>
    .message {
      display: none;
      padding: 10px;
      border-radius: 5px;
      margin-bottom: 10px;
    }

    .bg-green-500 {
      background-color: green;
      color: white;
    }

    .bg-red-500 {
      background-color: red;
      color: white;
    }
  </style>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    const form = document.querySelector("form");
    form.addEventListener("submit", function(event) {
      event.preventDefault();

      const formData = new FormData(form);
      const data = Object.fromEntries(formData.entries());

      fetch("process-reservation-ligate.php", {
        method: "POST",
        body: JSON.stringify(data),
        headers: {
          "Content-Type": "application/json",
        },
      })
        .then(response => response.json())
        .then(result => {
          const messageDiv = document.querySelector(".message");
          messageDiv.innerHTML = "";

          if (result.success) {
            window.location.href = "success-ligate.php";

          } else {
            messageDiv.classList.remove("bg-green-500");
            messageDiv.classList.add("bg-red-500");
            if (result.message) {
              messageDiv.innerHTML += `<p>${result.message}</p>`;
            }
          }

          messageDiv.style.display = "block";
        })
        .catch(error => {
          const messageDiv = document.querySelector(".message");
          messageDiv.innerHTML = "An error occurred. Please try again.";
          messageDiv.classList.remove("bg-green-500");
          messageDiv.classList.add("bg-red-500");
          messageDiv.style.display = "block";
        });
    });
  });
</script>
