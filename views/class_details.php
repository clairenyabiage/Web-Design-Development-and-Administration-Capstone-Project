<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../config/config.php';
requireRole('lecturer');

$lecturer_id = $_SESSION['user_id'];
$class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;

if ($class_id === 0) {
    header("Location: dashboard_lecturer.php");
    exit();
}

// Verify lecturer owns this class
$stmt = $conn->prepare("SELECT id, class_code, name, description, schedule, room, capacity FROM classes WHERE id = ? AND lecturer_id = ?");
$stmt->bind_param("ii", $class_id, $lecturer_id);
$stmt->execute();
$result = $stmt->get_result();
$class = $result->fetch_assoc();
$stmt->close();

if (!$class) {
    error_log("Class not found for class_id: " . $class_id);
    $_SESSION['error_message'] = "Class not found or you do not have permission to view it.";
    header("Location: dashboard_lecturer.php");
    die();
}

// Handle grade update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_grade'])) {
    $enrollment_id = (int)$_POST['enrollment_id'];
    $grade = sanitize($_POST['grade']);

    // Validate grade format (e.g., A, B, C, D, F, P, NP)
    if (!preg_match('/^[A-DFP][+-]?$/i', $grade) && !empty($grade)) {
        $_SESSION['error_message'] = "Invalid grade format. Please use A, B, C, D, F, P, NP with optional + or -.";
    } else {
        $stmt = $conn->prepare("UPDATE enrollments SET grade = ? WHERE id = ? AND class_id = ?");
        $stmt->bind_param("sii", $grade, $enrollment_id, $class_id);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Grade updated successfully!";
        } else {
            $_SESSION['error_message'] = "Error updating grade: " . $conn->error;
        }
        $stmt->close();
    }
    header("Location: class_details.php?class_id=" . $class_id);
    exit();
}

// Handle attendance marking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_attendance'])) {
    $enrollment_id = (int)$_POST['enrollment_id'];
    $attendance_date = sanitize($_POST['attendance_date']);
    $status = sanitize($_POST['status']); // present, absent, late

    // Basic validation
    if (empty($attendance_date) || empty($status)) {
        $_SESSION['error_message'] = "Attendance date and status are required.";
    } else {
        // Check if attendance record for this enrollment and date already exists
        $stmt = $conn->prepare("SELECT id FROM attendance WHERE enrollment_id = ? AND attendance_date = ?");
        $stmt->bind_param("is", $enrollment_id, $attendance_date);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $_SESSION['error_message'] = "Attendance for this student on this date has already been marked.";
        } else {
            $stmt = $conn->prepare("INSERT INTO attendance (enrollment_id, attendance_date, status) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $enrollment_id, $attendance_date, $status);
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Attendance marked successfully!";
            }
            else {
                $_SESSION['error_message'] = "Error marking attendance: " . $conn->error;
            }
            $stmt->close();
        }
    }
    header("Location: class_details.php?class_id=" . $class_id);
    exit();
}

$message = '';
if (isset($_SESSION['success_message'])) {
    $message = "<p class='success'>" . $_SESSION['success_message'] . "</p>";
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    $message = "<p class='error'>" . $_SESSION['error_message'] . "</p>";
    unset($_SESSION['error_message']);
}


// Get enrolled students with attendance stats
$enrolled_students = [];
$sql = "SELECT e.id AS enrollment_id, u.id AS student_id, u.full_name, u.email, e.grade,
               (SELECT COUNT(a.id) FROM attendance a WHERE a.enrollment_id = e.id AND a.status = 'present') AS present_count,
               (SELECT COUNT(a.id) FROM attendance a WHERE a.enrollment_id = e.id AND a.status = 'absent') AS absent_count,
               (SELECT COUNT(a.id) FROM attendance a WHERE a.enrollment_id = e.id AND a.status = 'late') AS late_count,
               (SELECT COUNT(a.id) FROM attendance a WHERE a.enrollment_id = e.id) AS total_attendance_records
        FROM enrollments e
        JOIN users u ON e.student_id = u.id
        WHERE e.class_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $class_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $enrolled_students[] = $row;
}
$stmt->close();

include 'header.php';
?>

<div class="dashboard">
    <div class="breadcrumb">
        <a href="dashboard_lecturer.php">Dashboard</a> &gt; <span><?php echo htmlspecialchars($class['class_code']); ?> Details</span>
    </div>

    <h2><?php echo htmlspecialchars($class['class_code']); ?> - <?php echo htmlspecialchars($class['name']); ?></h2>
    <p><strong>Description:</strong> <?php echo htmlspecialchars($class['description']); ?></p>
    <p><strong>Schedule:</strong> <?php echo htmlspecialchars($class['schedule']); ?></p>
    <p><strong>Room:</strong> <?php echo htmlspecialchars($class['room']); ?></p>
    <p><strong>Capacity:</strong> <?php echo count($enrolled_students); ?> / <?php echo htmlspecialchars($class['capacity']); ?></p>
    
    <?php if ($message): ?>
        <div class="message-container"><?php echo $message; ?></div>
    <?php endif; ?>

    <h3>Enrolled Students</h3>
    
    <?php if (empty($enrolled_students)): ?>
        <p>No students are currently enrolled in this class.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Email</th>
                        <th>Grade</th>
                        <th>Attendance Rate</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($enrolled_students as $student): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($student['email']); ?></td>
                            <td>
                                <form action="class_details.php?class_id=<?php echo $class_id; ?>" method="POST" style="display:inline-block;">
                                    <input type="hidden" name="enrollment_id" value="<?php echo $student['enrollment_id']; ?>">
                                    <input type="text" name="grade" value="<?php echo htmlspecialchars($student['grade']); ?>" size="3">
                                    <button type="submit" name="update_grade" class="btn btn-sm btn-primary">Update</button>
                                </form>
                            </td>
                            <td>
                                <?php
                                if ($student['total_attendance_records'] > 0) {
                                    $attendance_rate = ($student['present_count'] / $student['total_attendance_records']) * 100;
                                    echo round($attendance_rate, 2) . "% (" . $student['present_count'] . "/" . $student['total_attendance_records'] . ")";
                                } else {
                                    echo "N/A";
                                }
                                ?>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info mark-attendance-btn" 
                                        data-enrollment-id="<?php echo $student['enrollment_id']; ?>" 
                                        data-student-name="<?php echo htmlspecialchars($student['full_name']); ?>">
                                    Mark Attendance
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Attendance Modal -->
<div id="attendanceModal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h4>Mark Attendance for <span id="modalStudentName"></span></h4>
        <form id="attendanceForm" action="class_details.php?class_id=<?php echo $class_id; ?>" method="POST">
            <input type="hidden" name="enrollment_id" id="modalEnrollmentId">
            <div class="input-group">
                <label for="attendance_date">Date</label>
                <input type="date" id="attendance_date" name="attendance_date" required value="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="input-group">
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="present">Present</option>
                    <option value="absent">Absent</option>
                    <option value="late">Late</option>
                </select>
            </div>
            <button type="submit" name="mark_attendance" class="btn btn-primary">Save Attendance</button>
        </form>
    </div>
</div>

<script>
    // Get the modal
    var modal = document.getElementById("attendanceModal");

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close-button")[0];

    // Get all buttons that open the modal
    var btns = document.querySelectorAll(".mark-attendance-btn");

    // When the user clicks the button, open the modal 
    btns.forEach(function(btn) {
        btn.onclick = function() {
            modal.style.display = "block";
            document.getElementById("modalEnrollmentId").value = this.dataset.enrollmentId;
            document.getElementById("modalStudentName").textContent = this.dataset.studentName;
        }
    });

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

</body>
</html>
