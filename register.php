<?php
session_start();
include "db.php"; 



if (isset($_POST['register'])) {
    $employee_id = $_POST['employee_id'];
    $phone = $_POST['phone'];
    $department = $_POST['department'];
    $role = "user";
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);


    $stmt = $conn->prepare("INSERT INTO registers (em_id, phone, department, role,password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $employee_id, $phone, $department, $role, $password);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful! Wait for admin approval.');</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
    $stmt->close();
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

        body {
            font-family: Arial, sans-serif;
            background: url('hospital.jpg') no-repeat center center/cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        h1 { 
            color: #007bff; 
            font-size: 24px; 
            margin-bottom: 10px; 
            text-transform: uppercase;
        }


        form {
            background: rgba(255, 255, 255, 0.9); 
            padding: 30px; 
            border-radius: 12px; 
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.9); 
            width: 350px; 
            text-align: center;
            align-items: center;
        }

        .form-card h1 { 
            color: #007bff; 
            font-size: 24px; 
            margin-bottom: 10px; 
            text-transform: uppercase; 
        }

        .form-card h2 { margin-bottom: 20px; color: black; text-align: center; font-weight: bold; }

       
        .form-card input, select {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
        }

        
        .form-card button {
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

        .form-card button:hover {
            background: #0056b3;
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
<div class="overlay" id="overlay">
    <div class="form-card" id="form-card">
        <form method="post">
            <h1>CHILDREN'S HOSPITAL</h1>
            <h2 id="form-title">Register User</h2>
            <input type="text" id="form_employee_id" name="employee_id" placeholder="Enter Employee id" required>
            <input type="text" id="form_phone" name="phone" placeholder="Registered Mobile" required>
            <select name="department" id="form_department">
                <option value="nurse">nurse</option>
                <option value="doctor">doctor</option>
            </select>
            <input type="password" id="password" name="password" placeholder="Enter Password" required>
            <button type="submit" name="register" id="submit-btn">Register</button>
            <br>
            <br>
            <a href="index.php">Go Back</a>
        </form>
    </div>
</div>

<footer>
    <p>&copy; <?php echo date("Y"); ?> astTECs. All rights reserved.</p>
</footer>
    
</body>
</html>