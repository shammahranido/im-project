<?php 
class AllProcess {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function insertPet($name, $type, $photo, $info, $gender, $admin_id) {
        $result = [
            'valid' => true,
            'message' => '',
            'name_error' => '',
            'photo_error' => '',
            'general_error' => '',
        ];
    
        for ($i = 0; $i < strlen($name); $i++) {
            $char = $name[$i];
            if (!((ord($char) >= 65 && ord($char) <= 90) || (ord($char) >= 97 && ord($char) <= 122) || ord($char) == 32)) {
                $result['valid'] = false;
                $result['name_error'] = "Invalid name format. Name should contain only letters.";
                return $result;
            }
        }
    
        $directoryFile = "uploads/";
        $filePath = $directoryFile . basename($photo['name']);
        $fileType = pathinfo($filePath, PATHINFO_EXTENSION);
        if ($fileType !== 'jpg' && $fileType !== 'jpeg' && $fileType !== 'png') {
            $result['valid'] = false;
            $result['photo_error'] = "Only JPG, JPEG, and PNG files are allowed.";
            return $result;
        }
    
        if (!move_uploaded_file($photo['tmp_name'], $filePath)) {
            $result['valid'] = false;
            $result['general_error'] = "Error uploading file.";
            return $result;
        }
    
        $query = "INSERT INTO PET (pet_name, pet_type, pet_image, pet_info, pet_gender, admin_id) VALUES (:name, :type, :photo, :info, :gender, :admin_id)";
        $statement = $this->pdo->prepare($query);
        $data = [
            ':name' => $name,
            ':type' => $type,
            ':photo' => basename($photo['name']),
            ':info' => $info,
            ':gender' => $gender,
            ':admin_id' => $admin_id,
        ];
    
        try {
            $execute_q = $statement->execute($data);

            if ($execute_q) {
                $result['message'] = "Pet added successfully!";
                $result['valid'] = true;
            } else {
                $result['valid'] = false;
                $result['general_error'] = "Error... Unable to add pet";
            }
        } catch(PDOException $e) {
            $result['valid'] = false;
            $result['general_error'] = "Database error: " . $e->getMessage();
        }
    
        return $result;
    }

    public function deletePet($pet_id) {
        $query = "DELETE FROM PET WHERE pet_id = :id";
        $statement = $this->pdo->prepare($query);
      
        $data = [
            ':id' => $pet_id
        ];

        $execute_q = $statement->execute($data);
      
        if ($execute_q) {
            return true;
        } else {
            echo 'Unable to remove pet'; 
        }
    }

    public function editPet($pet_id, $info) {
        $query = "UPDATE PET SET pet_info = :info WHERE pet_id = :id";
        $statement = $this->pdo->prepare($query);
        
        $data = [
            ':info' => $info,
            ':id' => $pet_id,
        ];
        
        try {
            $execute_q = $statement->execute($data);

            if ($execute_q) {
                return true;
            } else {
                return false;
            }
        } catch(PDOException $e) {
            $_SESSION['message'] = "Error: " . $e->getMessage();
            return false;
        }
    } 
    
    public function insertAdmin($name, $id, $password) {
        $result = [
            'valid' => true,
            'message' => '',
            'name_error' => '',
            'id_error' => '',
            'password_error' => '',
            'general_error' => '',
        ];
    
        if (empty($name) || empty($id) || empty($password)) {
            $result['valid'] = false;
            $result['general_error'] = "Please fill out all the fields.";
            return $result;
        }

        for ($i = 0; $i < strlen($name); $i++) {
            $char = $name[$i];
            if (!((ord($char) >= 65 && ord($char) <= 90) || (ord($char) >= 97 && ord($char) <= 122) || ord($char) == 32)) {
                $result['valid'] = false;
                $result['name_error'] = "Invalid name format. Name should contain only letters.";
                return $result;
            }
        }
    
        if (strlen($password) <= 8) {
            $result['valid'] = false;
            $result['password_error'] = "Password should be longer than 8 characters.";
            return $result;
        }
    
        $check_query = "SELECT admin_idnum FROM ADMIN WHERE admin_idnum = :id";
        $check_admin = $this->pdo->prepare($check_query);
        $check_admin->execute([':id' => $id]);
        $exist_admin = $check_admin->fetch(PDO::FETCH_ASSOC);
    
        if ($exist_admin) {
            $result['valid'] = false;
            $result['id_error'] = "Admin ID already exists. Unable to create Admin account.";
            return $result;
        }
    
        $query = "INSERT INTO ADMIN (ad_name, admin_idnum, ad_password) VALUES (:name, :id, :password)";
        $stmt = $this->pdo->prepare($query);
    
        $data = [
            ':name' => $name,
            ':id' => $id,
            ':password' => $password,
        ];
    
        try {
            $execute_q = $stmt->execute($data);
    
            if ($execute_q) {
                $result['message'] = "Admin added Successfully!";
            } else {
                $result['valid'] = false;
                $result['general_error'] = "Error... Unable to register admin";
            }
        } catch(PDOException $e) {
            $result['valid'] = false;
            $result['general_error'] = "Error: " . $e->getMessage();
        }
    
        return $result;
    }          

    public function deleteAdmin($admin_id) {
        $query = "DELETE FROM ADMIN WHERE admin_id = :id";
        $statement = $this->pdo->prepare($query);
    
        $data = [
            ':id' => $admin_id
        ];
    
        $deleted = $statement->execute($data);
    
        if ($deleted) {
            header('Location: admin-menu.php?page=admin');
            exit();
        } else {
            echo "Admin user deletion failed";
            exit();
        }
    }
    

    public function deleteAppointment($appoint_id) {
        try {
            $query = "SELECT customer_id FROM appointment WHERE appoint_id = :id";
            $statement = $this->pdo->prepare($query);
            $data = [
                ':id' => $appoint_id
            ];

            $statement->execute($data);
    
            $customer = $statement->fetch(PDO::FETCH_ASSOC);

            if (!$customer) {
                throw new Exception('Appointment not found.');
            }
    
            $customer_id = $customer['customer_id'];
    
            $query = "DELETE FROM appointment WHERE appoint_id = :id";
            $statement = $this->pdo->prepare($query);
            $data = [
                ':id' => $appoint_id
            ];

            $del_appointment = $statement->execute($data);
    
            if (!$del_appointment) {
                throw new Exception('Unable to delete appointment.');
            }
    
            $query = "DELETE FROM customer WHERE customer_id = :customer_id";
            $statement = $this->pdo->prepare($query);
            $data = [':customer_id' => $customer_id];
            $del_customer = $statement->execute($data);
    
            if (!$del_customer) {
                throw new Exception('Unable to delete customer.');
            }
    
            return true;
    
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }        

    //basta delete ni sa adoption makalibog na nuon ang name: hardDeleteAdoption dapat
    public function hardDeleteAppointment($pet_id) {
        try {
            $query = "SELECT customer_id FROM adoption WHERE pet_id = :pet_id";
            $statement = $this->pdo->prepare($query);
            $data = [
                ':pet_id' => $pet_id
            ];
            $statement->execute($data);
            
            $result = $statement->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                echo 'Error: Adoption record not found.';
                return false;
            }
    
            $customer_id = $result['customer_id'];
    
            $query = "DELETE FROM adoption WHERE pet_id = :pet_id";
            $statement = $this->pdo->prepare($query);
            $data = [
                ':pet_id' => $pet_id
            ];

            $del_action = $statement->execute($data);

            if (!$del_action) {
                echo 'Error: Adoption deletion failed.';
                return false;
            }
    
            $query = "DELETE FROM pet WHERE pet_id = :pet_id";
            $statement = $this->pdo->prepare($query);

            $data = [':pet_id' => $pet_id];

            if (!$statement->execute($data)) {
                echo 'Error: Pet deletion failed.';
                return false;
            }
    
            $query = "DELETE FROM customer WHERE customer_id = :customer_id";
            $statement = $this->pdo->prepare($query);
            $data = [':customer_id' => $customer_id];

            $del = $statement->execute($data);

            if (!$del) {
                echo 'Error: Customer deletion failed.';
                return false;
            }
    
            return true;
    
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }
    
    public function insertCustomer($name, $email, $contactnum) {
        $name = strtoupper($name);

        $check_query = "SELECT adoption.adopt_id, adoption.adopt_date, adoption.adopt_time,
                            customer.customer_name, customer.customer_email, 
                            customer.customer_contactnum, 
                            pet.pet_id, pet.pet_name, pet.pet_image
                        FROM adoption
                        JOIN customer ON adoption.customer_id = customer.customer_id
                        JOIN pet ON adoption.pet_id = pet.pet_id
                        WHERE customer.customer_name = :name AND customer.customer_email = :email";

        $check_customer = $this->pdo->prepare($check_query);
        $check_customer->execute([
            ':name' => $name,
            ':email' => $email,
        ]);

        $exist_customer = $check_customer->fetch(PDO::FETCH_ASSOC);

        $check_query2 = "SELECT adoption.adopt_id, adoption.adopt_date, adoption.adopt_time,
                            customer.customer_name, customer.customer_email, 
                            customer.customer_contactnum, 
                            pet.pet_id, pet.pet_name, pet.pet_image
                        FROM adoption
                        JOIN customer ON adoption.customer_id = customer.customer_id
                        JOIN pet ON adoption.pet_id = pet.pet_id
                        WHERE customer.customer_name = :name AND customer.customer_contactnum = :contactnum";
        $check_customer2 = $this->pdo->prepare($check_query2);
        $check_customer2->execute([
            ':name' => $name,
            ':contactnum' => $contactnum,
        ]);

        $exist_customer2 = $check_customer2->fetch(PDO::FETCH_ASSOC);

        if ($exist_customer) {
            $_SESSION['message'] = "The customer still has an appointment to be attended. Can't schedule another appointment yet.";
            header('Location: customer-frontend-reservation-adopt.php?pet_id=' . urlencode($_POST['pet_id']));
            exit(0);
        }

        else if ($exist_customer2){
            $_SESSION['message'] = "The customer still has an appointment to be attended. Can't schedule another appointment yet.";
            header('Location: customer-frontend-reservation-adopt.php?pet_id=' . urlencode($_POST['pet_id']));
            exit(0);
        }

        $query = 'INSERT INTO CUSTOMER (customer_name, customer_email, customer_contactnum) 
                VALUES (:name, :email, :contactnum)';
        $statement = $this->pdo->prepare($query);

        $data = [
            ':name' => $name,
            ':email' => $email,
            ':contactnum' => $contactnum,
        ];

        try {
            $execute_q = $statement->execute($data);

            if ($execute_q) {
                $customer_id = $this->pdo->lastInsertId();
                return $customer_id;
            } else {
                echo "Error: Unable to execute customer insert query.<br>";
                return false;
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    public function insertAdoption($customer_id, $pet_id, $adopt_date, $adopt_time, $ques_one, $ques_two, $ques_three) {
        
        $current_date = date('Y-m-d');

        if ($adopt_date < $current_date) {
            $_SESSION['date_message'] = "Invalid appointment date.";
            header('Location: customer-frontend-reservation-adopt.php?pet_id=' . urlencode($_POST['pet_id']));
            return false;
        }

        if ($adopt_time < '09:00' || $adopt_time > '16:30') {
            $_SESSION['time_message'] = "Appointment time must be between 9:00 AM to 4:30 PM.";
            header('Location: customer-frontend-reservation-adopt.php?pet_id=' . urlencode($_POST['pet_id']));
            return false;
        }

        $query = 'INSERT INTO ADOPTION (customer_id, pet_id, adopt_date, adopt_time, ques_one, ques_two, ques_three) VALUES (:customer_id, :pet_id, :adopt_date, :adopt_time, :ques_one, :ques_two, :ques_three)';
        $statement = $this->pdo->prepare($query);

        $data = [
            ':customer_id' => $customer_id,
            ':pet_id' => $pet_id,
            ':adopt_date' => $adopt_date,
            ':adopt_time' => $adopt_time,
            ':ques_one' => $ques_one,
            ':ques_two' => $ques_two,
            ':ques_three' => $ques_three,
        ];

        try {
            $execute_q = $statement->execute($data);

            if ($execute_q) {
                return true;
            } else {
                echo "Error: Unable to execute adoption insert query.<br>";
                return false;
            }
        } catch (PDOException $e) {
            $_SESSION['message'] = "Error: " . $e->getMessage();
            header('Location: customer-frontend-reservation-adopt.php?pet_id=' . urlencode($_POST['pet_id']));
            exit(0);
        }
    }

    public function deleteAdoption($adopt_id) {
        try {
            $query = "SELECT customer_id FROM adoption WHERE adopt_id = :id";
            $statement = $this->pdo->prepare($query);
            $data = [
                ':id' => $adopt_id
            ];

            $statement->execute($data);
            $customer = $statement->fetch(PDO::FETCH_ASSOC);

            if (!$customer) {
                throw new Exception('Adoption record not found.');
            }
    
            $customer_id = $customer['customer_id'];
    
            $query = "DELETE FROM adoption WHERE adopt_id = :id";
            $statement = $this->pdo->prepare($query);
            $data = [
                ':id' => $adopt_id
            ];

            if (!$statement->execute($data)) {
                throw new Exception('Unable to delete adoption appointment.');
            }
    
            $query = "DELETE FROM customer WHERE customer_id = :customer_id";

            $statement = $this->pdo->prepare($query);
            $data = [':customer_id' => $customer_id];

            if (!$statement->execute($data)) {
                throw new Exception('Unable to delete customer.');
            }
    
            return true;
    
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }
    
    public function editReservation($appoint_id, $appoint_status) {
        $query = "UPDATE APPOINTMENT 
                SET appoint_status = :appoint_status 
                WHERE appoint_id = :id";
        $statement = $this->pdo->prepare($query);
        
        $data = [
            ':appoint_status' => $appoint_status,
            ':id' => $appoint_id,
        ];
        
        try {
            $execute_q = $statement->execute($data);

            if ($execute_q) {
            $_SESSION['message'] = "Client's reservation has been edited successfully!";
            } else {
                $_SESSION['message'] = "Error... Unable to edit information";
            }
        } catch(PDOException $e) {
            $_SESSION['message'] = "Error: " . $e->getMessage();
            return false;
        }
    }

    public function getAppointment($appoint_id) {
        if (empty($appoint_id)) {
            $_SESSION['message'] = "No appointment ID provided.";
            return null;
        }
    
        $query = "SELECT appointment.*, customer.* 
                  FROM appointment 
                  JOIN customer ON appointment.customer_id = customer.customer_id 
                  WHERE appointment.appoint_id = :id";
    
        $statement = $this->pdo->prepare($query);

        $data = [
            ':id' => $appoint_id
        ];

        $statement->execute($data);
        $reservation = $statement->fetch(PDO::FETCH_ASSOC);
    
        if ($reservation) {
            return $reservation; 
        } else {
            $_SESSION['message'] = "Appointment not found.";
            return null;
        }
    }

    public function getAdoption($adopt_id) {
        if (empty($adopt_id)) {
            $_SESSION['message'] = "No adoption ID provided.";
            return null;
        }
    
        $query = "SELECT adoption.*, customer.*, pet.*
                  FROM adoption
                  JOIN customer ON adoption.customer_id = customer.customer_id
                  JOIN pet ON adoption.pet_id = pet.pet_id
                  WHERE adoption.adopt_id = :id
                  ORDER BY adoption.adopt_date ASC";
    
        try {
            $statement = $this->pdo->prepare($query);

            $data = [
                ':id' => $adopt_id
            ];

            $statement->execute($data);
            $adoption = $statement->fetch(PDO::FETCH_ASSOC);
    
            if ($adoption) {
                return $adoption;
            } else {
                $_SESSION['message'] = "Appointment not found.";
                return null;
            }
        } catch (PDOException $e) {
            $_SESSION['message'] = "Error retrieving adoption details: " . $e->getMessage();
            return null;
        }
    }
    

    public function getPet($pet_id) {
        if (empty($pet_id)) {
            $_SESSION['message'] = "No pet ID provided.";
            return null;
        }
    
        $query = "SELECT * FROM PET WHERE pet_id = :id";

        $statement = $this->pdo->prepare($query);
        $data = [
            ':id' => $pet_id
        ];
        $statement->execute($data);
        $pet = $statement->fetch(PDO::FETCH_ASSOC);
    
        if ($pet) {
            return $pet; 
        } else {
            $_SESSION['message'] = "Appointment not found.";
            return null;
        }
    }
    
}

?>