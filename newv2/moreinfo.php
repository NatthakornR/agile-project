<?php
session_start();
$dsn = 'mysql:host=mariadb.vamk.fi;dbname=e2301469_;charset=utf8';
$db_username = 'e2301469';  
$db_password = 'NZHYAuR8dEQ';

try {
    $pdo = new PDO($dsn, $db_username, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}

// Get the tender_id from the URL query string
if (isset($_GET['tender'])) {
    $tender_id = intval($_GET['tender']);

    // Fetch tender details from the tenders table

    $stmt = $pdo->prepare("SELECT * FROM tenders WHERE id = $tender_id");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $tender_result = $result;

    $tender = $tender_result;

    // Fetch companies that applied for this tender
    $stmt = $pdo->prepare("SELECT * FROM tender_applications WHERE tender_id = $tender_id");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $companies_result = $result;

    $companies = $companies_result;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>E-Tendering - Tender Info</title>
  <style>
    .register-container {
      background: #fff;
      padding: 2rem;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.2);
      width: 400px;
      box-sizing: border-box;
    }
    .register-container h2 {
      margin-top: 0;
      margin-bottom: 1rem;
      text-align: center;
    }
    .register-container label {
      display: block;
      margin-top: 1rem;
      font-weight: bold;
    }
    .register-container input[type="text"],
    .register-container input[type="number"],
    .register-container input[type="file"] {
      width: 100%;
      padding: 0.5rem;
      margin-top: 0.5rem;
      border: 1px solid #ccc;
      border-radius: 4px;
      box-sizing: border-box;
    }
    .register-container button {
      margin-top: 1.5rem;
      width: 100%;
      padding: 0.75rem;
      background-color: #c00;
      color: #fff;
      border: none;
      border-radius: 4px;
      font-size: 1rem;
      cursor: pointer;
    }
    .register-container button:hover {
      background-color: #900;
    }
    .back-link {
      display: block;
      text-align: center;
      margin-top: 1rem;
      text-decoration: none;
      color: #333;
    }
  </style>
</head>
<body>
  <header>
    <h1>E-Tendering</h1>
    <nav>
      <a href="home.html">Home</a>
    </nav>
  </header>

  <div class="container">
    <!-- Show the tender details dynamically -->
    <h2><?php echo htmlspecialchars($tender['tender_name']); ?></h2>
    <div class="tender-details">
      <p><strong>Bidding price:</strong> <?php echo htmlspecialchars($tender['bidding_price']); ?></p>
      <p><strong>Winning company:</strong> <?php echo htmlspecialchars($tender['winning_company']); ?></p>
      <p><strong>Bidding price of winning company:</strong> <?php echo htmlspecialchars($tender['winning_price']); ?></p>
      <p><strong>Information about Tender:</strong> <?php echo htmlspecialchars($tender['tender_info']); ?></p>
    </div>

    <!-- List of companies that have applied -->
    <div class="company-list">
      <h3>Companies Applied</h3>
      <?php if (count($companies) > 0): ?>
        <ul>
          <?php foreach ($companies as $company): ?>
            <li><?php echo htmlspecialchars($company['company_name']); ?> - Bidding Price: <?php echo htmlspecialchars($company['bidding_price']); ?></li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p>No companies have applied yet.</p>
      <?php endif; ?>
    </div>
    <?php
    if (isset($_SESSION['user'])):
      ?>
    <!-- Registration form for companies to apply for the tender -->
    <div class="register-container">
    <h2>Register for Tender</h2>
    <!-- 
      In a real application, you'd dynamically fill 'tender_id' (e.g., from a query param).
      Also, you'd identify the user from session (company_id), so they wouldn't manually enter "company name" unless needed. 
    -->
    <form action="register.php" method="POST" enctype="multipart/form-data">
      <!-- Hidden action so the backend knows to apply for a tender -->
      <input type="hidden" name="action" value="apply_tender">

      <!-- Example: hidden tender_id (or display it as read-only if you prefer) -->
      <input type="hidden" name="tender_id" value= <?php echo htmlspecialchars($tender_id); ?>>

      <!-- According to your schema, 'company_id' is usually from session. 
           But if you want the user to see or confirm their company name, 
           you can ask them to type it (or show it read-only).
      -->
      <label for="company_name">Company Name</label>
      <input type="text" id="company_name" name="company_name" placeholder="e.g. ABC Corp" required>

      <label for="bidding_price">Bidding Price</label>
      <input type="number" step="0.01" id="bidding_price" name="bidding_price" placeholder="e.g. 3050.00" required>

      <label for="document">Upload Document</label>
      <input type="file" id="document" name="document" required>

      <button type="submit">Submit Application</button>
    </form>
    <a href="index.html" class="back-link">Back to Home</a>
  </div>

    <a href="home.html" class="back-button">Back</a>
  </div>
  <?php
exit;
endif; ?>
</body>
</html>
