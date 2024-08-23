<?php
include 'config.php'; // Include the database connection

// Handle form submission for adding new fees
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_fee'])) {
        $fee_name = $_POST['fee_name'];
        $amount = $_POST['amount'];
        $description = $_POST['description'];
        
        // Validate input
        $fee_name = filter_var($fee_name, FILTER_SANITIZE_STRING);
        $amount = filter_var($amount, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $description = filter_var($description, FILTER_SANITIZE_STRING);
        
        // Insert into database
        $sql = "INSERT INTO fees (fee_name, amount, description) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sds", $fee_name, $amount, $description);
        if ($stmt->execute()) {
            $message = "Fee added successfully!";
        } else {
            $message = "Error adding fee: " . $conn->error;
        }
    }

    // Handle form submission for editing fees
    if (isset($_POST['edit_fee'])) {
        $id = $_POST['id'];
        $fee_name = $_POST['fee_name'];
        $amount = $_POST['amount'];
        $description = $_POST['description'];
        
        // Validate input
        $fee_name = filter_var($fee_name, FILTER_SANITIZE_STRING);
        $amount = filter_var($amount, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $description = filter_var($description, FILTER_SANITIZE_STRING);
        
        // Update in database
        $sql = "UPDATE fees SET fee_name = ?, amount = ?, description = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdsi", $fee_name, $amount, $description, $id);
        if ($stmt->execute()) {
            $message = "Fee updated successfully!";
        } else {
            $message = "Error updating fee: " . $conn->error;
        }
    }
}

// Handle form submission for deleting fees
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql = "DELETE FROM fees WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $message = "Fee deleted successfully!";
    } else {
        $message = "Error deleting fee: " . $conn->error;
    }
}

// Search and Filter
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
    $search_query = filter_var($search_query, FILTER_SANITIZE_STRING);
}

// Pagination
$limit = 10; // Number of fees per page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Fetch fees with search and pagination
$sql = "SELECT * FROM fees WHERE fee_name LIKE ? OR description LIKE ? LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$search_param = "%$search_query%";
$stmt->bind_param("ssii", $search_param, $search_param, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

// Fetch total number of fees for pagination
$total_sql = "SELECT COUNT(*) AS total FROM fees WHERE fee_name LIKE ? OR description LIKE ?";
$total_stmt = $conn->prepare($total_sql);
$total_stmt->bind_param("ss", $search_param, $search_param);
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_row = $total_result->fetch_assoc();
$total_fees = $total_row['total'];
$total_pages = ceil($total_fees / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fees Management</title>
    <link rel="stylesheet" href="../css/style2.css">
    <style>
        /* Additional CSS for fees page */
        .fees-container {
            display: flex;
            flex-direction: column;
            padding: 20px;
        }

        .fees-form, .fees-list {
            margin-bottom: 20px;
        }

        .fees-form input, .fees-form textarea, .fees-form button {
            display: block;
            margin: 10px 0;
            padding: 10px;
            width: 100%;
        }

        .fees-list table {
            width: 100%;
            border-collapse: collapse;
        }

        .fees-list table, .fees-list th, .fees-list td {
            border: 1px solid #ddd;
        }

        .fees-list th, .fees-list td {
            padding: 10px;
            text-align: left;
        }

        .fees-list th {
            background-color: #f4f4f4;
        }

        .message {
            padding: 10px;
            color: green;
            font-weight: bold;
        }

        .pagination {
            margin: 10px 0;
        }

        .pagination a {
            padding: 8px 12px;
            border: 1px solid #ddd;
            margin: 0 2px;
            text-decoration: none;
            color: #007bff;
        }

        .pagination a.active {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <img src="../assets/images/log.png" alt="School Logo">
            </div>
            <button class="nav-toggle" onclick="toggleNav()">☰</button>
            <nav class="nav-links" id="nav-links">
                <ul>
                    <li><a href="HomePage.php">Home</a></li>
                    <li><a href="student.php">Students</a></li>
                    <li><a href="teacher.php">Teachers</a></li>
                    <li><a href="course.php">Courses</a></li>
                    <li><a href="attendance.php">Attendance</a></li>
                    <li><a href="calendar.php">Calendar</a></li>
                    <li><a href="notifications.php">Notifications</a></li>
                    <li><a href="settings.php">Settings</a></li>
                    <li><a href="fees.php" class="active">Fees</a></li>
                    <li><a href="results.php">Results</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="fees-container">
            <?php if (isset($message)): ?>
                <div class="message"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <!-- Add New Fee -->
            <div class="fees-form">
                <h2>Add New Fee</h2>
                <form action="" method="post">
                    <label for="fee_name">Fee Name:</label>
                    <input type="text" id="fee_name" name="fee_name" required>

                    <label for="amount">Amount:</label>
                    <input type="number" id="amount" name="amount" step="0.01" required>

                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows="4"></textarea>

                    <button type="submit" name="add_fee">Save Fee</button>
                </form>
            </div>

            <!-- Edit Fee (Show if editing) -->
            <?php if (isset($_GET['edit_id'])): ?>
                <?php
                $edit_id = $_GET['edit_id'];
                $sql = "SELECT * FROM fees WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $edit_id);
                $stmt->execute();
                $edit_result = $stmt->get_result();
                $edit_fee = $edit_result->fetch_assoc();
                ?>
                <div class="fees-form">
                    <h2>Edit Fee</h2>
                    <form action="" method="post">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($edit_fee['id']); ?>">

                        <label for="fee_name">Fee Name:</label>
                        <input type="text" id="fee_name" name="fee_name" value="<?php echo htmlspecialchars($edit_fee['fee_name']); ?>" required>

                        <label for="amount">Amount:</label>
                        <input type="number" id="amount" name="amount" value="<?php echo htmlspecialchars($edit_fee['amount']); ?>" step="0.01" required>

                        <label for="description">Description:</label>
                        <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($edit_fee['description']); ?></textarea>

                        <button type="submit" name="edit_fee">Update Fee</button>
                    </form>
                </div>
            <?php endif; ?>

            <!-- Search and Filter -->
            <div class="search-filter">
                <form action="" method="get">
                    <label for="search">Search Fees:</label>
                    <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search_query); ?>">
                    <button type="submit">Search</button>
                </form>
            </div>

            <!-- Fees List -->
            <div class="fees-list">
                <h2>Existing Fees</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fee Name</th>
                            <th>Amount</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['fee_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['amount']); ?></td>
                                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                                    <td>
                                        <a href="fees.php?edit_id=<?php echo htmlspecialchars($row['id']); ?>">Edit</a> | 
                                        <a href="fees.php?delete_id=<?php echo htmlspecialchars($row['id']); ?>" onclick="return confirm('Are you sure you want to delete this fee?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">No fees found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="fees.php?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search_query); ?>">« Prev</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="fees.php?page=<?php echo $i; ?>&search=<?php echo urlencode($search_query); ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="fees.php?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search_query); ?>">Next »</a>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>
    <footer>
    <div class="footer-content">
            <p class="contact-heading">Contact Information:</p>
            <ul>
                <li>Phone: +237650242757, +237650443410</li>
                <li>Address: Opposite Njeiforbi, Molyko, Buea</li>
                    <li>P.O. Box 318</li>
                </ul>
       </div> 
    </footer>

    <script>
        function toggleNav() {
            const nav = document.getElementById('nav-links');
            nav.classList.toggle('show');
        }
    </script>
</body>
</html>
