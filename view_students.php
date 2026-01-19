<?php
require_once 'config_database.php';

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    
    // First, get the activity_id to update participant count
    $get_query = "SELECT activity_id FROM students WHERE id = ?";
    $stmt = $conn->prepare($get_query);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->bind_result($activity_id);
    $stmt->fetch();
    $stmt->close();
    
    // Delete the student
    $delete_query = "DELETE FROM students WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $delete_id);
    
    if ($stmt->execute()) {
        // Update participant count in activities table
        if ($activity_id) {
            $update_query = "UPDATE activities SET current_participants = current_participants - 1 WHERE id = ? AND current_participants > 0";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("i", $activity_id);
            $update_stmt->execute();
            $update_stmt->close();
        }
        $success_msg = "Student deleted successfully!";
    }
    $stmt->close();
}

// Fetch all students with activity details
$query = "SELECT s.*, a.activity_name 
          FROM students s 
          LEFT JOIN activities a ON s.activity_id = a.id 
          ORDER BY s.created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Students - Sports Management System</title>
    <link rel="stylesheet" href="style_css.css">
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo-section">
                    <div class="logo">👨‍🎓 All Students</div>
                    <div class="subtitle">University of Vavuniya - Manage Student Registrations</div>
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
                <li><a href="view_students.php" class="active">👨‍🎓 View Students</a></li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <?php if (isset($success_msg)): ?>
            <div class="alert alert-success">
                ✅ <?php echo $success_msg; ?>
            </div>
        <?php endif; ?>
        
        <div class="table-container">
            <h2>📊 All Student Registrations</h2>
            <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                <input type="text" id="searchStudents" placeholder="Search students..." 
                       style="padding: 8px; width: 300px;">
                <a href="add_student.php" class="btn btn-success">👥 Add New Student</a>
            </div>
            
            <table id="studentsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Faculty</th>
                        <th>Year</th>
                        <th>Activity</th>
                        <th>Join Date</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['id'] . "</td>";
                            echo "<td><strong>" . $row['student_id'] . "</strong></td>";
                            echo "<td>" . $row['full_name'] . "</td>";
                            echo "<td>" . $row['email'] . "</td>";
                            echo "<td>" . $row['phone'] . "</td>";
                            echo "<td>" . $row['faculty'] . "</td>";
                            echo "<td>" . $row['academic_year'] . "</td>";
                            echo "<td>" . ($row['activity_name'] ?? 'Not assigned') . "</td>";
                            echo "<td>" . $row['join_date'] . "</td>";
                            echo "<td>" . str_replace('_', ' ', ucfirst($row['role'])) . "</td>";
                            echo "<td>";
                            if ($row['status'] == 'active') {
                                echo "<span style='color: green;'>✅ Active</span>";
                            } elseif ($row['status'] == 'graduated') {
                                echo "<span style='color: blue;'>🎓 Graduated</span>";
                            } else {
                                echo "<span style='color: red;'>❌ Inactive</span>";
                            }
                            echo "</td>";
                            echo "<td class='action-buttons'>";
                            echo "<a href='edit_student.php?id=" . $row['id'] . "' class='btn btn-warning btn-sm'>✏️ Edit</a>";
                            echo "<a href='?delete_id=" . $row['id'] . "' 
                                  onclick=\"return confirm('Are you sure you want to delete this student record?')\" 
                                  class='btn btn-danger btn-sm'>🗑️ Delete</a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='12' style='text-align: center; padding: 20px;'>No students found. <a href='add_student.php'>Add your first student</a></td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        
        <div style="text-align: center; margin-top: 20px;">
            <a href="index.php" class="btn btn-primary">🏠 Back to Dashboard</a>
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
    <script>
        // Initialize search functionality
        document.getElementById('searchStudents').addEventListener('keyup', function() {
            searchTable('studentsTable', 'searchStudents');
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>