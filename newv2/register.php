<?php
$dsn = 'mysql:host=mariadb.vamk.fi;dbname=e2301469_;charset=utf8';
$db_username = 'e2301469';
$db_password = 'NZHYAuR8dEQ';
try {
    $pdo = new PDO($dsn, $db_username, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'apply_tender') {
    $tender_id = intval($_POST['tender_id']); 
    $company_name = $_POST['company_name']; 
    $bidding_price = floatval($_POST['bidding_price']); 
    $document_name = $_FILES['document']['name']; 
    $document_tmp_name = $_FILES['document']['tmp_name']; 
    $target_dir = "/"; 
    $target_file = $target_dir . basename($document_name); 
    $uploadOk = 1;
    if ($_FILES['document']['size'] > 5000000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }
    $file_extension = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $allowed_extensions = ['pdf', 'docx', 'jpg', 'png'];
    if (!in_array($file_extension, $allowed_extensions)) {
        echo "Sorry, only PDF, DOCX, JPG, and PNG files are allowed.";
        $uploadOk = 0;
    }
    if ($uploadOk == 1) {
        $sql = "INSERT INTO tender_applications (tender_id, company_id, bidding_price, document) 
                  VALUES ('$tender_id', 1, '$bidding_price', '$target_file')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        echo "Tender application successfully submitted!";
        header("Location: index.php");
        exit();
    }
}
?>
