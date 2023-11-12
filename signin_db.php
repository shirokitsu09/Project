<?php 

    session_start();
    require_once 'config/db.php';

    if (isset($_POST['signin'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $_SESSION['username'] = $username;

      
        if (empty($username)) {
            $_SESSION['error'] = 'Please enter a username';
            header("location: signin.php");
        } else if (empty($password)) {
            $_SESSION['error'] = 'Please enter a password';
            header("location: signin.php");
        } else if (strlen($_POST['password']) > 20 || strlen($_POST['password']) < 5) {
            $_SESSION['error'] = 'Please enter a valid password';
            header("location: signin.php");
        } else {
            try {

                $check_data = $conn->prepare("SELECT * FROM users WHERE username = :username");
                $check_data->bindParam(":username", $username);
                $check_data->execute();
                $row = $check_data->fetch(PDO::FETCH_ASSOC);

                if ($check_data->rowCount() > 0) {

                    if ($username == $row['username']) {
                        if (password_verify($password, $row['password'])) {
                            if ($row['urole'] == 'admin') {
                                $_SESSION['admin_login'] = $row['id'];
                                header("location: admin.php");
                            } else {
                                $_SESSION['user_login'] = $row['id'];
                                header("location: user.php");
                            }
                        } else {
                            $_SESSION['error'] = 'Password incorrect';
                            header("location: signin.php");
                        }
                    } else {
                        $_SESSION['error'] = 'Username incorrect';
                        header("location: signin.php");
                    }
                } else {
                    $_SESSION['error'] = "Data not found";
                    header("location: signin.php");
                }

            } catch(PDOException $e) {
                echo $e->getMessage();
            }
        }
    }


?>