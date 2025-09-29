<?php
include 'config.php';




// Check login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
// Fetch students with attendance stats in one query
$analyticsQuery = $conn->query("
    SELECT s.reg_no, s.firstname, s.lastname,
        COUNT(a.status) AS total,
        SUM(a.status='Present') AS present,
        SUM(a.status='Absent') AS absent
    FROM student s
    LEFT JOIN attende a ON s.reg_no = a.reg_no
    GROUP BY s.reg_no
    ORDER BY CAST(SUBSTRING_INDEX(s.reg_no,'/',-1) AS UNSIGNED) ASC
");

$analytics = [];
while($row = $analyticsQuery->fetch_assoc()) {
    $percentage = ($row['total'] > 0) ? round(($row['present'] / $row['total']) * 100, 1) : 0;
    $analytics[] = [
        'reg_no' => $row['reg_no'],
        'name' => $row['firstname'].' '.$row['lastname'],
        'total' => $row['total'],
        'present' => $row['present'],
        'absent' => $row['absent'],
        'percentage' => $percentage
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Attendance Analytics</title>
<style>
body { font-family:Poppins,sans-serif; background:#f5f7fa; padding:20px;}
.card { background:#fff; border-radius:12px; padding:25px; margin:auto; max-width:1000px; box-shadow:0 5px 20px rgba(0,0,0,0.05);}
table { width:100%; border-collapse:collapse; margin-top:15px;}
th, td { padding:12px; text-align:center; border-bottom:1px solid #eee; }
th { background:#2575fc; color:#fff; position: sticky; top: 0;}
tr:hover { background:#f1f5f9; }
.present-row { background:#d4edda; }   /* >=75% green */
.warning-row { background:#fff3cd; }   /* 50-74% yellow */
.absent-row { background:#f8d7da; }    /* <50% red */
input[type=text] { width:100%; max-width:300px; padding:8px 12px; border-radius:8px; border:1px solid #ddd; margin-bottom:10px; }
#searchBar { margin-bottom:15px; }
button.reset-btn { padding:8px 15px; border:none; border-radius:8px; background:#555; color:#fff; cursor:pointer; margin-left:5px; }
button.reset-btn:hover { background:#333; }
footer { text-align:center; margin-top:40px; padding:15px; font-size:1rem; color:#fff; background:#2575fc; border-radius:12px; }
footer a { color:#fff; font-weight:600; text-decoration:none; }
footer a:hover { text-decoration:underline; }
a.button-back { display:inline-block; margin-bottom:15px; padding:8px 15px; background:#555; color:#fff; border-radius:8px; text-decoration:none; }
a.button-back:hover { background:#333; }
@media(max-width:768px){ th,td{font-size:0.9rem; padding:8px;} input[type=text]{width:100%;} }
</style>
</head>
<body>

<div class="card"><a href="index.php" class="button-back">Back to Dashboard</a>
<h3>Attendance Analytics</h3>

<input type="text" id="searchBar" placeholder="Search by Reg No or Name">
<button class="reset-btn" onclick="resetSearch()">Reset</button>


<table id="analyticsTable">
<tr>
<th>Reg No</th>
<th>Full Name</th>
<th>Total Days Marked</th>
<th>Present Days</th>
<th>Absent Days</th>
<th>Attendance %</th>
</tr>

<?php foreach($analytics as $a):
    if($a['percentage'] >= 75) $rowClass = 'present-row';
    elseif($a['percentage'] >= 50) $rowClass = 'warning-row';
    else $rowClass = 'absent-row';
?>
<tr class="<?= $rowClass ?>">
<td><?= $a['reg_no'] ?></td>
<td><?= $a['name'] ?></td>
<td><?= $a['total'] ?></td>
<td><?= $a['present'] ?></td>
<td><?= $a['absent'] ?></td>
<td><?= $a['percentage'] ?>%</td>
</tr>
<?php endforeach; ?>

</table>
</div>

<script>
// Search filter
const searchInput = document.getElementById('searchBar');
searchInput.addEventListener('keyup', function() {
    const filter = searchInput.value.toLowerCase();
    const rows = document.querySelectorAll('#analyticsTable tr:not(:first-child)');
    rows.forEach(row => {
        const reg = row.cells[0].innerText.toLowerCase();
        const name = row.cells[1].innerText.toLowerCase();
        row.style.display = (reg.includes(filter) || name.includes(filter)) ? '' : 'none';
    });
});

// Reset search
function resetSearch() {
    searchInput.value = '';
    const rows = document.querySelectorAll('#analyticsTable tr:not(:first-child)');
    rows.forEach(row => row.style.display = '');
}
</script>

<footer>
  Developed with ❤️ by 
  <a href="https://harishpavan-dev.vercel.app" target="_blank">Bavananthan Harishpavan</a>
</footer>

</body>
</html>
