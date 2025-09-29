<?php


include 'config.php'; // Use centralized DB connection

// Get date from search or default to today
$searchDate = $_GET['date'] ?? date('Y-m-d');

// Fetch attendance with numeric order by Reg No
$attendanceResult = $conn->query("
    SELECT s.reg_no, s.firstname, s.lastname, 
           IFNULL(a.status,'Not Marked') as status 
    FROM student s 
    LEFT JOIN attende a ON s.reg_no=a.reg_no AND a.date='$searchDate'
    ORDER BY CAST(SUBSTRING_INDEX(s.reg_no,'/',-1) AS UNSIGNED) ASC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>View Attendance</title>
<style>
body{font-family:Poppins,sans-serif;background:#f5f7fa;padding:20px;}
.card{background:#fff;border-radius:12px;padding:25px;margin:30px auto;max-width:900px;box-shadow:0 5px 20px rgba(0,0,0,0.05);}
table{width:100%;border-collapse:collapse;margin-top:15px;}
th,td{padding:12px;text-align:center;border-bottom:1px solid #eee;}
th{background:#2575fc;color:#fff;}
tr.present-row{background:#d4edda;}
tr.absent-row{background:#f8d7da;}
tr:hover{background:#f1f5f9;}
input[type=date]{padding:8px 12px;border-radius:8px;border:1px solid #ddd;margin-right:5px;}
button{padding:8px 15px;margin:5px;border:none;border-radius:8px;background:#2575fc;color:#fff;cursor:pointer;transition:all 0.3s;}
button:hover{background:#6a11cb;}
a{display:inline-block;margin-top:15px;color:#fff;text-decoration:none;padding:8px 15px;background:#555;border-radius:8px;}
a:hover{text-decoration:underline;}
@media(max-width:768px){th,td{font-size:0.9rem;padding:8px;} button{font-size:0.9rem;padding:6px 10px;}}
</style>
</head>
<body>

<div class="card">
<a href="index.php">Back to Dashboard</a>
<h3>Attendance for <?= $searchDate ?></h3> 

<!-- Search Box -->
<form method="get" style="margin-bottom:15px;">
    <input type="date" name="date" value="<?= $searchDate ?>">
    <input type="submit" value="Search">
    <button type="button" onclick="window.location='analytics.php'">View Analytics</button>
   
</form>

<table>
<tr>
<th>Reg No</th>
<th>Full Name</th>
<th>Status</th>
</tr>

<?php while($row = $attendanceResult->fetch_assoc()): 
    $class = '';
    if($row['status']=='Present') $class='present-row';
    if($row['status']=='Absent') $class='absent-row';
?>
<tr class="<?= $class ?>">
<td><?= $row['reg_no'] ?></td>
<td><?= $row['firstname'].' '.$row['lastname'] ?></td>
<td><?= $row['status'] ?></td>
</tr>
<?php endwhile; ?>
</table>
</div>
</body>
</html>
