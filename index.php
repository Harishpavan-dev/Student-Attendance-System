<?php

include 'config.php';

// Handle local lockout (logout)
if(isset($_POST['lockout'])){
    session_destroy();
    header("Location: login.php");
    exit();
}

// Check login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Handle toggle attendance via AJAX (secure prepared statements)
if (isset($_GET['reg_no']) && isset($_GET['status'])) {
    $reg_no = $_GET['reg_no'];
    $status = $_GET['status'];
    $date = date("Y-m-d");

    if ($status == "None") {
        $stmt = $conn->prepare("DELETE FROM attende WHERE reg_no=? AND date=?");
        $stmt->bind_param("ss", $reg_no, $date);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("
            INSERT INTO attende (reg_no, date, status) 
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE status=VALUES(status)
        ");
        $stmt->bind_param("sss", $reg_no, $date, $status);
        $stmt->execute();
    }
    exit();
}

// Fetch students with today's attendance, ordered numerically
$result = $conn->query("
    SELECT s.*, a.status as attendance_status 
    FROM student s 
    LEFT JOIN attende a ON s.reg_no=a.reg_no AND a.date='".date('Y-m-d')."'
    ORDER BY CAST(SUBSTRING_INDEX(s.reg_no,'/',-1) AS UNSIGNED) ASC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Attendance Dashboard</title>
<style>
/* --- Base Styles --- */
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
body{background:#f5f7fa;padding:20px;min-height:100vh;}
header{display:flex;align-items:center;justify-content:center;gap:15px;margin-bottom:30px;flex-wrap:wrap;text-align:center;}
header img{width:90px;height:90px;}
header h1{font-size:2rem;font-weight:700;color:#2575fc;}

/* Card container */
.card{background:#fff;border-radius:12px;padding:25px;margin-bottom:30px;box-shadow:0 5px 20px rgba(0,0,0,0.05);}

/* Table */
table{width:100%;border-collapse:collapse;margin-top:15px;}
th,td{padding:12px;text-align:center;border-bottom:1px solid #eee;transition:all 0.3s ease;}
th{background:#2575fc;color:#fff;}
tr:hover{background:#f1f5f9;}
.present-row{background:#d4edda;}
.absent-row{background:#f8d7da;}

/* Buttons */
button{padding:8px 15px;border:none;border-radius:8px;cursor:pointer;font-weight:600;transition:all 0.3s ease;margin:2px;background:#ddd;color:#333;}
button:hover{background:#bbb;}
button.present.active{background:#4caf50;color:#fff;}
button.absent.active{background:#f44336;color:#fff;}
.add-btn{background:linear-gradient(45deg,#2575fc,#6a11cb);color:#fff;margin-bottom:10px;}
.add-btn:hover{background:linear-gradient(45deg,#6a11cb,#2575fc);}
.lock-btn{background:#f44336;color:#fff;margin-bottom:10px;}
.lock-btn:hover{background:#d32f2f;}

/* Inputs */
input[type=text]{padding:8px 12px;border-radius:8px;border:1px solid #ddd;width:200px;margin-bottom:10px;}

footer {
    width: 100%;
    text-align: center;
    padding: 15px 0;
    font-size: 1rem;
    color: #fff;
    background: linear-gradient(90deg, #2575fc, #6a11cb);
    border-radius: 10px;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
    font-weight: 500;
}
footer a {
    color: #fff;
    font-weight: 600;
    text-decoration: none;
    transition: 0.3s;
}
footer a:hover {
    text-decoration: underline;
    color: #ffd700;
}
@media(max-width:480px){
    footer { font-size: 0.85rem; padding: 10px 0; }
}

/* --- Responsive --- */
@media(max-width:768px){
  header h1{font-size:1.3rem;line-height:1.4;}
  header img{width:60px;height:60px;}
  th,td{font-size:0.9rem;padding:8px;}
  button{font-size:0.9rem;padding:6px 10px;}
  input[type=text]{width:100%;margin-bottom:15px;}
}
@media(max-width:480px){
  body{padding:10px;}
  header{flex-direction:column;gap:10px;}
  header img{width:45px;height:45px;}
  header h1{font-size:1rem;}
  .card{padding:15px;}
  table{display:block;overflow-x:auto;white-space:nowrap;}
  th,td{font-size:0.8rem;padding:6px;}
  button{font-size:0.75rem;padding:5px 8px;}
}
</style>
</head>
<body>

<header>
  <img src="images/SLIATE_logo.png" alt="Logo">
  <h1>Advanced Technological Institute, Jaffna <br>Student Attendance System</h1>
  <img src="images/atijaffna_logo.jpg" alt="Logo">
</header>

<!-- Top Buttons & Search -->
<div class="card" style="text-align:center;">
  <input type="text" id="regSearch" placeholder="Search by Reg No">
  <button class="add-btn" onclick="window.location='add_student.php'">Add Student</button>
  <button class="add-btn" onclick="window.location='view.php'">View Attendance</button>
  <form method="post" style="display:inline;">
      <button type="submit" name="lockout" class="lock-btn">Lockout</button>
  </form>
</div>

<!-- Attendance Table -->
<div class="card">
  <h3>Mark Attendance (<?= date('Y-m-d') ?>)</h3>
  <table id="attendanceTable">
    <tr>
      <th>Reg No</th>
      <th>Full Name</th>
      <th>Status</th>
    </tr>
    <?php while($row = $result->fetch_assoc()):
        $rowClass = '';
        if($row['attendance_status']=='Present') $rowClass='present-row';
        if($row['attendance_status']=='Absent') $rowClass='absent-row';
    ?>
    <tr id="row-<?= $row['reg_no'] ?>" class="<?= $rowClass ?>">
      <td><?= $row['reg_no'] ?></td>
      <td><?= $row['firstname'].' '.$row['lastname'] ?></td>
      <td>
          <button class="present <?= ($row['attendance_status']=='Present')?'active':'' ?>" onclick="toggleAttendance('<?= $row['reg_no'] ?>','Present', this)">Present</button>
          <button class="absent <?= ($row['attendance_status']=='Absent')?'active':'' ?>" onclick="toggleAttendance('<?= $row['reg_no'] ?>','Absent', this)">Absent</button>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>

<script>
// Toggle attendance
function toggleAttendance(reg_no, status, btn){
    let row = document.getElementById('row-'+reg_no);
    let presentBtn = row.querySelector('.present');
    let absentBtn = row.querySelector('.absent');
    let newStatus;
    if((status=='Present' && presentBtn.classList.contains('active')) || 
       (status=='Absent' && absentBtn.classList.contains('active'))){
        newStatus = 'None';
        row.classList.remove('present-row','absent-row');
        presentBtn.classList.remove('active');
        absentBtn.classList.remove('active');
    } else {
        newStatus = status;
        if(status=='Present'){
            row.classList.add('present-row');
            row.classList.remove('absent-row');
            presentBtn.classList.add('active');
            absentBtn.classList.remove('active');
        } else {
            row.classList.add('absent-row');
            row.classList.remove('present-row');
            absentBtn.classList.add('active');
            presentBtn.classList.remove('active');
        }
    }
    let xhr = new XMLHttpRequest();
    xhr.open('GET','?reg_no='+reg_no+'&status='+newStatus,true);
    xhr.send();
}

// Search by Reg No
document.getElementById('regSearch').addEventListener('keyup', function(){
    let filter = this.value.toLowerCase();
    document.querySelectorAll('#attendanceTable tr:not(:first-child)').forEach(row => {
        let reg = row.cells[0].innerText.toLowerCase();
        row.style.display = reg.includes(filter) ? '' : 'none';
    });
});
</script>

<footer>
  <p>
    Developed with <span style="color:red;">❤️</span> by 
    <a href="https://harishpavan-dev.vercel.app" target="_blank">Bavananthan Harishpavan</a>
  </p>
</footer>

</body>
</html>
