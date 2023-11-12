<?php 

    session_start();
    require_once 'config/db.php';

    if (isset($_POST['signup'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $c_password = $_POST['c_password'];
        $urole = 'user';

        if (empty($username)) {
            $_SESSION['error'] = 'Please enter a username';
            header("location: index.php");
        } else if (empty($password)) {
            $_SESSION['error'] = 'Please enter a password';    
            header("location: index.php");
        } else if (strlen($_POST['password']) > 20 || strlen($_POST['password']) < 5) {
            $_SESSION['error'] = 'Please enter a valid password';
            header("location: index.php");
        } else if (empty($c_password)) {
            $_SESSION['error'] = 'Please confirm password';
            header("location: index.php");
        } else if ($password != $c_password) {
            $_SESSION['error'] = 'Password are not matching';
            header("location: index.php");
        } else {
            try {

                $check_email = $conn->prepare("SELECT username FROM users WHERE username = :username");
                $check_email->bindParam(":username", $username);
                $check_email->execute();
                $row = $check_email->fetch(PDO::FETCH_ASSOC);

                if ($row['username'] == $username) {
                    $_SESSION['warning'] = "This user has been login to server <a href='signin.php'>Click here</a> login";
                    header("location: index.php");
                } else if (!isset($_SESSION['error'])) {
                    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("INSERT INTO users(username, password, urole) 
                                            VALUES(:username, :password, :urole)");
                    $stmt->bindParam(":username", $username);
                    $stmt->bindParam(":password", $passwordHash);
                    $stmt->bindParam(":urole", $urole);
                    $stmt->execute();

                    if ($insert_user) {
                        $user_directory = "/var/www/PHP/NAS/" . $username;
                        
                        if (!file_exists($user_directory)) {
                            mkdir($user_directory, 0777, true);

                            $_SESSION['success'] = "Already have a account! <a href='signin.php' class='alert-link'>Click here</a> login";
                            header("location: index.php");
                        }else {
                            $_SESSION['error'] = "Failed to create user directory. Registration failed.";
                        }
                    }
        
                } else {
                    $_SESSION['error'] = "Have something wrong";
                    header("location: index.php");
                }

            } catch(PDOException $e) {
                echo $e->getMessage();
            }
        }
    }


?>