<?php
session_start();

// Database connection settings
$dsn = 'mysql:host=mariadb.vamk.fi;dbname=e2301469_;charset=utf8';
$db_username = 'e2301469';  
$db_password = 'NZHYAuR8dEQ';// update with your DB password

try {
    $pdo = new PDO($dsn, $db_username, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}

// Handle logout request
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("Location: backend.php");
    exit;
}

// Handle login form submission
if (isset($_POST['action']) && $_POST['action'] == 'login') {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$user]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($userData && password_verify($pass, $userData['password'])) {
        $_SESSION['user'] = $userData;
        header("Location: backend.php");
        exit;
    } else {
        $error = "Invalid credentials!";
    }
}

// If the user is not logged in, show the login form.
if (!isset($_SESSION['user'])):
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - E-Tendering</title>
</head>
<body>
    <h2>Login</h2>
    <?php if(isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
    <form method="post" action="backend.php">
        <input type="hidden" name="action" value="login">
        <label>Username:</label> <input type="text" name="username" required><br>
        <label>Password:</label> <input type="password" name="password" required><br>
        <button type="submit">Login</button>
    </form>
</body>
</html>
<?php
exit;
endif;

// Get the logged-in user's data
$user = $_SESSION['user'];
$userType = $user['user_type'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>E-Tendering Dashboard</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?> (<?php echo htmlspecialchars($userType); ?>)</h1>
    <p><a href="backend.php?action=logout">Logout</a></p>
    <hr>

    <?php
    // If user is a city, show tender creation form and process submissions.
    if ($userType == 'city'):
        if (isset($_POST['action']) && $_POST['action'] == 'create_tender') {
            $description = $_POST['description'];
            $bidding_price = $_POST['bidding_price'];
            $duration = $_POST['duration'];
            
            // Handle document upload (if any)
            $document = null;
            if (isset($_FILES['document']) && $_FILES['document']['error'] == 0) {
                $uploadDir = 'uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $document = $uploadDir . basename($_FILES['document']['name']);
                move_uploaded_file($_FILES['document']['tmp_name'], $document);
            }
            
            $stmt = $pdo->prepare("INSERT INTO tenders (city_id, description, bidding_price, duration, document) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$user['id'], $description, $bidding_price, $duration, $document]);
            echo "<p style='color:green;'>Tender created successfully!</p>";
        }
    ?>
        <h2>Create Tender</h2>
        <form method="post" action="backend.php" enctype="multipart/form-data">
            <input type="hidden" name="action" value="create_tender">
            <label>Description:</label><br>
            <textarea name="description" required></textarea><br>
            <label>Bidding Price:</label>
            <input type="number" step="0.01" name="bidding_price" required><br>
            <label>Duration (days):</label>
            <input type="number" name="duration" required><br>
            <label>Document:</label>
            <input type="file" name="document"><br>
            <button type="submit">Create Tender</button>
        </form>
    <?php
    // If user is a company, show available tenders with application form.
    elseif ($userType == 'company'):
        if (isset($_POST['action']) && $_POST['action'] == 'apply_tender') {
            $tender_id = $_POST['tender_id'];
            $bidding_price = $_POST['bidding_price'];
            
            // Handle document upload (if any)
            $document = null;
            if (isset($_FILES['document']) && $_FILES['document']['error'] == 0) {
                $uploadDir = 'uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $document = $uploadDir . basename($_FILES['document']['name']);
                move_uploaded_file($_FILES['document']['tmp_name'], $document);
            }
            
            $stmt = $pdo->prepare("INSERT INTO tender_applications (tender_id, company_id, bidding_price, document) VALUES (?, ?, ?, ?)");
            $stmt->execute([$tender_id, $user['id'], $bidding_price, $document]);
            echo "<p style='color:green;'>Application submitted successfully!</p>";
        }
    ?>
        <h2>Available Tenders</h2>
        <?php
        // Fetch and display all tenders (customize the query as needed, e.g., only active tenders)
        $stmt = $pdo->query("SELECT * FROM tenders ORDER BY created_at DESC");
        $tenders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($tenders) > 0) {
            foreach ($tenders as $tender) {
                echo "<div style='border:1px solid #ccc; padding:10px; margin-bottom:10px;'>";
                echo "<p><strong>Description:</strong> " . htmlspecialchars($tender['description']) . "</p>";
                echo "<p><strong>Bidding Price:</strong> " . htmlspecialchars($tender['bidding_price']) . "</p>";
                echo "<p><strong>Duration:</strong> " . htmlspecialchars($tender['duration']) . " days</p>";
                if ($tender['document']) {
                    echo "<p><a href='" . htmlspecialchars($tender['document']) . "' target='_blank'>View Document</a></p>";
                }
                ?>
                <form method="post" action="backend.php" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="apply_tender">
                    <input type="hidden" name="tender_id" value="<?php echo $tender['id']; ?>">
                    <label>Bidding Price:</label>
                    <input type="number" step="0.01" name="bidding_price" required><br>
                    <label>Document:</label>
                    <input type="file" name="document"><br>
                    <button type="submit">Apply</button>
                </form>
                <?php
                echo "</div>";
            }
        } else {
            echo "<p>No tenders available at the moment.</p>";
        }
    endif;
    ?>
</body>
</html>
