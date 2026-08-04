<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Rabies Reservation Form</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.2/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
  <nav class="bg-white shadow-md w-full mb-5">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        <div class="flex items-center">
          <a href="index.php">
            <img src="https://i.ibb.co/t8zddsB/dvmf-logo.jpg" alt="Logo" width="50" height="auto">
          </a>
        </div>
        <div class="flex space-x-4">
          <a href="index.php" class="text-green-900 font-bold hover:text-green-700 px-4 py-2">Home</a>
          <a href="about.html" class="text-green-900 font-bold hover:text-green-700 px-4 py-2">About Us</a>
          <a href="customer-frontend-adoption-list.php" class="text-green-900 font-bold hover:text-green-700 px-4 py-2">Adoption</a>
        </div>
      </div>
    </div>
  </nav>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col md:flex-row bg-white p-8 rounded shadow-md">
      <div class="md:w-1/2 p-4">
        <h2 class="text-2xl font-bold mb-6 text-gray-900">About Vaccination</h2>
        <p class="font-bold mb-2">Types of Vaccination Registration</p>
        <p class="text-xs font-bold text-gray-700 mb-2">1. Annual Registration</p>
        <p class="text-xs mb-2">&emsp; Amount Fee: Php 200.00</p>
        <p class="text-xs font-bold text-gray-700 mb-2">2. Perpetual Registration</p>
        <p class="text-xs mb-4">&emsp; Amount Fee: Php 600.00</p>
        <p class="font-bold mb-2">Other services</p>
        <p class="text-xs font-bold text-gray-700 mb-2">Vaccination Card or Certificate Reissue</p>
        <p class="text-xs mb-2">&emsp; Amount Fee: Php 100.00</p>
        <p class="text-xs font-bold text-gray-700 mb-2">Veterinary Health Certificate </p>
        <p class="text-xs mb-2">&emsp; Amount Fee: Php 50.00 per animal head</p>
        <br>
        <p class="mt-4 font-bold text-red-500">The rest of the information will be discussed during the appointment. To schedule an appointment, owners should fill out the form.</p>
        <p class="text-xs text-gray-500 mt-2">Vaccination will be done on the same day as the appointment.</p>
      </div>
      <div class="md:w-1/2 p-4">
        <h2 class="text-2xl font-bold mb-6 text-gray-900 text-center">Pet Reservation Form</h2>
        <div class="message"></div>
        <form>
          <div class="mb-4">
            <label for="name" class="block text-gray-700">Owner Name</label>
            <input type="text" id="name" name="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2" required>
          </div>
          <div class="mb-4">
            <label for="email" class="block text-gray-700">Email Address</label>
            <input type="email" id="email" name="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2" required>
          </div>
          <div class="mb-4">
            <label for="contactnum" class="block text-gray-700">Contact Number</label>
            <input type="tel" id="contactnum" name="contactnum" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2" required>
          </div>
          <div class="mb-4 flex">
            <div class="mr-4">
              <label for="num_dogs" class="block text-gray-700">Number of Dogs</label>
              <input type="number" id="num_dogs" name="num_dogs" class="mt-1 block w-24 border-gray-300 rounded-md shadow-sm p-2" min="0" value="0" required>
              <small class="text-gray-500 italic">Leave 0 if not applicable</small>
            </div>
            <div class="mr-4">
              <label for="num_cats" class="block text-gray-700">Number of Cats</label>
              <input type="number" id="num_cats" name="num_cats" class="mt-1 block w-24 border-gray-300 rounded-md shadow-sm p-2" min="0" value="0" required>
            </div>
            <div>
              <label for="appointment_time" class="block text-gray-700">Appointment Time</label>
              <input type="time" id="appointment_time" name="appointment_time" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2" required>
            </div>
          </div>
          <div class="mb-4">
            <label for="appointment_date" class="block text-gray-700">Appointment Date</label>
            <input type="date" id="appointment_date" name="appointment_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2" required>
          </div>
          <div class="flex items-center justify-center">
            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Submit Reservation</button>
          </div>
        </form>
        <div class="mt-3 mb-8 text-xs">
            <span>Want to cancel vaccination appointment? <a href="customer-frontend-cancel-rabies.php" class="text-blue-500">Click here.</a></span>
        </div>
      </div>
    </div>
  </div>

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

      fetch("process-reservation-rabies.php", {
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
            messageDiv.classList.remove("bg-red-500");
            messageDiv.classList.add("bg-green-500");
            messageDiv.innerHTML = "Reservation successfully submitted!";
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