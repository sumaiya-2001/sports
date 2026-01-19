<?php
require_once 'config_database.php';

// Check if ID is provided
if (!isset($_GET['id'])) {
    header("Location: view_activities.php");
    exit();
}

$id = $_GET['id'];
$success_msg = "";
$error_msg = "";

// Fetch activity data
$query = "SELECT * FROM activities WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: view_activities.php");
    exit();
}

$activity = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $activity_name = $_POST['activity_name'];
    $activity_type = $_POST['activity_type'];
    $description = $_POST['description'];
    $schedule_day = $_POST['schedule_day'];
    $schedule_time = $_POST['schedule_time'];
    $venue = $_POST['venue'];
    $coach_instructor = $_POST['coach_instructor'];
    $max_participants = $_POST['max_participants'];
    $status = $_POST['status'];
    
    // Prepare and bind
    $update_stmt = $conn->prepare("UPDATE activities SET activity_name=?, activity_type=?, description=?, schedule_day=?, schedule_time=?, venue=?, coach_instructor=?, max_participants=?, status=? WHERE id=?");
    $update_stmt->bind_param("sssssssisi", $activity_name, $activity_type, $description, $schedule_day, $schedule_time, $venue, $coach_instructor, $max_participants, $status, $id);
    
    if ($update_stmt->execute()) {
        $success_msg = "Activity updated successfully!";
    } else {
        $error_msg = "Error: " . $update_stmt->error;
    }
    $update_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Activity - Sports Management System</title>
    <link rel="stylesheet" href="style_css.css">
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo-section">
                    <div class="logo">✏️ Edit Activity</div>
                    <div class="subtitle">University of Vavuniya - Update Activity Details</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <div class="navbar">
        <div class="container">
            <ul class="nav-links">
                <li><a href="index.php">🏠 Dashboard</a></li>
                <li><a href="add_activity.php">➕ Add Activity</a></li>
                <li><a href="add_student.php">👥 Add Student</a></li>
                <li><a href="view_activities.php">📋 View Activities</a></li>
                <li><a href="view_students.php">👨‍🎓 View Students</a></li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <div class="form-container">
            <h2>✏️ Edit Activity: <?php echo $activity['activity_name']; ?></h2>
            
            <?php if ($success_msg): ?>
                <div class="alert alert-success">
                    ✅ <?php echo $success_msg; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error_msg): ?>
                <div class="alert alert-error">
                    ❌ <?php echo $error_msg; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" id="editActivityForm" class="validate-form">
                <div class="form-group">
                    <label for="activity_name">Activity Name *</label>
                    <input type="text" id="activity_name" name="activity_name" required 
                           value="<?php echo htmlspecialchars($activity['activity_name']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="activity_type">Activity Type *</label>
                    <select id="activity_type" name="activity_type" required>
                        <option value="sports" <?php echo $activity['activity_type'] == 'sports' ? 'selected' : ''; ?>>Sports</option>
                        <option value="extracurricular" <?php echo $activity['activity_type'] == 'extracurricular' ? 'selected' : ''; ?>>Extracurricular</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="3"><?php echo htmlspecialchars($activity['description']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="schedule_day">Schedule Day(s) *</label>
                    <input type="text" id="schedule_day" name="schedule_day" required 
                           value="<?php echo htmlspecialchars($activity['schedule_day']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="schedule_time">Schedule Time *</label>
                    <input type="time" id="schedule_time" name="schedule_time" required 
                           value="<?php echo $activity['schedule_time']; ?>">
                </div>
                
                <div class="form-group">
                    <label for="venue">Venue *</label>
                    <input type="text" id="venue" name="venue" required 
                           value="<?php echo htmlspecialchars($activity['venue']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="coach_instructor">Coach/Instructor *</label>
                    <input type="text" id="coach_instructor" name="coach_instructor" required 
                           value="<?php echo htmlspecialchars($activity['coach_instructor']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="max_participants">Maximum Participants *</label>
                    <input type="number" id="max_participants" name="max_participants" required 
                           min="1" max="1000" value="<?php echo $activity['max_participants']; ?>">
                </div>
                
                <div class="form-group">
                    <label for="current_participants">Current Participants</label>
                    <input type="number" id="current_participants" name="current_participants" 
                           value="<?php echo $activity['current_participants']; ?>" readonly>
                </div>
                
                <div class="form-group">
                    <label for="status">Status *</label>
                    <select id="status" name="status" required>
                        <option value="active" <?php echo $activity['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo $activity['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        <option value="full" <?php echo $activity['status'] == 'full' ? 'selected' : ''; ?>>Full</option>
                    </select>
                </div>
                
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn btn-success">💾 Save Changes</button>
                    <button type="reset" class="btn btn-warning">🔄 Reset</button>
                    <a href="view_activities.php" class="btn btn-primary">📋 Back to Activities</a>
                    <a href="index.php" class="btn">🏠 Dashboard</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="container">
            <div class="footer-content">
                <p>Sports & Extracurricular Activity Management System</p>
                <p>University of Vavuniya</p>
                <div class="copyright">
                    &copy; <?php echo date('Y'); ?> All Rights Reserved
                </div>
            </div>
        </div>
    </div>

    <script src="script_js.js"></script>
</body>
</html>
<?php $conn->close(); ?>