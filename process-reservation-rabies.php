
<?php

session_start();
header('Content-Type: application/json');

function insertCustomer(PDO $pdo, $owner_name, $email, $contact_number, $num_pets_1, $num_pets_2) {

  $owner_name = strtoupper($owner_name);
  
  try {
    $stmt = $pdo->prepare("
    SELECT COUNT(*) AS count
    FROM CUSTOMER c
    INNER JOIN APPOINTMENT a ON c.CUSTOMER_ID = a.CUSTOMER_ID
    WHERE c.CUSTOMER_EMAIL = :email AND a.SERVICE_ID = 1;
    ");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $result = $stmt->fetch();
    if ((int) $result['count'] > 0) {
      return array("success" => false, "message" => "A customer with the email '$email' already exists.");
    }

    $stmt = $pdo->prepare("
    SELECT COUNT(*) AS count
    FROM CUSTOMER c
    INNER JOIN APPOINTMENT a ON c.CUSTOMER_ID = a.CUSTOMER_ID
    WHERE c.CUSTOMER_CONTACTNUM = :contact_number AND a.SERVICE_ID = 1;
    ");
    $stmt->bindParam(':contact_number', $contact_number);
    $stmt->execute();
    $result = $stmt->fetch();
    if ((int) $result['count'] > 0) {
      return array("success" => false, "message" => "A customer with the contact number '$contact_number' already exists.");
    }

    $stmt = $pdo->prepare("
    SELECT COUNT(*) AS count
    FROM CUSTOMER c
    INNER JOIN APPOINTMENT a ON c.CUSTOMER_ID = a.CUSTOMER_ID
    WHERE c.CUSTOMER_NAME = :owner_name AND a.SERVICE_ID = 1;
    ");
    $stmt->bindParam(':owner_name', $owner_name);
    $stmt->execute();
    $result = $stmt->fetch();
    if ((int) $result['count'] > 0) {
      return array("success" => false, "message" => "A customer with the name '$owner_name' already exists.");
    }

    $stmt = $pdo->prepare("
      INSERT INTO CUSTOMER (CUSTOMER_NAME, CUSTOMER_EMAIL, CUSTOMER_CONTACTNUM, CUS_NUM_CAT, CUS_NUM_DOG)
      VALUES (:owner_name, :email, :contact_number, :cus_num_cat, :cus_num_dog)
    ");
    $stmt->bindParam(':owner_name', $owner_name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':contact_number', $contact_number);
    $stmt->bindParam(':cus_num_cat', $num_pets_2);
    $stmt->bindParam(':cus_num_dog', $num_pets_1);
    $stmt->execute();

    return array("success" => true, "customer_id" => $pdo->lastInsertId());
  } catch (PDOException $e) {
    return array("success" => false, "message" => 'Connection failed: ' . $e->getMessage());
  }
}

function insertAppointment(PDO $pdo, $service_id, $customer_id, $appoint_status, $appoint_date, $appoint_time) {
  try {
    $stmt = $pdo->prepare("
      INSERT INTO APPOINTMENT (SERVICE_ID, CUSTOMER_ID, APPOINT_STATUS, APPOINT_DATE, APPOINT_TIME)
      VALUES (:service_id, :cus_id, :appoint_status, :appoint_date, :appoint_time)
    ");
    $stmt->bindParam(':service_id', $service_id);
    $stmt->bindParam(':cus_id', $customer_id);
    $stmt->bindParam(':appoint_status', $appoint_status);
    $stmt->bindParam(':appoint_date', $appoint_date);
    $stmt->bindParam(':appoint_time', $appoint_time);
    $stmt->execute();
    return array("success" => true);
  } catch (PDOException $e) {
    return array("success" => false, "message" => 'Connection failed: ' . $e->getMessage());
  }
}


$host = 'localhost';
$dbname = 'postgres';
$user = 'postgres';
$password = '0205';

$input = json_decode(file_get_contents('php://input'), true);

$owner_name = htmlspecialchars($input['name']);
$owner_name = strtoupper($owner_name);
$email = htmlspecialchars($input['email']);
$contact_number = htmlspecialchars($input['contactnum']);
$appointment_date = htmlspecialchars($input['appointment_date']);
$appointment_time = htmlspecialchars($input['appointment_time']);
$service_id = 1;
$num_pets_1 = intval($input['num_dogs']);
$num_pets_2 = intval($input['num_cats']);
$appoint_status = 'In Progress';  
$currentDate = new DateTime('now'); 

$appointTime = DateTime::createFromFormat('H:i', $appointment_time);
$startTime = DateTime::createFromFormat('H:i', '09:00'); // 9:00 AM
$endTime = DateTime::createFromFormat('H:i', '16:30'); 
$appointDate = DateTime::createFromFormat('Y-m-d', $appointment_date);
$appointDate2 = $appointDate->format('Y-m-d');
$currentDate2 = $currentDate->format('Y-m-d');

if (empty($owner_name) || empty($email) || empty($contact_number) || !isset($num_pets_1) || !isset($num_pets_2)) {
  echo json_encode(array("success" => false, "message" => "Please fill out all required fields."));
  exit();
} elseif (empty($num_pets_1) && empty($num_pets_2)) {
  echo json_encode(array("success" => false, "message" => "Please fill out all required fields."));
  exit();
} elseif (!preg_match("/^[a-zA-Z '-]+$/", $owner_name)) {
  echo json_encode(array("success" => false, "message" => "Name must only contain letters."));
  exit();
} elseif (!preg_match("/[0-9]{11}$/", $contact_number)) {
  echo json_encode(array("success" => false, "message" => "Invalid contact number format."));
  exit();
} elseif (strlen($contact_number) != 11) {
  echo json_encode(array("success" => false, "message" => "Contact number must be 11 digits."));
  exit();
} elseif ($appointDate2 < $currentDate2) {
  echo json_encode(array("success" => false, "message" => "Appointment date cannot be before the current date."));
  exit();
} elseif ($appointTime < $startTime || $appointTime > $endTime) {
  echo json_encode(array("success" => false, "message" => "Appointment time must be between 9:00 AM and 4:30 PM."));
  exit();
} else {
  try {
    $dsn = "pgsql:host=$host;dbname=$dbname";
    $pdo = new PDO($dsn, $user, $password, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $pdo->beginTransaction();

    $customerResult = insertCustomer($pdo, $owner_name, $email, $contact_number, $num_pets_1, $num_pets_2);

    if ($customerResult['success']) {
      $customer_id = $customerResult['customer_id'];
      $appointmentResult = insertAppointment($pdo, $service_id, $customer_id, $appoint_status, $appointment_date, $appointment_time);
      if ($appointmentResult['success']) {
        $pdo->commit();
        echo json_encode(array("success" => true, "message" => "Reservation and appointment successfully created."));
      } else {
        $pdo->rollBack();
        echo json_encode($appointmentResult);
      }
    } else {
      $pdo->rollBack();
      echo json_encode($customerResult);
    }
  } catch (PDOException $e) {
    echo json_encode(array("success" => false, "message" => 'Connection failed: ' . $e->getMessage()));
  }
}

exit;
?>
