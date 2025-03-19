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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'apply_tender') {

  // Get the form values
  $tender_id = intval($_POST['tender_id']); // Tender ID from hidden input
  $company_name = $_POST['company_name']; // Company name
  $bidding_price = floatval($_POST['bidding_price']); // Bidding price
  $document_name = $_FILES['document']['name']; // File name
  $document_tmp_name = $_FILES['document']['tmp_name']; // Temporary file path

  // Check if the file was uploaded successfully
  $target_dir = "uploads/"; // Directory to store uploaded files
  $target_file = $target_dir . basename($document_name); // Full path for file
  $uploadOk = 1;

  // Check the file size (5MB limit in this case)
  if ($_FILES['document']['size'] > 5000000) {
      echo "Sorry, your file is too large.";
      $uploadOk = 0;
  }

  // Only allow certain file formats (optional)
  $file_extension = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
  $allowed_extensions = ['pdf', 'docx', 'jpg', 'png'];

  if (!in_array($file_extension, $allowed_extensions)) {
      echo "Sorry, only PDF, DOCX, JPG, and PNG files are allowed.";
      $uploadOk = 0;
  }

  // If the file is valid, move it to the target directory
  if ($uploadOk == 1) {
      if (move_uploaded_file($document_tmp_name, $target_file)) {
          // Insert data into the tender table
          $sql = "INSERT INTO tender_applications (tender_id, company_id, bidding_price, document) 
                  VALUES ('$tender_id', 1, '$bidding_price', '$target_file')";
          $stmt = $pdo->prepare($sql);
          $stmt->execute();

          
              echo "Tender application successfully submitted!";
              // You can redirect to a success page or back to home
              header("Location: index.html");
              exit();
      } else {
          echo "Sorry, there was an error uploading your file.";
      }
  }
}
?>