<?php

$dsn = 'mysql:host=mariadb.vamk.fi;dbname=e2301469_;charset=utf8';
$db_username = 'e2301469';  
$db_password = 'NZHYAuR8dEQ';

try {
    $pdo = new PDO($dsn, $db_username, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}

$stmt = $pdo->prepare("SELECT * FROM tenders");
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$tenders = $result;



?>
<!DOCTYPE html>
<html lang="en">
<head>
<style>
    body {
      margin: 0; 
      padding: 0; 
      font-family: Arial, sans-serif; 
      background-color: #f5f5f5;
    }
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
    .container {
      max-width: 1200px; 
      margin: 2rem auto; 
      background-color: #fff; 
      padding: 2rem;
      border-radius: 5px;
    }
    .search-bar {
      display: flex; 
      gap: 0.5rem; 
      margin-bottom: 2rem;
    }
    .search-bar input[type="text"] {
      flex: 1; 
      padding: 0.5rem; 
      font-size: 1rem;
    }
    .search-bar button {
      padding: 0.5rem 1rem; 
      background-color: #000; 
      color: #fff; 
      border: none; 
      cursor: pointer;
    }
    h2 {
      margin-bottom: 1rem;
    }
    .tenders-list {
      display: grid; 
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 1rem;
    }
    .tender-card {
      border: 1px solid #ddd; 
      border-radius: 5px; 
      padding: 1rem; 
      background-color: #fafafa;
      display: flex; 
      flex-direction: column; 
      justify-content: space-between;
    }
    .tender-card h3 {
      margin-top: 0; 
      margin-bottom: 0.5rem;
    }
    .tender-card button {
      margin-top: 1rem; 
      padding: 0.5rem; 
      background-color: #c00; 
      color: #fff; 
      border: none; 
      cursor: pointer; 
      border-radius: 3px;
    }
    .tender-card button:hover {
      background-color: #900;
    }
  </style>
  <meta charset="UTF-8">
  <title>E-Tendering - Home</title>
  <style>
    /* Add your styles here */
  </style>
</head>
<body>
  <header>
    <h1>E-Tendering</h1>
    <nav>
      <a href="login.php">Login</a>
    </nav>
  </header>

  <div class="container">
    <div class="search-bar">
      <input type="text" placeholder="Search tenders...">
      <button>Search</button>
    </div>

    <h2>Tenders</h2>
    <div class="tenders-list">
      <?php if (!empty($tenders)): ?>
        <?php foreach ($tenders as $tender): ?>
          <div class="tender-card">
            <h3><?php echo htmlspecialchars($tender['tender_name']); ?></h3>
            <p><strong>Bidding price:</strong> <?php echo htmlspecialchars($tender['bidding_price']); ?></p>
            <p><strong>Winning company:</strong> <?php echo htmlspecialchars($tender['winning_company']); ?></p>
            <p><strong>Bidding price of winning company:</strong> <?php echo htmlspecialchars($tender['winning_price']); ?></p>
            <button onclick="location.href='moreinfo.php?tender=<?php echo $tender['id']; ?>'">More Info</button>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>No tenders available.</p>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
