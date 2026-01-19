<?php
require_once 'config_database.php';

$success_msg = "";
$error_msg = "";

// Get activities for dropdown
$activities_query = "SELECT id, activity_name FROM activities WHERE status = 'active'";
$activities_result = $conn->query($activities_query);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $faculty = $_POST['faculty'];
    $academic_year = $_POST['academic_year'];
    $activity_id = $_POST['activity_id'];
    $join_date = $_POST['join_date'];
    $role = $_POST['role'];
    
    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO students (student_id, full_name, email, phone, faculty, academic_year, activity_id, join_date, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssiss", $student_id, $full_name, $email, $phone, $faculty, $academic_year, $activity_id, $join_date, $role);
    
    if ($stmt->execute()) {
        // Update participant count in activities table
        $update_query = "UPDATE activities SET current_participants = current_participants + 1 WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("i", $activity_id);
        $update_stmt->execute();
        $update_stmt->close();
        
        $success_msg = "Student registered successfully!";
        echo "<script>setTimeout(function(){ window.location.href = 'view_students.php'; }, 2000);</script>";
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
    <title>Add New Student - Sports Management System</title>
    <link rel="stylesheet" href="style_css.css">
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo-section">
                    <div class="logo">👥 Register Student</div>
                    <div class="subtitle">University of Vavuniya - Register Students for Activities</div>
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
                <li><a href="add_student.php" class="active">👥 Add Student</a></li>
                <li><a href="view_activities.php">📋 View Activities</a></li>
                <li><a href="view_students.php">👨‍🎓 View Students</a></li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <div class="form-container">
            <h2>👨‍🎓 Register New Student</h2>
            
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
            
            <form method="POST" action="" id="studentForm" class="validate-form">
                <div class="form-group">
                    <label for="student_id">Student ID *</label>
                    <input type="text" id="student_id" name="student_id" required 
                           placeholder="e.g., 2021/ICTS/40" pattern="\d{4}/[A-Z]+/\d+">
                </div>
                
                <div class="form-group">
                    <label for="full_name">Full Name *</label>
                    <input type="text" id="full_name" name="full_name" required 
                           placeholder="Enter full name">
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" 
                           placeholder="student@vau.ac.lk">
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" 
                           placeholder="0712345678" pattern="[0-9]{10}">
                </div>
                
                <div class="form-group">
                    <label for="faculty">Faculty *</label>
                    <select id="faculty" name="faculty" required>
                        <option value="">Select Faculty</option>
                        <option value="ICT">ICT</option>
                        <option value="Science">Science</option>
                        <option value="Management">Management</option>
                        <option value="Arts">Arts</option>
                        <option value="Engineering">Engineering</option>
                        <option value="Medicine">Medicine</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="academic_year">Academic Year *</label>
                    <select id="academic_year" name="academic_year" required>
                        <option value="">Select Year</option>
                        <option value="1st Year">1st Year</option>
                        <option value="2nd Year">2nd Year</option>
                        <option value="3rd Year">3rd Year</option>
                        <option value="4th Year">4th Year</option>
                        <option value="Graduate">Graduate</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="activity_id">Select Activity *</label>
                    <select id="activity_id" name="activity_id" required>
                        <option value="">Select Activity</option>
                        <?php
                        if ($activities_result->num_rows > 0) {
                            while($row = $activities_result->fetch_assoc()) {
                                echo "<option value='" . $row['id'] . "'>" . $row['activity_name'] . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="join_date">Join Date *</label>
                    <input type="date" id="join_date" name="join_date" required 
                           value="<?php echo date('Y-m-d'); ?>">
                </div>
                
                <div class="form-group">
                    <label for="role">Role *</label>
                    <select id="role" name="role" required>
                        <option value="participant">Participant</option>
                        <option value="team_captain">Team Captain</option>
                        <option value="coordinator">Coordinator</option>
                    </select>
                </div>
                
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn btn-success">👥 Register Student</button>
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