<?php
require_once 'config_database.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sports Management System - University of Vavuniya</title>
    <link rel="stylesheet" href="style_css.css">
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo-section">
                    <div class="logo">🏆 Sports Management System</div>
                    <div class="subtitle">University of Vavuniya - Tracking Excellence in Sports & Activities</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <div class="navbar">
        <div class="container">
            <ul class="nav-links">
                <li><a href="index.php" class="active">🏠 Dashboard</a></li>
                <li><a href="add_activity.php">➕ Add Activity</a></li>
                <li><a href="add_student.php">👥 Add Student</a></li>
                <li><a href="view_activities.php">📋 View Activities</a></li>
                <li><a href="view_students.php">👨‍🎓 View Students</a></li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <!-- Dashboard Stats -->
        <div class="dashboard">
            <?php
            // Get total activities
            $activity_query = "SELECT COUNT(*) as total FROM activities";
            $activity_result = $conn->query($activity_query);
            $activity_count = $activity_result->fetch_assoc()['total'];

            // Get total students
            $student_query = "SELECT COUNT(*) as total FROM students";
            $student_result = $conn->query($student_query);
            $student_count = $student_result->fetch_assoc()['total'];

            // Get sports count
            $sports_query = "SELECT COUNT(*) as total FROM activities WHERE activity_type = 'sports'";
            $sports_result = $conn->query($sports_query);
            $sports_count = $sports_result->fetch_assoc()['total'];

            // Get extracurricular count
            $extra_query = "SELECT COUNT(*) as total FROM activities WHERE activity_type = 'extracurricular'";
            $extra_result = $conn->query($extra_query);
            $extra_count = $extra_result->fetch_assoc()['total'];
            ?>

            <div class="card">
                <h3>Total Activities</h3>
                <p style="font-size: 2rem; color: #3498db;"><?php echo $activity_count; ?></p>
                <p>Sports & Extracurricular Activities</p>
            </div>

            <div class="card">
                <h3>Total Students</h3>
                <p style="font-size: 2rem; color: #2ecc71;"><?php echo $student_count; ?></p>
                <p>Registered Participants</p>
            </div>

            <div class="card">
                <h3>Sports Activities</h3>
                <p style="font-size: 2rem; color: #e74c3c;"><?php echo $sports_count; ?></p>
                <p>Cricket, Football, Basketball, etc.</p>
            </div>

            <div class="card">
                <h3>Extracurricular</h3>
                <p style="font-size: 2rem; color: #f39c12;"><?php echo $extra_count; ?></p>
                <p>Debate, Music, Clubs, etc.</p>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="table-container">
            <h2>Recent Activities</h2>
            <input type="text" id="searchActivities" placeholder="Search activities..." style="padding: 8px; margin-bottom: 10px; width: 300px;">
            <table id="activitiesTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Activity Name</th>
                        <th>Type</th>
                        <th>Schedule</th>
                        <th>Venue</th>
                        <th>Participants</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $recent_query = "SELECT * FROM activities ORDER BY created_at DESC LIMIT 10";
                    $recent_result = $conn->query($recent_query);
                    
                    if ($recent_result->num_rows > 0) {
                        while($row = $recent_result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['id'] . "</td>";
                            echo "<td>" . $row['activity_name'] . "</td>";
                            echo "<td>" . ucfirst($row['activity_type']) . "</td>";
                            echo "<td>" . $row['schedule_day'] . " at " . $row['schedule_time'] . "</td>";
                            echo "<td>" . $row['venue'] . "</td>";
                            echo "<td>" . $row['current_participants'] . "/" . $row['max_participants'] . "</td>";
                            echo "<td><span style='color: " . ($row['status'] == 'active' ? 'green' : ($row['status'] == 'full' ? 'orange' : 'red')) . ";'>" . ucfirst($row['status']) . "</span></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No activities found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Recent Students -->
        <div class="table-container">
            <h2>Recent Student Registrations</h2>
            <input type="text" id="searchStudents" placeholder="Search students..." style="padding: 8px; margin-bottom: 10px; width: 300px;">
            <table id="studentsTable">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Full Name</th>
                        <th>Faculty</th>
                        <th>Activity</th>
                        <th>Join Date</th>
                        <th>Role</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $students_query = "SELECT s.*, a.activity_name FROM students s 
                                     LEFT JOIN activities a ON s.activity_id = a.id 
                                     ORDER BY s.created_at DESC LIMIT 10";
                    $students_result = $conn->query($students_query);
                    
                    if ($students_result->num_rows > 0) {
                        while($row = $students_result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['student_id'] . "</td>";
                            echo "<td>" . $row['full_name'] . "</td>";
                            echo "<td>" . $row['faculty'] . "</td>";
                            echo "<td>" . ($row['activity_name'] ?? 'Not assigned') . "</td>";
                            echo "<td>" . $row['join_date'] . "</td>";
                            echo "<td>" . str_replace('_', ' ', ucfirst($row['role'])) . "</td>";
                            echo "<td><span style='color: " . ($row['status'] == 'active' ? 'green' : ($row['status'] == 'graduated' ? 'blue' : 'red')) . ";'>" . ucfirst($row['status']) . "</span></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No students found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
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

        document.getElementById('searchStudents').addEventListener('keyup', function() {
            searchTable('studentsTable', 'searchStudents');
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>