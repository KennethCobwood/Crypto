<?php
$host = 'localhost';
$username = 'root';
$password = '';
$conn = new mysqli($host, $username, $password);
$cipher = 'AES-128-CBC';
$key = 'thebestsecretkey';


if ($conn->connect_error) {
die('Connection failed: ' . $conn->connect_error);
}
if (isset($_POST['delete-everything'])) {
$sql = 'DROP DATABASE K_Table;';
if (!$conn->query($sql) === TRUE) {
die('Error dropping database: ' . $conn->error);
}
}
$sql = 'CREATE DATABASE IF NOT EXISTS K_Table;';
if (!$conn->query($sql) === TRUE) {
die('Error creating database: ' . $conn->error);
}
$sql = 'USE K_Table;';
if (!$conn->query($sql) === TRUE) {
die('Error using database: ' . $conn->error);
}
$sql = 'CREATE TABLE IF NOT EXISTS K_registrationform (
id int NOT NULL AUTO_INCREMENT,
iv varchar(32) NOT NULL,
firstname varchar(256) NOT NULL,
surname varchar(256) NOT NULL,
pps varchar(256) NOT NULL,
PRIMARY KEY (id));';
if (!$conn->query($sql) === TRUE) {
die('Error creating table: ' . $conn->error);
}
?>
<html>

<head>
<title>Patients Registration Portal</title>
</head>

<body>
        
<h1>Patients Registration Portal</h1>
<?php

if (isset($_POST['new-patient'])) {
$iv = random_bytes(16);
$escaped_firstname = $conn -> real_escape_string($_POST['firstname']);
$encrypted_firstname = openssl_encrypt($escaped_firstname, $cipher, $key, OPENSSL_RAW_DATA, $iv);
$escaped_surname = $conn -> real_escape_string($_POST['surname']);
$encrypted_surname = openssl_encrypt($escaped_surname, $cipher, $key, OPENSSL_RAW_DATA, $iv);
$escaped_pps = $conn -> real_escape_string($_POST["pps"]);
$encrypted_pps = openssl_encrypt($escaped_pps, $cipher, $key, OPENSSL_RAW_DATA, $iv);


$iv_hex = bin2hex($iv);
$firstname_hex = bin2hex($encrypted_firstname);
$surname_hex = bin2hex($encrypted_surname);
$pps_hex = bin2hex($encrypted_pps);


$sql = "INSERT INTO K_registrationform (iv, firstname, surname, pps) VALUES ('$iv_hex', '$firstname_hex', '$surname_hex', '$pps_hex' )";
if ($conn->query($sql) === TRUE) {
echo '<p><i>New Patient Added to Register!</i></p>';
} else {
die('Error creating note: ' . $conn->error);
}
}
?>
<h1>Please enter your details</h1>
<h2>Firstname</h2>
<form method="post">
<input type="text" id="firstname" name="firstname" size="64"><br><br>
<h2>Lastname</h2>
<form method="post">
<input type="text" id="surname" name="surname" size="64"><br><br>
<h2>PPS Number</h2>
<form method="post">
<input type="text" id="pps" name="pps" size="64"><br><br>


		
 
 <button type="submit" name="new-patient">Save Patient</button>
</form>
<h2>Details that you entered to COVID-19 form:</h2>
<?php
$sql = "SELECT id, iv, firstname, surname, pps FROM K_registrationform";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
echo '<table><tr><th>ID</th><th>firstname</th></tr>';

while($row = $result->fetch_assoc()) {
$id = $row['id'];
$iv = hex2bin($row['iv']);
$firstname = hex2bin($row['firstname']);
$surname = hex2bin($row['surname']);
$pps = hex2bin($row['pps']);


$unencrypted_firstname = openssl_decrypt($firstname, $cipher, $key, OPENSSL_RAW_DATA, $iv);
$unencrypted_surname = openssl_decrypt($surname, $cipher, $key, OPENSSL_RAW_DATA, $iv);
$unencrypted_pps = openssl_decrypt($pps, $cipher, $key, OPENSSL_RAW_DATA, $iv);
echo "<tr><td>$id</td><td>$unencrypted_firstname, $unencrypted_surname, $unencrypted_pps, </td></tr>";
}
echo '</table>';
} else {
echo '<p>There are no notes!</p>';
}
?>
<h3>Delete Everything</h3>
<form method="post">

 <button type="submit" name="delete-everything">Delete Everything!</button>
 
</form>
<a href="kenneth.html">
		<button>Return to Homepage</button>
		
		<a/>
</body>

</html>