<?php 
class Auth {
    public function login($pdo, $id, $password) {
        $statement = $pdo->prepare('SELECT * FROM admin WHERE admin_idnum = :id');
        $statement->execute(['id' => $id]);
        $admin = $statement->fetch(PDO::FETCH_ASSOC);

        if ($admin && $password === $admin['ad_password']) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['admin_id'];
            return true;
        } else {
            $_SESSION['error'] = "Invalid ID number or password.";
            return false;
        }
    }
}
?>
