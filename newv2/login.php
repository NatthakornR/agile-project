<?php
session_start();

$dsn = "mysql:host=mariadb.vamk.fi;dbname=e2301469_;charset=utf8";
$db_username = "e2301469";
$db_password = "NZHYAuR8dEQ"; 

try {
    $pdo = new PDO($dsn, $db_username, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}

if (isset($_GET["action"]) && $_GET["action"] == "logout") {
    session_destroy();
    header("Location: index.php");
    exit();
}

if (isset($_POST["action"]) && $_POST["action"] == "login") {
    $user = $_POST["username"];
    $pass = $_POST["password"];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$user]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($userData && $pass == $userData["password"]) {
        $_SESSION["user"] = $userData;
        header("Location: login.php");
        exit();
    } else {
        $error = "Invalid credentials!";
    }
}

if (!isset($_SESSION["user"])): ?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - E-Tendering</title>
    <style>
    body {
      margin: 0; 
      padding: 0; 
      font-family: Arial, sans-serif; 
      background-color: #f5f5f5;
      display: flex; 
      justify-content: center; 
      align-items: center; 
      height: 100vh;
    }
    .login-container {
      background: #fff; 
      padding: 2rem; 
      border-radius: 8px; 
      box-shadow: 0 2px 8px rgba(0,0,0,0.2); 
      width: 320px; 
      text-align: center;
    }
    .login-container h2 {
      margin-bottom: 1rem; 
      color: #333;
    }
    .login-container input[type="text"],
    .login-container input[type="password"] {
      width: 100%; 
      padding: 0.75rem; 
      margin: 0.5rem 0; 
      border: 1px solid #ccc; 
      border-radius: 4px; 
      font-size: 1rem;
    }
    .login-container button {
      width: 100%; 
      padding: 0.75rem; 
      background-color: #c00; 
      color: #fff; 
      border: none; 
      border-radius: 4px; 
      font-size: 1rem; 
      cursor: pointer;
      margin-top: 1rem;
    }
    .login-container button:hover {
      background-color: #900;
    }
    .login-container .note {
      margin-top: 1rem; 
      font-size: 0.9rem; 
      color: #555;
    }
  </style>
</head>
<body>
    <div class="login-container"> 
    <h2>Log In</h2>
    <?php if (isset($error)) {
        echo "<p style='color:red;'>$error</p>";
    } ?>
    <form method="post"action="login.php">
    <input type="hidden" name="action" value="login">
      <input type="text" name="username" placeholder="Email (City or Company)" required>
      <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Log In</button>
    </form>
    <p class="note">Only companies and city can log in</p>
    </div>
</body>
</html>
<?php exit();endif;

$user = $_SESSION["user"];
$userType = $user["user_type"];
?>
<!DOCTYPE html>
<html>
<head>
  
    <title>E-Tendering Dashboard</title>
    <style>
    header {
      background-color: #000; 
      color: #fff; 
      display: flex; 
      justify-content: space-between; 
      align-items: center; 
      padding: 1rem 2rem;
    }
    header h1 {
      margin: 0;
    }
    header nav a {
      color: #fff;
      text-decoration: none;
      margin-left: 1rem;
      font-weight: bold;
    }
</style>
</head>
<body>
    <header>
        <h1>E-Tendering</h1>
        <nav>
        <a href="index.php">Home</a>
        </nav>
    </header>

    <h1>Welcome, <?php echo htmlspecialchars(
        $user["username"]
    ); ?> (<?php echo htmlspecialchars($userType); ?>)</h1>
    <p><a href="login.php?action=logout">Logout</a></p>
    <hr>

    <?php 
    if ($userType == "city"):
        if (isset($_POST["action"]) && $_POST["action"] == "create_tender") {
            $description = $_POST["description"];
            $bidding_price = $_POST["bidding_price"];
            $duration = $_POST["duration"];
            $document = null;
            if (
                isset($_FILES["document"]) &&
                $_FILES["document"]["error"] == 0
            ) {
                $uploadDir = "uploads/";
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $document = $uploadDir . basename($_FILES["document"]["name"]);
                move_uploaded_file($_FILES["document"]["tmp_name"], $document);
            }

            $stmt = $pdo->prepare(
                "INSERT INTO tenders (city_id, description, bidding_price, duration, document) VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $user["id"],
                $description,
                $bidding_price,
                $duration,
                $document,
            ]);
            echo "<p style='color:green;'>Tender created successfully!</p>";
        } ?>
        <h2>Create Tender</h2>
        <form method="post" action="login.php" enctype="multipart/form-data">
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
    elseif ($userType == "company"):
        if (isset($_POST["action"]) && $_POST["action"] == "apply_tender") {
            $tender_id = $_POST["tender_id"];
            $bidding_price = $_POST["bidding_price"];

            $document = null;
            if (
                isset($_FILES["document"]) &&
                $_FILES["document"]["error"] == 0
            ) {
                $uploadDir = "uploads/";
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $document = $uploadDir . basename($_FILES["document"]["name"]);
                move_uploaded_file($_FILES["document"]["tmp_name"], $document);
            }

            $stmt = $pdo->prepare(
                "INSERT INTO tender_applications (tender_id, company_id, bidding_price, document) VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([
                $tender_id,
                $user["id"],
                $bidding_price,
                $document,
            ]);
            echo "<p style='color:green;'>Application submitted successfully!</p>";
        } ?>
        <h2>Available Tenders</h2>
        <?php
        $stmt = $pdo->query("SELECT * FROM tenders ORDER BY created_at DESC");
        $tenders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($tenders) > 0) {
            foreach ($tenders as $tender) {

                echo "<div style='border:1px solid #ccc; padding:10px; margin-bottom:10px;'>";
                echo "<p><strong>Description:</strong> " .
                    htmlspecialchars($tender["description"]) .
                    "</p>";
                echo "<p><strong>Bidding Price:</strong> " .
                    htmlspecialchars($tender["bidding_price"]) .
                    "</p>";
                echo "<p><strong>Duration:</strong> " .
                    htmlspecialchars($tender["duration"]) .
                    " days</p>";
                if ($tender["document"]) {
                    echo "<p><a href='" .
                        htmlspecialchars($tender["document"]) .
                        "' target='_blank'>View Document</a></p>";
                }
                ?>
                <form method="post" action="login.php" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="apply_tender">
                    <input type="hidden" name="tender_id" value="<?php echo $tender[
                        "id"
                    ]; ?>">
                    <label>Bidding Price:</label>
                    <input type="number" step="0.01" name="bidding_price" required><br>
                    <label>Document:</label>
                    <input type="file" name="document"><br>
                    <button type="submit">Apply</button>
                </form>
                <?php echo "</div>";
            }
        } else {
            echo "<p>No tenders available at the moment.</p>";
        }

    endif; ?>
</body>
</html>
