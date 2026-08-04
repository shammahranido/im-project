<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cant Delete in progress</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-red-500 flex items-center justify-center h-screen">
    <div class="max-w-md w-full bg-white shadow-lg rounded-lg overflow-hidden mx-auto">
        <div class="py-4 px-6 bg-red-900">
            <h1 class="text-3xl font-bold text-white">Unsuccessful!</h1>
        </div>
        <div class="p-6 text-center">
            <p class="text-lg text-gray-700">In Progress Appointment can't be deleted.</p>
            <p class="mt-6">
                <a href="admin-menu.php?service=list-rabies-appointment" class="text-red-500 font-bold hover:text-red-700">&lt; Back to List</a>
            </p>
        </div>
    </div>
</body>
</html>