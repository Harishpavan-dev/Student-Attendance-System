<?php
include 'config.php'; // Use the shared config for DB and session

$message = "";
$prefix = "JAF/IT/2025/F/"; // Fixed prefix

if (isset($_POST['add_student_submit'])) {
    $reg_no_suffix = $conn->real_escape_string($_POST['reg_no_suffix']);
    $firstname = $conn->real_escape_string($_POST['firstname']);
    $lastname = $conn->real_escape_string($_POST['lastname']);
    $contact = $conn->real_escape_string($_POST['contact']);
    $email = $conn->real_escape_string($_POST['email']);

    $reg_no = $prefix . $reg_no_suffix;

    // Validate suffix is numeric
    if (!preg_match("/^\d+$/", $reg_no_suffix)) {
        $message = "<p class='error'>Only numbers allowed for registration suffix!</p>";
    }
    // Validate contact number: international format e.g., +94771234567
    elseif (!empty($contact) && !preg_match("/^\+\d{10,15}$/", $contact)) {
        $message = "<p class='error'>Invalid contact number! Use international format e.g., +94771234567</p>";
    } 
    else {
        // Check if reg_no already exists
        $check = $conn->query("SELECT reg_no FROM student WHERE reg_no='$reg_no'");
        if ($check->num_rows > 0) {
            $message = "<p class='error'>Student with this Reg No already exists!</p>";
        } else {
            $conn->query("INSERT INTO student (reg_no, firstname, lastname, contact, email) 
                          VALUES ('$reg_no', '$firstname', '$lastname', '$contact', '$email')");
            $message = "<p class='success'>Student added successfully! Reg No: $reg_no</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Student</title>
<style>
body{font-family:Poppins,sans-serif;background:#f5f7fa;padding:20px;}
.card{background:#fff;border-radius:12px;padding:25px;margin:30px auto;max-width:500px;box-shadow:0 5px 20px rgba(0,0,0,0.05);}
input[type=text], input[type=email]{width:100%;padding:12px;margin-bottom:15px;border-radius:8px;border:1px solid #ddd;}
input[type=submit]{padding:12px 20px;background:#2575fc;color:#fff;border:none;border-radius:8px;cursor:pointer;font-weight:600;transition:all 0.3s ease;}
input[type=submit]:hover{background:#6a11cb;}
a{display:inline-block;margin-top:15px;color:#2575fc;text-decoration:none;}
a:hover{text-decoration:underline;}
.success{color:green;margin-bottom:15px;}
.error{color:red;margin-bottom:15px;}
</style>
</head>
<body>
<div class="card">
<h3>Add New Student</h3>

<?php if($message) echo $message; ?>

<form method="post">
<label>Registration No (Prefix: <?= $prefix ?>)</label>
<input type="text" name="reg_no_suffix" required pattern="\d+" placeholder="Enter last number only">

<label>First Name:</label>
<input type="text" name="firstname" required>

<label>Last Name:</label>
<input type="text" name="lastname" required>

<label>Contact (+94771234567):</label>
<input type="text" name="contact" pattern="\+\d{10,15}" placeholder="+94771234567">

<label>Email:</label>
<input type="email" name="email">

<input type="submit" name="add_student_submit" value="Save Student">
</form>
<a href="index.php">Back to Dashboard</a>
</div>
</body>
</html>
