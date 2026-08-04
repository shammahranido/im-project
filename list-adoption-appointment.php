<?php

include_once 'admin-db.php';
include_once 'all-process.php';

try {
    $database = new Database('localhost', 'postgres', 'postgres', '0205');
    $pdo = $database->getPDO();

    $processAdoption = new AllProcess($pdo);

    if (isset($_GET['adopt_id']) && !isset($_GET['pet_id'])) {
        $adoptId = $_GET['adopt_id'];
        $deleted = $processAdoption->deleteAdoption($adoptId);

        if ($deleted) {
            header('Location: admin-menu.php?service=list-adoption-appointment');
            exit();
        } else {
            echo "Appointment deletion failed";
            exit();
        }
    }

    if (isset($_GET['pet_id']) && !isset($_GET['adopt_id'])) {
        $pet_id = $_GET['pet_id'];
        $hard_deleted = $processAdoption->hardDeleteAppointment($pet_id);

        if ($hard_deleted) {
            header('Location: admin-menu.php?service=list-adoption-appointment');
            exit();
        } else {
            echo "Appointment deletion failed";
            exit();
        }
    }

    $searchName = isset($_GET['search_name']) ? htmlspecialchars($_GET['search_name']) : '';
    $appointments = [];

    $query = 'SELECT adoption.adopt_id, adoption.adopt_date, adoption.adopt_time,
                customer.customer_name, customer.customer_email, 
                customer.customer_contactnum, 
                pet.pet_id, pet.pet_name, pet.pet_image
              FROM adoption
              JOIN customer ON adoption.customer_id = customer.customer_id
              JOIN pet ON adoption.pet_id = pet.pet_id';
    
    $params = [];

    if (!empty($searchName)) {
        $query .= ' AND customer.customer_name ILIKE :searchName';
        $params['searchName'] = '%' . $searchName . '%';
    }

    if (!empty($searchStatus)) {
        $query .= ' AND adoption.status = :searchStatus';
        $params['searchStatus'] = $searchStatus;
    }

    $query .= ' ORDER BY adoption.adopt_date ASC';

    $statement = $pdo->prepare($query);
    $statement->execute($params);
    $appointments = $statement->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    echo "Query failed: " . $e->getMessage();
    die(); 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adoption Appointments</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.15/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Adoption Appointments</h1>
        <div class="mb-3">
            <p class="font-bold flex items-center">
                <i class="fas fa-trash-restore" style="color:blue; margin-right:15px;"></i>
                Delete button for unsuccessful appointment
            </p>
            <p class="font-bold flex items-center">
                <i class="fas fa-trash" style="color:red; margin-right:15px;"></i>
                Delete button for successful appointment
            </p>
        </div>
        <form method="GET" action="admin-menu.php">
            <input type="hidden" name="service" value="list-adoption-appointment">
            <div class="flex items-center mb-4">
                <input
                    type="text"
                    name="search_name"
                    value="<?= htmlspecialchars($searchName) ?>"
                    placeholder="Search by Customer Name"
                    class="px-7 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring focus:ring-indigo-200 w-full max-w-md"
                />
                <button type="submit" class="ml-4 mr-4 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Search Name</button>
                <a href="admin-menu.php?service=list-adoption-appointment"><i class="fas fa-undo"></i></a>
            </div>
        </form>
        <div class="overflow-x-auto">
            <table class="table-auto min-w-full divide-y">
                <thead class="bg-yellow-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Appointment ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Customer Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Appointment Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Appointment Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Pet wanted to adopt</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Settings</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($appointments as $appointment): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($appointment['adopt_id']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap uppercase"><?= htmlspecialchars($appointment['customer_name']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($appointment['adopt_date']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($appointment['adopt_time']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap uppercase"><?= htmlspecialchars($appointment['pet_name']) ?></td>
                            <td class="pl-3">
                                <a href="admin-frontend-adoption-view.php?adopt_id=<?=htmlspecialchars($appointment['adopt_id']) ?>"><i class="fa fa-eye" style="margin-right:15px;"></i></a>
                                <a href="list-adoption-appointment.php?adopt_id=<?= htmlspecialchars($appointment['adopt_id']) ?>" onclick="return confirm('Are you sure you want to remove the appointment?')"><i class="fas fa-trash-restore" style="color:blue; margin-right:15px;"></i></a>
                                <a href="list-adoption-appointment.php?pet_id=<?= htmlspecialchars($appointment['pet_id']) ?>" onclick="return confirm('Are you sure you want to remove the appointment?')"><i class="fas fa-trash" style="color:red; margin-right:15px;"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($appointments)) : ?>
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center">No appointments found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
