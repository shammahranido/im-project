<?php
session_start();

function insertFeedback(PDO $pdo, $customer_id, $name, $email, $message) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO feedback (customer_id, fb_name, fb_email, fb_message)
            VALUES (:cus_id, :name, :email, :message)
        ");

        $stmt->bindParam(':cus_id', $customer_id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':message', $message);

        $stmt->execute();

        $_SESSION['message'] = "Thank you for taking the time to share your insights with us. Feedback submitted successfully.";
    } catch (PDOException $e) {
        $_SESSION['message'] = 'Connection failed: ' . $e->getMessage();
    }
}

$host = 'localhost';
$dbname = 'postgres';
$user = 'postgres';
$password = '0205';

$name = htmlspecialchars_decode($_POST['name']);
$name = strtoupper($name);
$email = htmlspecialchars($_POST['email']);
$message = htmlspecialchars_decode($_POST['message']);

if (empty($name) || empty($email) || empty($message)) {
    $_SESSION['message'] = "Failure in processing your feedback. Please fill out all required fields.";
} else {
    try {
        $dsn = "pgsql:host=$host;dbname=$dbname";
        $pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        $pdo->beginTransaction();

        // Check if the customer exists in the database
        $stmt = $pdo->prepare("SELECT customer_id FROM customer WHERE customer_name = :name");
        $stmt->bindParam(':name', $name);
        $stmt->execute();
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($customer) {
            // Customer found, insert feedback
            $customer_id = $customer['customer_id'];
            insertFeedback($pdo, $customer_id, $name, $email, $message);
            $pdo->commit();
        } else {
            // Customer not found, set error message
            $_SESSION['message'] = "Cannot submit feedback because customer data is not registered.";
            $pdo->rollBack();
        }

    } catch (PDOException $e) {
        $_SESSION['message'] = 'Connection failed: ' . $e->getMessage();
        $pdo->rollBack();
    }
}

header('Location: feedback-thank-you.php');
exit;
?>