<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-green-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-lg max-w-md mx-auto">
            <h1 class="text-2xl font-bold text-gray-900 mb-4">Hey there!</h1>
            <?php if (isset($_SESSION['message'])): ?>
                <p class="text-gray-700"><?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?></p>
            <?php else: ?>
                <p class="text-gray-700">Your feedback has been submitted successfully.</p>
            <?php endif; ?>
            <a href="index.php" class="mt-6 inline-block bg-green-500 text-white font-bold py-2 px-4 rounded hover:bg-green-600 float-right">Go Back to Home</a>
        </div>
    </div>
</body>
</html>