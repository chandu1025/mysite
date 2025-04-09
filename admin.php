<?php
session_start();
include "db.php";

if (!isset($_SESSION['admin'])) {
    header("Location: adminlogin.php");
    exit;
}

$active_section = isset($_GET['section']) ? $_GET['section'] : 'details';


$limit = 6;
$page_details = isset($_GET['page_details']) ? (int)$_GET['page_details'] : 1;
if ($page_details < 1) $page_details = 1;
$offset_details = ($page_details - 1) * $limit;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$whereClause = '';
if (!empty($search)) {
    $whereClause = "WHERE name LIKE '%$search%' OR mobile LIKE '%$search%'";
}

$total_records_query = $conn->query("SELECT COUNT(*) AS total FROM user_details $whereClause");
$total_records = $total_records_query->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);

$records = $conn->query("SELECT * FROM user_details $whereClause ORDER BY id DESC LIMIT $limit OFFSET $offset_details");

$limit_registrations = 10;
$page_registrations = isset($_GET['page_registrations']) ? (int)$_GET['page_registrations'] : 1;
if ($page_registrations < 1) $page_registrations = 1;
$offset_registrations = ($page_registrations - 1) * $limit_registrations;

$total_register_query = $conn->query("SELECT COUNT(*) AS total1 FROM registers");
$total_register = $total_register_query->fetch_assoc()['total1'];
$total_register_pages = ceil($total_register / $limit_registrations);

$allUsers = $conn->query("SELECT * FROM registers ORDER BY status DESC, id DESC LIMIT $limit_registrations OFFSET $offset_registrations");

if (isset($_GET['approve'])) {
    $id = $_GET['approve'];
    $conn->query("UPDATE registers SET status = 'approved' WHERE id = $id");
    header("Location: admin.php?section=registrations&page_registrations=$page_registrations");
    exit;
}

if (isset($_GET['reject'])) {
    $id = $_GET['reject'];
    $conn->query("UPDATE registers SET status = 'rejected' WHERE id = $id");
    header("Location: admin.php?section=registrations&page_registrations=$page_registrations");
    exit;
}


if (isset($_POST['add_user'])) {
    $name = trim($_POST['name']);
    $mobile = trim($_POST['mobile']);

    $check_query = $conn->query("SELECT * FROM user_details WHERE name='$name' OR mobile='$mobile'");

    if ($check_query->num_rows > 0) {
        echo "<script>alert('User already exists! Please use a different mobile number.'); window.location='admin.php?section=details&page_details=$page_details';</script>";
        exit();
    } else {
        $conn->query("INSERT INTO user_details (name, mobile) VALUES ('$name', '$mobile')");
        header("Location: admin.php?section=details&page_details=$page_details");
        exit();
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM user_details WHERE id=$id");
    header("Location: admin.php?section=details&page_details=$page_details");
    exit();
}

if (isset($_POST['edit_user'])) {
    $id = $_POST['edit_id'];
    $name = $_POST['name'];
    $mobile = $_POST['mobile'];
    $check_query = $conn->query("SELECT * FROM user_details WHERE mobile='$mobile'");
    if ($check_query->num_rows > 0) {
        echo "<script>alert('User already exists! Please use a different mobile number.'); window.location='admin.php';</script>";
        exit();
    } else {
     
        $conn->query("INSERT INTO user_details (name, mobile) VALUES ('$name', '$mobile')");
        header("Location: admin.php");
        exit();
    }
}
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

        .main-content { flex: 1; padding: 0px; }
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
            background-color:rgb(177, 36, 216);
            color: white;
            text-align: center;
            padding: 10px;
            position: absolute;
            bottom: 0;
            width: 100%;
        }

        .admin-container {
            display: none;
            width: calc(100vw - 250px); 
            height: 100vh; 
            position: absolute;
            top: 0;
            left: 250px;
            background: white;
            padding: 20px;
            overflow: auto;
            box-sizing: border-box;
        }

        .admin-container h3{
            text-align: center;
            margin-bottom: 20px;
        }

        .approved-status {
            color: green;
            font-weight: bold;
        }

        .rejected-status {
            color: red;
            font-weight: bold;
        }

        .approve-btn, .reject-btn {
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 0 5px;
        }

        .approve-btn {
            background: green;
            color: white;
        }

        .reject-btn {
            background: red;
            color: white;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2><i class="bi bi-pc"></i>Dashboard</h2>
        <ul>
            <li onclick="showSection('details')"><a href="?section=details"><i class="bi bi-newspaper"></i> Details</a></li>
            <li onclick="showSection('reports')"><a href="?section=reports"><i class="bi bi-file-spreadsheet"></i> Reports</a></li>
            <li onclick="showSection('registrations')"><a href="?section=registrations"><i class="bi bi-people"></i> Registrations</a></li>
            <li><a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div id="details" class="container" style="<?= $active_section == 'details' ? 'display:block;' : 'display:none;' ?>">
            <h2 style="text-align: center;">Registered Patients</h2>
            <button class="btn btn-add" onclick="showForm()"><i class="bi bi-plus"></i>ADD DETAILS</button>
            <form method="GET" action="admin.php" style="margin-bottom: 10px;">
                <button type="submit" style="margin: 4px; padding: 8px 12px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; float:right;"><i class="bi bi-search"></i> Search</button>
                <input type="hidden" name="section" value="details">
                <input type="text" name="search" placeholder="Search by Name or Mobile" value="<?= htmlspecialchars($search) ?>" 
                    style="margin: 4px; padding: 8px; width: 200px; border: 1px solid #ccc; border-radius: 4px; float: right;">
                <br>
                <br>
                <br>
                <a href="admin.php" style="margin-right: 8px; float: right;">Refresh</a>
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
                $sn = $offset_details + 1; 
                while ($row = $records->fetch_assoc()): ?>
                <tr>
                    <td><?= $sn++; ?></td> 
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['mobile']) ?></td>
                    <td>
                        <button class="btn btn-edit" onclick="editUser(<?= $row['id'] ?>, '<?= addslashes($row['name']) ?>', '<?= addslashes($row['mobile']) ?>')" ><i class="bi bi-pen"></i> Edit</button>
                        <a href="admin.php?delete=<?= $row['id'] ?>&section=details&page_details=<?= $page_details ?>" onclick="return confirm('Are you sure?');">
                            <button class="btn btn-delete"><i class="bi bi-trash"></i> Delete</button>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>

            <div style="margin-top: 15px; text-align: center;">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="admin.php?section=details&page_details=<?= $i ?><?= !empty($search) ? '&search='.urlencode($search) : '' ?>" 
                    style="padding: 8px 12px; margin: 2px; border: 1px solid #333; text-decoration: none;
                    background: <?= ($i == $page_details) ? '#007bff' : '#fff'; ?>; 
                    color: <?= ($i == $page_details) ? '#fff' : '#333'; ?>;">
                    <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>

        <div id="reports" class="reports-container" style="<?= $active_section == 'reports' ? 'display:block;' : 'display:none;' ?>">
            <h2 style="text-align: center;">REPORTS</h2>
            <p style="text-align: center;">Reports are under construction</p>
        </div>

        <div id="registrations" class="admin-container" style="<?= $active_section == 'registrations' ? 'display:block;' : 'display:none;' ?>">
            <h2 style="text-align: center;">All Users</h2>
            <table>
                <thead>
                    <tr>
                        <th>S.no</th>
                        <th>Employee_id</th>
                        <th>Phone</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sno = $offset_registrations + 1;
                     while ($row = $allUsers->fetch_assoc()) { ?>
                        <tr>
                            <td><?=  $sno++; ?></td>
                            <td><?= $row['em_id']; ?></td>
                            <td><?= $row['phone']; ?></td>
                            <td><?= $row['department']; ?></td>
                            <td class="<?= strtolower($row['status']); ?>-status"><?= ucfirst($row['status']); ?></td>
                            <td>
                                <?php if ($row['status'] == 'pending') { ?>
                                    <a href="?approve=<?= $row['id']; ?>&section=registrations&page_registrations=<?= $page_registrations ?>" class="approve-btn"><i class="bi bi-check"></i>Approve</a>
                                    <a href="?reject=<?= $row['id']; ?>&section=registrations&page_registrations=<?= $page_registrations ?>" class="reject-btn"><i class="bi bi-x"></i>Reject</a>
                                <?php } else { ?>
                                    -
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table> 
            <div style="margin-top: 15px; text-align: center;">
                <?php for ($i = 1; $i <= $total_register_pages; $i++): ?>
                    <a href="admin.php?section=registrations&page_registrations=<?= $i ?>"
                    style="padding: 8px 12px; margin: 2px; border: 1px solid #333; text-decoration: none; 
                    background: <?= ($i == $page_registrations) ? '#007bff' : '#fff'; ?>; 
                    color: <?= ($i == $page_registrations) ? '#fff' : '#333'; ?>;">
                    <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <div class="overlay" id="overlay"></div>
    <div class="form-card" id="form-card">
        <h2 id="form-title">Register User</h2>
        <form method="post">
            <input type="hidden" name="section" value="<?= $active_section ?>">
            <input type="hidden" id="edit_id" name="edit_id">
            <input type="hidden" name="page_details" value="<?= $page_details ?>">
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
            window.location.href = "admin.php?section=" + section;
        }
    </script>
    
    <footer>
        <p>&copy; <?php echo date("Y"); ?> astTECs. All rights reserved.</p>
    </footer>

</body>
</html>