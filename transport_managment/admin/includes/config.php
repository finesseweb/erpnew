<?php 
//// DB credentials.
//define('DB_HOST','localhost');
//define('DB_USER','root');
//define('DB_PASS','');
//define('DB_NAME','main-erp');
//// Establish database connection.
//try
//{
//$dbh = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME,DB_USER, DB_PASS,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
//}
//catch (PDOException $e)
//{
//exit("Error: " . $e->getMessage());
//}
?>
<?php
$servername = "localhost";
$username = "root";
$password = "";
global $conn;
try {
    $conn = new PDO("mysql:host=$servername;dbname=main-erp", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Connected successfully";
    }
catch(PDOException $e)
    {
    echo "Connection failed: " . $e->getMessage();
    }
?>