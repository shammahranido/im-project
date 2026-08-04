<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include_once 'admin-db.php';
include 'all-process.php';

$database = new Database('localhost', 'postgres', 'postgres', '0205');
$pdo = $database->getPDO();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['info']) && isset($_POST['pet_id'])) {
        $pet_id = $_POST['pet_id'];
        $info = $_POST['info'];

        $processPet = new AllProcess($pdo);
        $success = $processPet->editPet($pet_id, $info);

        if ($success) {
            $_SESSION['message'] = "Pet information updated successfully.";
        } else {
            $_SESSION['message'] = "Error updating pet information.";
        }

        header('Location: admin-frontend-pet-edit.php?pet_id=' . $pet_id);
        exit();
    } else {
        $_SESSION['message'] = "Please fill all required fields.";
    }
}

if (isset($_GET['pet_id'])) {   
    $pet_id = $_GET['pet_id'];

    $getPets = new AllProcess($pdo);
    $pet = $getPets->getPet($pet_id);

    if ($pet) {
        $name = $pet['pet_name'];
        $type = $pet['pet_type'];
        $gender = $pet['pet_gender'];
        $photo = $pet['pet_image'];
        $info = $pet['pet_info'];
    } else {
        $_SESSION['message'] = "Pet not found.";
    }
} else {
    $_SESSION['message'] = "No pet ID provided.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pet Information</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.1.2/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <nav class="bg-green-500 p-4">
        <div class="container mx-auto flex justify-between items-center">
            <a href="#"></a>
            <a href="admin-menu.php?page=adoption" class="text-white font-bold">X</a>
        </div>
    </nav>
    <div class="container mx-auto mt-10 flex justify-center">
        <div class="flex flex-row space-x-10">
            <div class="w-1/3">
                <img src="uploads/<?php echo htmlspecialchars($photo); ?>" alt="<?php echo htmlspecialchars($name); ?>" class="w-full h-auto object-cover">
            </div>
            <div class="max-w-md bg-white rounded-md p-8 shadow-md w-2/3">
                <h2 class="text-2xl font-bold mb-4">Edit Pet Information</h2>

                <form action="admin-frontend-pet-edit.php?pet_id=<?php echo htmlspecialchars($pet_id); ?>" method="POST">
                    <input type="hidden" name="pet_id" value="<?php echo htmlspecialchars($pet_id); ?>">
                    <div class="mb-4">
                        <label for="name" class="block text-gray-700 font-bold mb-2 text-xs">Name:</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" class="form-input border w-full py-1" disabled>
                    </div>
                    <div class="mb-4">
                        <label for="type" class="block text-gray-700 font-bold mb-2 text-xs">Type:</label>
                        <select id="type" name="type" class="form-select border w-full py-1" disabled>
                            <option value="<?php echo htmlspecialchars($type); ?>"><?php echo htmlspecialchars($type); ?></option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="gender" class="block text-gray-700 font-bold mb-2 text-xs">Gender:</label>
                        <select id="gender" name="gender" class="form-select border w-full py-1" disabled>
                            <option value="<?php echo htmlspecialchars($gender); ?>"><?php echo htmlspecialchars($gender); ?></option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2 text-xs">About the Pet:</label>
                        <textarea id="info" name="info" class="form-input border w-full py-1"><?php echo htmlspecialchars($info); ?></textarea>
                    </div>
                    <div>
                        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Save Changes</button>
                    </div>
                </form>
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="mt-4">
                        <p class="text-red-500"><?php echo $_SESSION['message']; ?></p>
                    </div>
                    <?php unset($_SESSION['message']); ?>
                <?php endif; ?>
                
            </div>
        </div>
    </div>
</body>
</html>
