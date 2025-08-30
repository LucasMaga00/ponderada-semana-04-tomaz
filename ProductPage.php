<?php include "../inc/dbinfo.inc"; ?>
<html>
<body>
<h1>Products Management</h1>
<?php
  /* Connect to MySQL and select the database. */
  $connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);
  if (mysqli_connect_errno()) echo "Failed to connect to MySQL: " . mysqli_connect_error();
  $database = mysqli_select_db($connection, DB_DATABASE);
  
  /* Ensure that the PRODUCTS table exists. */
  VerifyProductsTable($connection, DB_DATABASE);
  
  /* If input fields are populated, add a row to the PRODUCTS table. */
  $product_name = htmlentities($_POST['NAME']);
  $product_price = htmlentities($_POST['PRICE']);
  
  if (strlen($product_name) || strlen($product_price)) {
    AddProduct($connection, $product_name, $product_price);
  }
?>
<!-- Input form -->
<form action="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
  <table border="0">
    <tr>
      <td>PRODUCT NAME</td>
      <td>PRICE</td>
    </tr>
    <tr>
      <td>
        <input type="text" name="NAME" maxlength="100" size="30" placeholder="Enter product name" />
      </td>
      <td>
        <input type="number" name="PRICE" step="0.01" min="0" size="20" placeholder="0.00" />
      </td>
      <td>
        <input type="submit" value="Add Product" />
      </td>
    </tr>
  </table>
</form>

<!-- Display table data. -->
<table border="1" cellpadding="2" cellspacing="2">
  <tr>
    <td>ID</td>
    <td>PRODUCT NAME</td>
    <td>PRICE</td>
    <td>CREATED AT</td>
  </tr>
<?php
$result = mysqli_query($connection, "SELECT * FROM PRODUCTS ORDER BY created_at DESC");
while($query_data = mysqli_fetch_row($result)) {
  echo "<tr>";
  echo "<td>",$query_data[0], "</td>",
       "<td>",$query_data[1], "</td>",
       "<td>$",$query_data[2], "</td>",
       "<td>",$query_data[3], "</td>";
  echo "</tr>";
}
?>
</table>

<!-- Clean up. -->
<?php
  mysqli_free_result($result);
  mysqli_close($connection);
?>
</body>
</html>

<?php
/* Add a product to the table. */
function AddProduct($connection, $name, $price) {
   $n = mysqli_real_escape_string($connection, $name);
   $p = mysqli_real_escape_string($connection, $price);
   
   // Validate price is numeric
   if (!is_numeric($p) || $p < 0) {
     echo("<p>Error: Price must be a valid positive number.</p>");
     return;
   }
   
   $query = "INSERT INTO PRODUCTS (name, price) VALUES ('$n', '$p')";
   if(!mysqli_query($connection, $query)) {
     echo("<p>Error adding product data: " . mysqli_error($connection) . "</p>");
   } else {
     echo("<p>Product added successfully!</p>");
   }
}

/* Check whether the table exists and, if not, create it. */
function VerifyProductsTable($connection, $dbName) {
  if(!TableExists("PRODUCTS", $connection, $dbName))
  {
     $query = "CREATE TABLE PRODUCTS (
         id INT AUTO_INCREMENT PRIMARY KEY,
         name VARCHAR(100) NOT NULL,
         price DECIMAL(10,2) NOT NULL,
         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
       )";
     if(!mysqli_query($connection, $query)) {
       echo("<p>Error creating PRODUCTS table: " . mysqli_error($connection) . "</p>");
     } else {
       echo("<p>PRODUCTS table created successfully!</p>");
     }
  }
}

/* Check for the existence of a table. */
function TableExists($tableName, $connection, $dbName) {
  $t = mysqli_real_escape_string($connection, $tableName);
  $d = mysqli_real_escape_string($connection, $dbName);
  $checktable = mysqli_query($connection,
      "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME = '$t' AND TABLE_SCHEMA = '$d'");
  if(mysqli_num_rows($checktable) > 0) return true;
  return false;
}
?>