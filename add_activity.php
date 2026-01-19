<?php
require_once 'config_database.php';

$success_msg = "";
$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $activity_name = $_POST['activity_name'];
    $activity_type = $_POST['activity_type'];
    $description = $_POST['description'];
    $schedule_day = $_POST['schedule_day'];
    $schedule_time = $_POST['schedule_time'];
    $venue = $_POST['venue'];
    $coach_instructor = $_POST['coach_instructor'];
    $max_participants = $_POST['max_participants'];
    
    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO activities (activity_name, activity_type, description, schedule_day, schedule_time, venue, coach_instructor, max_participants) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssi", $activity_name, $activity_type, $description, $schedule_day, $schedule_time, $venue, $coach_instructor, $max_participants);
    
    if ($stmt->execute()) {
        $success_msg = "Activity added successfully!";
        // Reset form or redirect
        echo "<script>setTimeout(function(){ window.location.href = 'view_activities.php'; }, 2000);</script>";
    } else {
        $error_msg = "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Activity - Sports Management System</title>
    <link rel="stylesheet" href="style_css.css">
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo-section">
                    <div class="logo">🏆 Add New Activity</div>
                    <div class="subtitle">University of Vavuniya - Register New Sports or Extracurricular Activity</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <div class="navbar">
        <div class="container">
            <ul class="nav-links">
                <li><a href="index.php">🏠 Dashboard</a></li>
                <li><a href="add_activity.php" class="active">➕ Add Activity</a></li>
                <li><a href="add_student.php">👥 Add Student</a></li>
                <li><a href="view_activities.php">📋 View Activities</a></li>
                <li><a href="view_students.php">👨‍🎓 View Students</a></li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <div class="form-container">
            <h2>📝 Register New Activity</h2>
            
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
            
            <form method="POST" action="" id="activityForm" class="validate-form">
                <div class="form-group">
                    <label for="activity_name">Activity Name *</label>
                    <input type="text" id="activity_name" name="activity_name" required 
                           placeholder="e.g., Cricket, Debate Club, Music Society">
                </div>
                
                <div class="form-group">
                    <label for="activity_type">Activity Type *</label>
                    <select id="activity_type" name="activity_type" required>
                        <option value="">Select Type</option>
                        <option value="sports">Sports</option>
                        <option value="extracurricular">Extracurricular</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="3" 
                              placeholder="Brief description of the activity"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="schedule_day">Schedule Day(s) *</label>
                    <input type="text" id="schedule_day" name="schedule_day" required 
                           placeholder="e.g., Monday, Wednesday, Friday">
                </div>
                
                <div class="form-group">
                    <label for="schedule_time">Schedule Time *</label>
                    <input type="time" id="schedule_time" name="schedule_time" required>
                </div>
                
                <div class="form-group">
                    <label for="venue">Venue *</label>
                    <input type="text" id="venue" name="venue" required 
                           placeholder="e.g., University Main Ground, Auditorium A">
                </div>
                
                <div class="form-group">
                    <label for="coach_instructor">Coach/Instructor *</label>
                    <input type="text" id="coach_instructor" name="coach_instructor" required 
                           placeholder="Name of coach or instructor">
                </div>
                
                <div class="form-group">
                    <label for="max_participants">Maximum Participants *</label>
                    <input type="number" id="max_participants" name="max_participants" required 
                           min="1" max="1000" placeholder="Maximum number of participants">
                </div>
                
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn btn-success">➕ Add Activity</button>
                    <button type="reset" class="btn btn-warning">🔄 Reset Form</button>
                    <a href="index.php" class="btn btn-primary">🏠 Back to Dashboard</a>
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