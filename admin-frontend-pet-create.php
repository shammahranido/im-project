<?php 
include_once 'admin-db.php';
include_once 'admin-auth.php';
include 'all-process.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin-login.php');
    exit;
}

$admin_id = $_SESSION['admin_id'];

if (!isset($_SESSION['form_data'])) {
    $_SESSION['form_data'] = [];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['name'], $_POST['type'], $_POST['gender'], $_POST['info'], $_FILES['photo'], $_POST['admin_id'])) {
        $name = $_POST['name'];
        $type = $_POST['type'];
        $gender = $_POST['gender'];
        $info = $_POST['info'];
        $photo = $_FILES['photo'];
        $admin_id = $_SESSION['admin_id'];

        $database = new Database('localhost', 'postgres', 'postgres', '0205');
        $pdo = $database->getPDO();

        $processPet = new AllProcess($pdo);
        $result = $processPet->insertPet($name, $type, $photo, $info, $gender, $admin_id);

        if ($result['valid']) {
            $_SESSION['success'] = $result['message'];
            unset($_SESSION['form_data']);
        } else {
            $_SESSION['error'] = $result['general_error'];
            $_SESSION['name_error'] = $result['name_error'];
            $_SESSION['photo_error'] = $result['photo_error'];
            
            $_SESSION['form_data'] = [
                'name' => $name,
                'type' => $type,
                'gender' => $gender,
                'info' => $info,
                'photo' => $photo,
                'admin_id' => $admin_id,
            ];
        }
        header('Location: admin-frontend-pet-create.php');
        exit;
    } else {
        $_SESSION['error'] = "Please fill all required fields.";
        header('Location: admin-frontend-pet-create.php');
        exit(0);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Pet Information</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.1.2/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-white-100">
    <nav class="bg-green-500 p-4">
        <div class="container mx-auto flex justify-between items-center">
            <a href="#"></a>
            <a href="admin-menu.php?page=adoption" class="text-white font-bold">X</a>
        </div>
    </nav>
    <div class="container mx-auto mt-3 flex justify-center">
        <div class="flex">
            <div class="max-w-md bg-white rounded-md p-8 shadow-md">
                <h2 class="text-2xl font-bold mb-4">Add Pet Information</h2>
                <form action="admin-frontend-pet-create.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-4">
                        <input type="hidden" id="admin_id" name="admin_id" value="<?php echo htmlspecialchars($admin_id); ?>" readonly>
                    </div>
                    <div class="mb-4">
                        <label for="name" class="block text-gray-700 font-bold mb-2 text-xs">Name:</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($_SESSION['form_data']['name'] ?? ''); ?>" class="form-input border w-full py-1" required>
                        <?php if (isset($_SESSION['name_error'])): ?>
                            <div class="mt-4 text-xs">
                                <p class="text-red-500"><?php echo $_SESSION['name_error']; ?></p>
                            </div>
                            <?php unset($_SESSION['name_error']); ?>
                        <?php endif; ?>
                    </div>
                    <div class="mb-4">
                        <label for="type" class="block text-gray-700 font-bold mb-2 text-xs">Type:</label>
                        <select id="type" name="type" class="form-select border w-full py-1" required>
                            <option value="">Select Type</option>
                            <option value="Dog" <?php echo (isset($_SESSION['form_data']['type']) && $_SESSION['form_data']['type'] == 'Dog') ? 'selected' : ''; ?>>Dog</option>
                            <option value="Cat" <?php echo (isset($_SESSION['form_data']['type']) && $_SESSION['form_data']['type'] == 'Cat') ? 'selected' : ''; ?>>Cat</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="gender" class="block text-gray-700 font-bold mb-2 text-xs">Gender:</label>
                        <select id="gender" name="gender" class="form-select border w-full py-1" required>
                            <option value="">Select Gender</option>
                            <option value="Female" <?php echo (isset($_SESSION['form_data']['gender']) && $_SESSION['form_data']['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                            <option value="Male" <?php echo (isset($_SESSION['form_data']['gender']) && $_SESSION['form_data']['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2 text-xs">About this Pet:</label>
                        <textarea id="info" name="info" class="form-input border w-full py-1"><?php echo htmlspecialchars($_SESSION['form_data']['info'] ?? ''); ?></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="photo" class="block text-gray-700 font-bold mb-2 text-xs">Photo:</label>
                        <input type="file" id="photo" name="photo" class="form-input border w-full py-2" required>
                        <?php if (isset($_SESSION['photo_error'])): ?>
                            <div class="mt-4 text-xs">
                                <p class="text-red-500"><?php echo $_SESSION['photo_error']; ?></p>
                            </div>
                            <?php unset($_SESSION['photo_error']); ?>
                        <?php endif; ?>
                    </div>
                    <div>
                        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Submit</button>
                    </div>
                </form>
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="mt-4">
                        <strong class="font-bold text-blue-600">Success!</strong>
                        <span class="block sm:inline text-blue-600"><?php echo $_SESSION['success']; ?></span> 
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="mt-4 text-xs">
                        <p class="text-red-500"><?php echo $_SESSION['error']; ?></p>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
            </div>
            <div>
                <img src="https://i.ibb.co/vqNQqzT/peek-a-boo-cat-form.webp" alt="Cat" class="w-64 h-auto">
            </div>
        </div>
    </div>
</body>
</html>
