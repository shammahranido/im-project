<?php
session_start();

$host = 'localhost';
$dbname = 'postgres';
$user = 'postgres';
$password = '0205';

if (isset($_POST['fb_id'])) {
    $fb_id = $_POST['fb_id'];

    try {
        $dsn = "pgsql:host=$host;dbname=$dbname";
        $pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        $stmt = $pdo->prepare("DELETE FROM feedback WHERE fb_id = :fb_id");
        $stmt->bindParam(':fb_id', $fb_id);

        $stmt->execute();

        $_SESSION['message'] = "Feedback deleted successfully";
    } catch (PDOException $e) {
        $_SESSION['message'] = 'Connection failed: ' . $e->getMessage();
    }
}

header('Location: feedback-view.php');
exit;