<?php
require_once '../config/config.php';
requireRole('student');

$student_id = $_SESSION['user_id'];
$student_name = $_SESSION['username'];

$message = '';
if (isset($_SESSION['success_message'])) {
    $message = "<p class='success'>" . $_SESSION['success_message'] . "</p>";
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    $message = "<p class='error'>" . $_SESSION['error_message'] . "</p>";
    unset($_SESSION['error_message']);
}

// Handle class enrollment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enroll_class'])) {
    $class_id_to_enroll = (int)$_POST['class_id'];

    // Check if student is already enrolled
    $stmt = $conn->prepare("SELECT id FROM enrollments WHERE student_id = ? AND class_id = ?");
    $stmt->bind_param("ii", $student_id, $class_id_to_enroll);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['error_message'] = "You are already enrolled in this class.";
    } else {
        // Check if class is full
        $stmt_capacity = $conn->prepare("SELECT capacity FROM classes WHERE id = ?");
        $stmt_capacity->bind_param("i", $class_id_to_enroll);
        $stmt_capacity->execute();
        $result_capacity = $stmt_capacity->get_result();
        $class_capacity = $result_capacity->fetch_assoc()['capacity'];
        $stmt_capacity->close();

        $stmt_enrolled_count = $conn->prepare("SELECT COUNT(id) AS enrolled_count FROM enrollments WHERE class_id = ?");
        $stmt_enrolled_count->bind_param("i", $class_id_to_enroll);
        $stmt_enrolled_count->execute();
        $result_enrolled_count = $stmt_enrolled_count->get_result();
        $enrolled_count = $result_enrolled_count->fetch_assoc()['enrolled_count'];
        $stmt_enrolled_count->close();

        if ($enrolled_count >= $class_capacity) {
            $_SESSION['error_message'] = "This class is full. Cannot enroll.";
        } else {
            $stmt = $conn->prepare("INSERT INTO enrollments (student_id, class_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $student_id, $class_id_to_enroll);
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Successfully enrolled in the class!";
            } else {
                $_SESSION['error_message'] = "Error enrolling in class: " . $conn->error;
            }
            $stmt->close();
        }
    }
    header("Location: dashboard_student.php");
    exit();
}

// Handle drop class
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['drop_class'])) {
    $enrollment_id_to_drop = (int)$_POST['enrollment_id'];

    // Verify that the student owns this enrollment before dropping
    $stmt = $conn->prepare("SELECT e.id FROM enrollments e JOIN classes c ON e.class_id = c.id WHERE e.id = ? AND e.student_id = ?");
    $stmt->bind_param("ii", $enrollment_id_to_drop, $student_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt_delete = $conn->prepare("DELETE FROM enrollments WHERE id = ?");
        $stmt_delete->bind_param("i", $enrollment_id_to_drop);
        if ($stmt_delete->execute()) {
            $_SESSION['success_message'] = "Successfully dropped the class.";
        } else {
            $_SESSION['error_message'] = "Error dropping class: " . $conn->error;
        }
        $stmt_delete->close();
    } else {
        $_SESSION['error_message'] = "You do not have permission to drop this class.";
    }
    $stmt->close();
    header("Location: dashboard_student.php");
    exit();
}


// Get student's enrolled classes
$enrolled_classes = [];
$sql_enrolled = "SELECT e.id AS enrollment_id, c.id AS class_id, c.class_code, c.name, c.description, 
                        l.full_name AS lecturer_name, c.schedule, c.room, e.grade,
                        (SELECT COUNT(a.id) FROM attendance a WHERE a.enrollment_id = e.id AND a.status = 'present') AS present_count,
                        (SELECT COUNT(a.id) FROM attendance a WHERE a.enrollment_id = e.id AND a.status = 'absent') AS absent_count,
                        (SELECT COUNT(a.id) FROM attendance a WHERE a.enrollment_id = e.id AND a.status = 'late') AS late_count,
                        (SELECT COUNT(a.id) FROM attendance a WHERE a.enrollment_id = e.id) AS total_attendance_records
                 FROM enrollments e
                 JOIN classes c ON e.class_id = c.id
                 JOIN users l ON c.lecturer_id = l.id
                 WHERE e.student_id = ?";
$stmt_enrolled = $conn->prepare($sql_enrolled);
$stmt_enrolled->bind_param("i", $student_id);
$stmt_enrolled->execute();
$result_enrolled = $stmt_enrolled->get_result();
while ($row = $result_enrolled->fetch_assoc()) {
    $enrolled_classes[] = $row;
}
$stmt_enrolled->close();

// Get available classes (not enrolled, not full)
$available_classes = [];
$sql_available = "SELECT c.id AS class_id, c.class_code, c.name, c.description, 
                         l.full_name AS lecturer_name, c.schedule, c.room, c.capacity,
                         COUNT(e.id) AS enrolled_students_count
                  FROM classes c
                  JOIN users l ON c.lecturer_id = l.id
                  LEFT JOIN enrollments e ON c.id = e.class_id
                  WHERE c.id NOT IN (SELECT class_id FROM enrollments WHERE student_id = ?)
                  GROUP BY c.id
                  HAVING enrolled_students_count < c.capacity OR c.capacity = 0"; // capacity 0 means unlimited
$stmt_available = $conn->prepare($sql_available);
$stmt_available->bind_param("i", $student_id);
$stmt_available->execute();
$result_available = $stmt_available->get_result();
while ($row = $result_available->fetch_assoc()) {
    $available_classes[] = $row;
}
$stmt_available->close();


include 'header.php';
?>

<div class="dashboard">
    <h2>Welcome, <?php echo htmlspecialchars($student_name); ?> (Student)</h2>
    
    <?php if ($message): ?>
        <div class="message-container"><?php echo $message; ?></div>
    <?php endif; ?>

    <h3>My Enrolled Classes</h3>
    <?php if (empty($enrolled_classes)): ?>
        <p>You are not currently enrolled in any classes.</p>
    <?php else: ?>
        <div class="class-grid">
            <?php foreach ($enrolled_classes as $class): ?>
                <div class="class-card">
                    <h3><?php echo htmlspecialchars($class['class_code']); ?> - <?php echo htmlspecialchars($class['name']); ?></h3>
                    <p><?php echo htmlspecialchars($class['description']); ?></p>
                    <p><strong>Lecturer:</strong> <?php echo htmlspecialchars($class['lecturer_name']); ?></p>
                    <p><strong>Schedule:</strong> <?php echo htmlspecialchars($class['schedule']); ?></p>
                    <p><strong>Room:</strong> <?php echo htmlspecialchars($class['room']); ?></p>
                    <p><strong>Grade:</strong> <?php echo htmlspecialchars($class['grade'] ?: 'N/A'); ?></p>
                    <p><strong>Attendance:</strong> 
                        <?php
                        if ($class['total_attendance_records'] > 0) {
                            $attendance_rate = ($class['present_count'] / $class['total_attendance_records']) * 100;
                            echo round($attendance_rate, 2) . "% (" . $class['present_count'] . "/" . $class['total_attendance_records'] . ")";
                        } else {
                            echo "N/A";
                        }
                        ?>
                    </p>
                    <div class="class-actions">
                        <form action="" method="POST" style="display:inline-block;">
                            <input type="hidden" name="enrollment_id" value="<?php echo $class['enrollment_id']; ?>">
                            <button type="submit" name="drop_class" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to drop this class?');">Drop Class</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <h3>Available Classes</h3>
    <?php if (empty($available_classes)): ?>
        <p>No new classes are currently available for enrollment.</p>
    <?php else: ?>
        <div class="class-grid">
            <?php foreach ($available_classes as $class): ?>
                <div class="class-card">
                    <h3><?php echo htmlspecialchars($class['class_code']); ?> - <?php echo htmlspecialchars($class['name']); ?></h3>
                    <p><?php echo htmlspecialchars($class['description']); ?></p>
                    <p><strong>Lecturer:</strong> <?php echo htmlspecialchars($class['lecturer_name']); ?></p>
                    <p><strong>Schedule:</strong> <?php echo htmlspecialchars($class['schedule']); ?></p>
                    <p><strong>Room:</strong> <?php echo htmlspecialchars($class['room']); ?></p>
                    <p><strong>Students:</strong> <?php echo htmlspecialchars($class['enrolled_students_count']); ?> / <?php echo htmlspecialchars($class['capacity']); ?></p>
                    <div class="class-actions">
                        <form action="" method="POST">
                            <input type="hidden" name="class_id" value="<?php echo $class['class_id']; ?>">
                            <button type="submit" name="enroll_class" class="btn btn-sm btn-primary">Enroll</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
