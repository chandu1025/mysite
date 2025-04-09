<?php
session_start();
include "db.php";


if (isset($_POST['login'])) {
    $admin_ID = $_POST['admin_id'];
    $admin_password = $_POST['password'];

    if ( $admin_ID === "0000" && $admin_password === "0000") {
        $_SESSION['admin'] = "Admin";
        header("Location: admin.php");
        exit;
    } else {
        echo "<script>alert('Invalid admin credentials!'); window.location.href='index.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }

        body { display: flex;
            height: 100vh; 
            justify-content: center; 
            align-items: center;
            background: url('hospital.jpg') no-repeat center center/cover; }
        
        .login-card { 
            background: rgba(255, 255, 255, 0.9); 
            padding: 30px; 
            border-radius: 12px; 
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2); 
            width: 350px; 
            text-align: center; 
            align-items: center;
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
        <h2>AdminLogin</h2>
        <form method="post">
            <input type="text" name="admin_id" placeholder="Enter Admin ID" required>
            <input type="password" name="password" placeholder="Enter Password" required>
            <button type="submit" name="login">Login</button>
        </form>
        <br>
        <a href="index.php">Go Back</a>
</div>
<footer>
    <p>&copy; <?php echo date("Y"); ?> astTECs. All rights reserved.</p>
</footer>
</body>
</html>