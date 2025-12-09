<?php
require_once '../config/config.php';
requireRole('lecturer');

$lecturer_id = $_SESSION['user_id'];
$lecturer_name = $_SESSION['username']; // Assuming username is stored in session, adjust if full_name is available

$message = '';

// Handle class creation form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_class'])) {
    $class_code = sanitize($_POST['class_code']);
    $class_name = sanitize($_POST['class_name']);
    $description = sanitize($_POST['description']);
    $capacity = (int) $_POST['capacity'];
    $schedule = sanitize($_POST['schedule']);
    $room = sanitize($_POST['room']);

    // Basic validation
    if (empty($class_code) || empty($class_name) || empty($capacity)) {
        $message = "<p class='error'>Class code, name, and max students are required.</p>";
    } else {
        // Check if class code already exists
        $stmt = $conn->prepare("SELECT id FROM classes WHERE class_code = ?");
        $stmt->bind_param("s", $class_code);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "<p class='error'>Class with this code already exists.</p>";
        } else {
            $stmt = $conn->prepare("INSERT INTO classes (class_code, name, description, lecturer_id, schedule, room, capacity) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssissi", $class_code, $class_name, $description, $lecturer_id, $schedule, $room, $capacity);

            if ($stmt->execute()) {
                $message = "<p class='success'>Class created successfully!</p>";
            } else {
                $message = "<p class='error'>Error creating class: " . $conn->error . "</p>";
            }
            $stmt->close();
        }
    }
}

// Handle class deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_class'])) {
    $class_id_to_delete = (int) $_POST['class_id'];

    // Verify that the lecturer owns this class before deleting
    $stmt = $conn->prepare("SELECT id FROM classes WHERE id = ? AND lecturer_id = ?");
    $stmt->bind_param("ii", $class_id_to_delete, $lecturer_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt_delete = $conn->prepare("DELETE FROM classes WHERE id = ?");
        $stmt_delete->bind_param("i", $class_id_to_delete);
        if ($stmt_delete->execute()) {
            $message = "<p class='success'>Class deleted successfully!</p>";
        } else {
            $message = "<p class='error'>Error deleting class: " . $conn->error . "</p>";
        }
        $stmt_delete->close();
    } else {
        $message = "<p class='error'>You do not have permission to delete this class.</p>";
    }
    $stmt->close();
}


// Get lecturer's classes from database
$classes = [];
$sql = "SELECT c.id, c.class_code, c.name, c.description, c.schedule, c.room, c.capacity, 
               COUNT(e.id) AS enrolled_students
        FROM classes c
        LEFT JOIN enrollments e ON c.id = e.class_id
        WHERE c.lecturer_id = ?
        GROUP BY c.id";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $lecturer_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $classes[] = $row;
}
$stmt->close();


include 'header.php';
?>

<div class="dashboard">
    <h2>Welcome, <?php echo htmlspecialchars($lecturer_name); ?> (Lecturer)</h2>
    <h3>My Classes</h3>
    
    <?php if ($message): ?>
        <div class="message-container"><?php echo $message; ?></div>
    <?php endif; ?>

    <button id="createClassBtn" class="btn">Create New Class</button>

    <div id="createClassForm" class="form-container" style="display: none;">
        <h3>Create New Class</h3>
        <form action="" method="POST">
            <div class="input-group">
                <label for="class_code">Class Code</label>
                <input type="text" id="class_code" name="class_code" required>
            </div>
            <div class="input-group">
                <label for="class_name">Class Name</label>
                <input type="text" id="class_name" name="class_name" required>
            </div>
            <div class="input-group">
                <label for="description">Description</label>
                <textarea id="description" name="description"></textarea>
            </div>
            <div class="input-group">
                <label for="capacity">Max Students</label>
                <input type="number" id="capacity" name="capacity" min="1" required>
            </div>
            <div class="input-group">
                <label for="schedule">Schedule (e.g., Mon/Wed 10:00-11:30)</label>
                <input type="text" id="schedule" name="schedule">
            </div>
            <div class="input-group">
                <label for="room">Room</label>
                <input type="text" id="room" name="room">
            </div>
            <button type="submit" name="create_class" class="btn btn-primary">Add Class</button>
            <button type="button" id="cancelCreateClass" class="btn btn-secondary">Cancel</button>
        </form>
    </div>

    <div class="class-grid">
        <?php if (empty($classes)): ?>
            <p>You haven't created any classes yet.</p>
        <?php else: ?>
            <?php foreach ($classes as $class): ?>
                <div class="class-card">
                    <h3><?php echo htmlspecialchars($class['class_code']); ?> - <?php echo htmlspecialchars($class['name']); ?></h3>
                    <p><?php echo htmlspecialchars($class['description']); ?></p>
                    <p><strong>Schedule:</strong> <?php echo htmlspecialchars($class['schedule']); ?></p>
                    <p><strong>Room:</strong> <?php echo htmlspecialchars($class['room']); ?></p>
                    <p><strong>Students:</strong> <?php echo htmlspecialchars($class['enrolled_students']); ?> / <?php echo htmlspecialchars($class['capacity']); ?></p>
                    <div class="class-actions">
                        <a href="class_details.php?class_id=<?php echo $class['id']; ?>" class="btn btn-sm btn-info">View Details</a>
                        <form action="" method="POST" style="display:inline-block;">
                            <input type="hidden" name="class_id" value="<?php echo $class['id']; ?>">
                            <button type="submit" name="delete_class" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this class? This action cannot be undone.');">Delete</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
    document.getElementById('createClassBtn').addEventListener('click', function() {
        document.getElementById('createClassForm').style.display = 'block';
        this.style.display = 'none';
    });

    document.getElementById('cancelCreateClass').addEventListener('click', function() {
        document.getElementById('createClassForm').style.display = 'none';
        document.getElementById('createClassBtn').style.display = 'block';
    });
</script>

</body>
</html>
