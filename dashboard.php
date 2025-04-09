<?php
session_start();
include "db.php";

if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}



if (isset($_POST['add_user'])) {
    $name = trim($_POST['name']);
    $mobile = trim($_POST['mobile']);


    $check_query = $conn->query("SELECT * FROM user_details WHERE mobile='$mobile'");

    if ($check_query->num_rows > 0) {
        echo "<script>alert('User already exists! Please use a different name or mobile number.'); window.location='dashboard.php';</script>";
        exit();
    } else {
     
        $conn->query("INSERT INTO user_details (name, mobile) VALUES ('$name', '$mobile')");
        header("Location: dashboard.php");
        exit();
    }
}



if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM user_details WHERE id=$id");
    header("Location: dashboard.php");
    exit();
}


if (isset($_POST['edit_user'])) {
    $id = $_POST['edit_id'];
    $name = $_POST['name'];
    $mobile = $_POST['mobile'];
    $check_query = $conn->query("SELECT * FROM user_details WHERE mobile='$mobile'");
    if ($check_query->num_rows > 0) {
        echo "<script>alert('User already exists! Please use a different mobile number.'); window.location='dashboard.php';</script>";
        exit();
    } else {
     
        $conn->query("INSERT INTO user_details (name, mobile) VALUES ('$name', '$mobile')");
        header("Location: dashboard.php");
        exit();
    }
}
$limit = 6;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit; 

$total_records_query = $conn->query("SELECT COUNT(*) AS total FROM user_details");
$total_records = $total_records_query->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);


$records = $conn->query("SELECT * FROM user_details LIMIT $limit OFFSET $offset");

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$whereClause = '';
if (!empty($search)) {
    $whereClause = "WHERE name LIKE '%$search%' OR mobile LIKE '%$search%'";
}


$total_records_query = $conn->query("SELECT COUNT(*) AS total FROM user_details $whereClause");
$total_records = $total_records_query->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);


$records = $conn->query("SELECT * FROM user_details $whereClause LIMIT $limit OFFSET $offset");


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { display: flex; height: 100vh; background: #f4f4f4;  background: white }

      
        .sidebar { width: 250px; background: #333; color: white; padding-top: 20px; }
        .sidebar h2 { text-align: center; }
        .sidebar ul { list-style: none; padding: 0; }
        .sidebar ul li { padding: 15px; text-align: left; border-bottom: 1px solid #444; cursor: pointer; }
        .sidebar ul li:hover { background: #555; }
        .sidebar ul li a { color: white; text-decoration: none; display: block; }

        
        .main-content { flex: 1; padding: 0px;}
        .container { 
            max-width: 100%; 
            margin: 0; 
            text-align: justify; 
            background: rgba(255, 255, 255, 0.9);
            padding: 20px; 
            border-radius: 10px; 
            box-shadow: 0px 0px 10px #ccc;
        }
        
        table { width: 100%; margin-top: 20px; border-collapse: collapse;  background: rgba(255, 255, 255, 0.9);}
        th, td { padding: 10px; border: 1px solid #ddd; text-align: center; }

        
        .btn { padding: 8px 12px; margin: 5px; cursor: pointer; border: none; color: white; border-radius: 5px; }
        .btn-edit { background: #28a745; }
        .btn-delete { background: #dc3545; }
        .btn-add { background: #007bff; margin-bottom: 10px; }

        
        .form-card { 
            display: none; 
            background: white; 
            padding: 30px; 
            border-radius: 12px; 
            position: fixed; 
            top: 50%; 
            left: 50%; 
            transform: translate(-50%, -50%); 
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
            width: 450px;
            text-align: center;
        }

        .form-card input {
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
            cursor: pointer;
        }

        .form-card button[type="submit"] {
            background: #007bff;
            color: white;
        }

        .form-card button:last-child {
            background:rgb(28, 136, 224);
            color: white;
        }

        
        .overlay { 
            display: none; 
            position: fixed; 
            top: 0; left: 0; 
            width: 100%; height: 100%; 
            background: rgba(0, 0, 0, 0.5); 
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

    <div class="sidebar">
        <h2><i class="bi bi-pc"></i>Dashboard</h2>
        <ul>
            <li onclick="showSection('details')"><a href="#"><i class="bi bi-newspaper"></i> Details</a></li>
            <li onclick="showSection('reports')"><a href="#"><i class="bi bi-file-spreadsheet"></i> Reports</a></li>
            <li><a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
        </ul>
    </div>


    <div class="main-content">
        <div id="details" class="container">
            <h2 style="text-align: center;">Registered Patients</h2>
            <button class="btn btn-add" onclick="showForm()"><i class="bi bi-plus"></i>ADD DETAILS</button>
            <form method="GET" action="dashboard.php" style="margin-bottom: 10px;">
            <button type="submit" style="margin: 4px; padding: 8px 12px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; float:right;"><i class="bi bi-search"></i> Search</button>
                <input type="hidden" name="section" value="details">
                <input type="text" name="search" placeholder="Search by Name or Mobile" value="<?= htmlspecialchars($search) ?>" 
                    style="margin: 4px; padding: 8px; width: 200px; border: 1px solid #ccc; border-radius: 4px; float: right;">
                <br>
                <br>
                <br>
                <a href="dashboard.php" style="margin-right: 8px; float:right;">Refresh</a>
            </form>
            <br>

            <table>
        <tr>
            <th>S.No</th>
            <th>Name</th>
            <th>Mobile</th>
            <th>Actions</th>
        </tr>
        <?php 
        $sn = $offset + 1; 
        while ($row = $records->fetch_assoc()): ?>
        <tr>
            <td><?= $sn++; ?></td> 
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['mobile']) ?></td>
            <td>
                <button class="btn btn-edit" onclick="editUser(<?= $row['id'] ?>, '<?= $row['name'] ?>', '<?= $row['mobile'] ?>')"><i class="bi bi-pen"></i> Edit</button>
                <a href="dashboard.php?delete=<?= $row['id'] ?>" onclick="return confirm('Are you sure?');">
                    <button class="btn btn-delete"><i class="bi bi-trash"></i> Delete</button>
                </a>
            </td>
        </tr>
        <?php endwhile; ?>
        </table>
        </div>

        <div id="reports" class="reports-container" style="display: none;">
            <h2 style="text-align: center;">REPORTS</h2>
            <p style="text-align: center;">Reports are under construction</p>
        </div>
    </div>
    


    <div class="overlay" id="overlay"></div>
    <div class="form-card" id="form-card">
        <h2 id="form-title">Register User</h2>
        <form method="post">
            <input type="hidden" id="edit_id" name="edit_id">
            <input type="text" id="form_name" name="name" placeholder="Enter Name" required>
            <input type="text" id="form_mobile" name="mobile" placeholder="Registered Mobile" required>
            <button type="submit" name="add_user" id="submit-btn">Submit</button>
            <button type="submit" name="edit_user" id="edit-btn" style="display:none;">Update</button>
        </form>
        <button onclick="closeForm()">Cancel</button>
    </div>


    <script>
        function showForm() {
            document.getElementById('form-title').innerText = "Register User";
            document.getElementById('submit-btn').style.display = "block";
            document.getElementById('edit-btn').style.display = "none";
            document.getElementById('form_name').value = "";
            document.getElementById('form_mobile').value = "";
            document.getElementById('overlay').style.display = "block";
            document.getElementById('form-card').style.display = "block";
        }

        function editUser(id, name, mobile) {
            document.getElementById('form-title').innerText = "Edit User";
            document.getElementById('submit-btn').style.display = "none";
            document.getElementById('edit-btn').style.display = "block";
            document.getElementById('edit_id').value = id;
            document.getElementById('form_name').value = name;
            document.getElementById('form_mobile').value = mobile;
            document.getElementById('overlay').style.display = "block";
            document.getElementById('form-card').style.display = "block";
        }

        function closeForm() {
            document.getElementById('overlay').style.display = "none";
            document.getElementById('form-card').style.display = "none";
        }

        function showSection(section) {
            document.getElementById('details').style.display = (section === 'details') ? 'block' : 'none';
            document.getElementById('reports').style.display = (section === 'reports') ? 'block' : 'none';
        }

    </script>
    
    <footer>
        <p>&copy; <?php echo date("Y"); ?> astTECs. All rights reserved.</p>
    </footer>

</body>
</html>
