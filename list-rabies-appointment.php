<?php
include_once 'admin-db.php';
include_once 'all-process.php';


$statusOptions = ['In Progress', 'Cancelled', 'Completed']; 

try {
    $database = new Database('localhost', 'postgres', 'postgres', '0205');
    $pdo = $database->getPDO();

    $processAppoint = new AllProcess($pdo);

    if (isset($_GET['appoint_id'])) {
        $appointId = $_GET['appoint_id'];

        $query = 'SELECT appointment.customer_id, appointment.appoint_date, appointment.appoint_status,
                         customer.customer_name, customer.cus_num_dog, customer.cus_num_cat 
                  FROM appointment 
                  JOIN customer ON appointment.customer_id = customer.customer_id 
                  WHERE appointment.appoint_id = :appointId';

        $statement = $pdo->prepare($query);
        $statement->bindParam(':appointId', $appointId, PDO::PARAM_INT);
        $statement->execute();
        $appointmentDetails = $statement->fetch(PDO::FETCH_ASSOC);

        if ($appointmentDetails['appoint_status'] == 'In Progress') {
            header('Location: inprogress-rabies.php');
          }
        
      
        if ($appointmentDetails['appoint_status'] == 'Cancelled' || $appointmentDetails['appoint_status'] == 'Completed' ){
            $deleted = $processAppoint->deleteAppointment($appointId);
            header('Location: admin-menu.php?service=list-rabies-appointment');
            exit();
        }
        
    }

    $searchName = isset($_GET['search_name']) ? htmlspecialchars($_GET['search_name']) : '';
    $searchStatus = isset($_GET['search_status']) ? $_GET['search_status'] : '';
    $appointments = [];

    $query = 'SELECT appointment.*, customer.customer_name, customer.cus_num_dog, customer.cus_num_cat 
              FROM appointment 
              JOIN customer ON appointment.customer_id = customer.customer_id 
              WHERE appointment.service_id = :id';

    $params = ['id' => 1];

    if (!empty($searchName)) {
        $query .= ' AND customer.customer_name ILIKE :searchName';
        $params['searchName'] = '%' . $searchName . '%';
    }

    if (!empty($searchStatus)) {
        $query .= ' AND appointment.appoint_status = :status';
        $params['status'] = $searchStatus;
    }

    $query .= ' ORDER BY appointment.appoint_date ASC';

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
    <title>Rabies Appointments</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.15/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Rabies Appointments</h1>
        <!-- Search Forms -->
        <div class="mb-4">
            <form method="GET" action="admin-menu.php">
                <input type="hidden" name="service" value="list-rabies-appointment">
                <div class="flex items-center mb-4">
                    <input
                        type="text"
                        name="search_name"
                        value="<?= htmlspecialchars($searchName) ?>"
                        placeholder="Search by Customer Name"
                        class="px-7 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring focus:ring-indigo-200 w-full max-w-md"
                    />
                    <button type="submit" class="ml-4 mr-4 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Search Name</button>
                    <a href="admin-menu.php?service=list-rabies-appointment"><i class="fas fa-undo"></i></a>
                </div>
            </form>
            <form method="GET" action="admin-menu.php">
                <input type="hidden" name="service" value="list-rabies-appointment">
                <div class="flex items-center">
                    <select name="search_status" class="px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring focus:ring-indigo-200">
                        <option value="">Filter Status</option>
                        <?php foreach ($statusOptions as $option) : ?>
                            <option value="<?= htmlspecialchars($option) ?>" <?= ($searchStatus === $option) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($option) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="ml-4 mr-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Search Status</button>
                    <a href="admin-menu.php?service=list-rabies-appointment"><i class="fas fa-undo"></i></a>
                </div>
            </form>
        </div>
        <!-- Appointments Table -->
        <div class="overflow-x-auto">
            <table class="table-auto min-w-full divide-y">
                <thead class="bg-blue-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Appointment ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Customer Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Appointment Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Appointment Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Number of Dogs</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Number of Cats</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Settings</th>
                    </tr>
                </thead>
                <?php
                if (isset($_SESSION['message'])) {
                    echo '<div class="message">' . $_SESSION['message'] . '</div>';
                    unset($_SESSION['message']);
                }
                ?>

                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($appointments as $appointment): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($appointment['appoint_id']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap uppercase"><?= htmlspecialchars($appointment['customer_name']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($appointment['appoint_date']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($appointment['appoint_time']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($appointment['cus_num_dog']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($appointment['cus_num_cat']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($appointment['appoint_status']) ?></td>
                            <td class="pl-3">
                                <a href="admin-frontend-rabies-view.php?appoint_id=<?=htmlspecialchars($appointment['appoint_id']) ?>"><i class="fa fa-eye" style="margin-right:15px;"></i></a>
                                <a href="admin-frontend-rabies-edit.php?appoint_id=<?=htmlspecialchars($appointment['appoint_id']) ?>"><i class="fas fa-edit" style="color:green; margin-right:15px;"></i></a>
                                <a href="list-rabies-appointment.php?appoint_id=<?= htmlspecialchars($appointment['appoint_id']) ?>" onclick="return confirm('Are you sure you want to remove the appointment?')"><i class="fas fa-trash" style="color:red;"></i></a>
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