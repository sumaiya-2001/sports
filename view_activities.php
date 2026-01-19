<?php
require_once 'config_database.php';

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_query = "DELETE FROM activities WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $success_msg = "Activity deleted successfully!";
    }
    $stmt->close();
}

// Fetch all activities
$query = "SELECT * FROM activities ORDER BY created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Activities - Sports Management System</title>
    <link rel="stylesheet" href="style_css.css">
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo-section">
                    <div class="logo">📋 All Activities</div>
                    <div class="subtitle">University of Vavuniya - Manage Sports & Extracurricular Activities</div>
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
                <li><a href="view_activities.php" class="active">📋 View Activities</a></li>
                <li><a href="view_students.php">👨‍🎓 View Students</a></li>
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
            <h2>📊 All Activities List</h2>
            <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                <input type="text" id="searchActivities" placeholder="Search activities..." 
                       style="padding: 8px; width: 300px;">
                <a href="add_activity.php" class="btn btn-success">➕ Add New Activity</a>
            </div>
            
            <table id="activitiesTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Activity Name</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Schedule</th>
                        <th>Venue</th>
                        <th>Participants</th>
                        <th>Coach</th>
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
                            echo "<td><strong>" . $row['activity_name'] . "</strong></td>";
                            echo "<td><span class='badge'>" . ucfirst($row['activity_type']) . "</span></td>";
                            echo "<td>" . substr($row['description'], 0, 50) . (strlen($row['description']) > 50 ? "..." : "") . "</td>";
                            echo "<td>" . $row['schedule_day'] . "<br><small>" . $row['schedule_time'] . "</small></td>";
                            echo "<td>" . $row['venue'] . "</td>";
                            echo "<td>" . $row['current_participants'] . "/" . $row['max_participants'] . "</td>";
                            echo "<td>" . $row['coach_instructor'] . "</td>";
                            echo "<td>";
                            if ($row['status'] == 'active') {
                                echo "<span style='color: green;'>✅ Active</span>";
                            } elseif ($row['status'] == 'full') {
                                echo "<span style='color: orange;'>🟡 Full</span>";
                            } else {
                                echo "<span style='color: red;'>❌ Inactive</span>";
                            }
                            echo "</td>";
                            echo "<td class='action-buttons'>";
                            echo "<a href='edit_activity.php?id=" . $row['id'] . "' class='btn btn-warning btn-sm'>✏️ Edit</a>";
                            echo "<a href='?delete_id=" . $row['id'] . "' 
                                  onclick=\"return confirm('Are you sure you want to delete this activity?\\nAll student registrations for this activity will be affected.')\" 
                                  class='btn btn-danger btn-sm'>🗑️ Delete</a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='10' style='text-align: center; padding: 20px;'>No activities found. <a href='add_activity.php'>Add your first activity</a></td></tr>";
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
        document.getElementById('searchActivities').addEventListener('keyup', function() {
            searchTable('activitiesTable', 'searchActivities');
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>