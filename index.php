<?php
session_start();
include "db.php"; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_id = $_POST['employee_id'];
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM registers WHERE em_id='$employee_id' AND status='approved'");
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        header("Location: dashboard.php");
    } else {
        echo "<script>alert('Invalid credentials!'); window.location.href='index.php';</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Children's Hospital - Login</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        
       
        body { 
            display: flex; 
            height: 100vh; 
            justify-content: center; 
            align-items: center; 
            background: url('hospital.jpg') no-repeat center center/cover;
        }

     
        .login-card { 
            background: rgba(255, 255, 255, 0.9); 
            padding: 30px; 
            border-radius: 12px; 
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2); 
            width: 350px; 
            text-align: center; 
        }


        .login-card h1 { 
            color: #007bff; 
            font-size: 24px; 
            margin-bottom: 10px; 
            text-transform: uppercase; 
        }

        .login-card h2 { margin-bottom: 20px; color: #333; }

     
        .login-card input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
        }

       
        .login-card button {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            background: #007bff;
            color: white;
            cursor: pointer;
        }

        .login-card button:hover {
            background: #0056b3;
        }

        .content {
            flex: 1;
        }

        footer {
            background-color: rgb(177, 36, 216);
            color: white;
            text-align: center;
            padding: 10px;
            position: absolute;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <h1>CHILDREN'S HOSPITAL</h1>
        <h2>Login</h2>
        <form method="post">
            <input type="text" name="employee_id" placeholder="Enter employee_id" required>
            <input type="password" name="password" placeholder="Enter Password" required>
            <button type="submit" name="login">Login</button>
        </form>
        <br>
        <a href="register.php" class="btn btn-add">Register here</a>
        <br>
        <br>
        <div class="admin-section">
        <a href="adminlogin.php" class="btn btn-add">Admin login</a>
        </div>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> astTECs. All rights reserved.</p>
    </footer>

</body>
</html>
