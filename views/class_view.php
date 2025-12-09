<?php
require_once '../config/config.php';
requireRole('student');

$student_id = $_SESSION['user_id'];
$enrollment_id = isset($_GET['enrollment_id']) ? (int)$_GET['enrollment_id'] : 0;

if ($enrollment_id === 0) {
    header("Location: dashboard_student.php");
    exit();
}

// Verify student owns this enrollment and get class details
$stmt = $conn->prepare("SELECT e.id AS enrollment_id, c.id AS class_id, c.class_code, c.name AS class_name, c.description, 
                               l.full_name AS lecturer_name, l.email AS lecturer_email, c.schedule, c.room, e.grade
                        FROM enrollments e
                        JOIN classes c ON e.class_id = c.id
                        JOIN users l ON c.lecturer_id = l.id
                        WHERE e.id = ? AND e.student_id = ?");
$stmt->bind_param("ii", $enrollment_id, $student_id);
$stmt->execute();
$result = $stmt->get_result();
$enrollment_details = $result->fetch_assoc();
$stmt->close();

if (!$enrollment_details) {
    $_SESSION['error_message'] = "Enrollment not found or you do not have permission to view it.";
    header("Location: dashboard_student.php");
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

// Get attendance records
$attendance_records = [];
$stmt = $conn->prepare("SELECT attendance_date, status FROM attendance WHERE enrollment_id = ? ORDER BY attendance_date DESC");
$stmt->bind_param("i", $enrollment_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $attendance_records[] = $row;
}
$stmt->close();

// Calculate attendance statistics
$total_sessions = count($attendance_records);
$present_count = 0;
$absent_count = 0;
$late_count = 0;

foreach ($attendance_records as $record) {
    if ($record['status'] === 'present') {
        $present_count++;
    } elseif ($record['status'] === 'absent') {
        $absent_count++;
    } elseif ($record['status'] === 'late') {
        $late_count++;
    }
}

$present_percentage = $total_sessions > 0 ? round(($present_count / $total_sessions) * 100, 2) : 0;


include 'header.php';
?>

<div class="dashboard">
    <div class="breadcrumb">
        <a href="dashboard_student.php">Dashboard</a> &gt; <span><?php echo htmlspecialchars($enrollment_details['class_name']); ?> Details</span>
    </div>

    <h2><?php echo htmlspecialchars($enrollment_details['class_code']); ?> - <?php echo htmlspecialchars($enrollment_details['class_name']); ?></h2>
    
    <?php if ($message): ?>
        <div class="message-container"><?php echo $message; ?></div>
    <?php endif; ?>

    <div class="class-details-summary class-grid">
        <div class="class-card">
            <h3>Class Information</h3>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($enrollment_details['description']); ?></p>
            <p><strong>Lecturer:</strong> <?php echo htmlspecialchars($enrollment_details['lecturer_name']); ?> (<?php echo htmlspecialchars($enrollment_details['lecturer_email']); ?>)</p>
            <p><strong>Schedule:</strong> <?php echo htmlspecialchars($enrollment_details['schedule']); ?></p>
            <p><strong>Room:</strong> <?php echo htmlspecialchars($enrollment_details['room']); ?></p>
        </div>
        <div class="class-card">
            <h3>My Progress</h3>
            <p><strong>My Grade:</strong> <?php echo htmlspecialchars($enrollment_details['grade'] ?: 'N/A'); ?></p>
            <p><strong>Attendance Rate:</strong> <?php echo $present_percentage; ?>%</p>
            <p><strong>Total Sessions:</strong> <?php echo $total_sessions; ?></p>
            <p><strong>Present:</strong> <?php echo $present_count; ?></p>
            <p><strong>Absent:</strong> <?php echo $absent_count; ?></p>
            <p><strong>Late:</strong> <?php echo $late_count; ?></p>
        </div>
    </div>

    <h3>Attendance History</h3>
    <?php if (empty($attendance_records)): ?>
        <p>No attendance records available for this class yet.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendance_records as $record): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($record['attendance_date']); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($record['status'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
