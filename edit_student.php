<?php
require_once 'config_database.php';

// Check if ID is provided
if (!isset($_GET['id'])) {
    header("Location: view_students.php");
    exit();
}

$id = $_GET['id'];
$success_msg = "";
$error_msg = "";

// Fetch student data
$query = "SELECT * FROM students WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: view_students.php");
    exit();
}

$student = $result->fetch_assoc();
$stmt->close();

// Get activities for dropdown
$activities_query = "SELECT id, activity_name FROM activities WHERE status = 'active'";
$activities_result = $conn->query($activities_query);

// Handle form submission
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
    $status = $_POST['status'];
    
    // Get old activity_id to update participant counts
    $old_activity_id = $student['activity_id'];
    
    // Prepare and bind
    $update_stmt = $conn->prepare("UPDATE students SET student_id=?, full_name=?, email=?, phone=?, faculty=?, academic_year=?, activity_id=?, join_date=?, role=?, status=? WHERE id=?");
    $update_stmt->bind_param("ssssssisssi", $student_id, $full_name, $email, $phone, $faculty, $academic_year, $activity_id, $join_date, $role, $status, $id);
    
    if ($update_stmt->execute()) {
        // Update participant counts if activity changed
        if ($old_activity_id != $activity_id) {
            // Decrement old activity
            if ($old_activity_id) {
                $decrement_query = "UPDATE activities SET current_participants = current_participants - 1 WHERE id = ? AND current_participants > 0";
                $decrement_stmt = $conn->prepare($decrement_query);
                $decrement_stmt->bind_param("i", $old_activity_id);
                $decrement_stmt->execute();
                $decrement_stmt->close();
            }
            
            // Increment new activity
            if ($activity_id) {
                $increment_query = "UPDATE activities SET current_participants = current_participants + 1 WHERE id = ?";
                $increment_stmt = $conn->prepare($increment_query);
                $increment_stmt->bind_param("i", $activity_id);
                $increment_stmt->execute();
                $increment_stmt->close();
            }
        }
        
        $success_msg = "Student updated successfully!";
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
    <title>Edit Student - Sports Management System</title>
    <link rel="stylesheet" href="style_css.css">
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo-section">
                    <div class="logo">✏️ Edit Student</div>
                    <div class="subtitle">University of Vavuniya - Update Student Details</div>
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
            <h2>✏️ Edit Student: <?php echo $student['full_name']; ?></h2>
            
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
            
            <form method="POST" action="" id="editStudentForm" class="validate-form">
                <div class="form-group">
                    <label for="student_id">Student ID *</label>
                    <input type="text" id="student_id" name="student_id" required 
                           value="<?php echo htmlspecialchars($student['student_id']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="full_name">Full Name *</label>
                    <input type="text" id="full_name" name="full_name" required 
                           value="<?php echo htmlspecialchars($student['full_name']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo htmlspecialchars($student['email']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" 
                           value="<?php echo htmlspecialchars($student['phone']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="faculty">Faculty *</label>
                    <select id="faculty" name="faculty" required>
                        <option value="ICT" <?php echo $student['faculty'] == 'ICT' ? 'selected' : ''; ?>>ICT</option>
                        <option value="Science" <?php echo $student['faculty'] == 'Science' ? 'selected' : ''; ?>>Science</option>
                        <option value="Management" <?php echo $student['faculty'] == 'Management' ? 'selected' : ''; ?>>Management</option>
                        <option value="Arts" <?php echo $student['faculty'] == 'Arts' ? 'selected' : ''; ?>>Arts</option>
                        <option value="Engineering" <?php echo $student['faculty'] == 'Engineering' ? 'selected' : ''; ?>>Engineering</option>
                        <option value="Medicine" <?php echo $student['faculty'] == 'Medicine' ? 'selected' : ''; ?>>Medicine</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="academic_year">Academic Year *</label>
                    <select id="academic_year" name="academic_year" required>
                        <option value="1st Year" <?php echo $student['academic_year'] == '1st Year' ? 'selected' : ''; ?>>1st Year</option>
                        <option value="2nd Year" <?php echo $student['academic_year'] == '2nd Year' ? 'selected' : ''; ?>>2nd Year</option>
                        <option value="3rd Year" <?php echo $student['academic_year'] == '3rd Year' ? 'selected' : ''; ?>>3rd Year</option>
                        <option value="4th Year" <?php echo $student['academic_year'] == '4th Year' ? 'selected' : ''; ?>>4th Year</option>
                        <option value="Graduate" <?php echo $student['academic_year'] == 'Graduate' ? 'selected' : ''; ?>>Graduate</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="activity_id">Select Activity</label>
                    <select id="activity_id" name="activity_id">
                        <option value="">Not assigned</option>
                        <?php
                        if ($activities_result->num_rows > 0) {
                            $activities_result->data_seek(0);
                            while($row = $activities_result->fetch_assoc()) {
                                $selected = ($row['id'] == $student['activity_id']) ? 'selected' : '';
                                echo "<option value='" . $row['id'] . "' $selected>" . $row['activity_name'] . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="join_date">Join Date *</label>
                    <input type="date" id="join_date" name="join_date" required 
                           value="<?php echo $student['join_date']; ?>">
                </div>
                
                <div class="form-group">
                    <label for="role">Role *</label>
                    <select id="role" name="role" required>
                        <option value="participant" <?php echo $student['role'] == 'participant' ? 'selected' : ''; ?>>Participant</option>
                        <option value="team_captain" <?php echo $student['role'] == 'team_captain' ? 'selected' : ''; ?>>Team Captain</option>
                        <option value="coordinator" <?php echo $student['role'] == 'coordinator' ? 'selected' : ''; ?>>Coordinator</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="status">Status *</label>
                    <select id="status" name="status" required>
                        <option value="active" <?php echo $student['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo $student['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        <option value="graduated" <?php echo $student['status'] == 'graduated' ? 'selected' : ''; ?>>Graduated</option>
                    </select>
                </div>
                
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn btn-success">💾 Save Changes</button>
                    <button type="reset" class="btn btn-warning">🔄 Reset</button>
                    <a href="view_students.php" class="btn btn-primary">👨‍🎓 Back to Students</a>
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