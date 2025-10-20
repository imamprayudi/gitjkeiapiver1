<?php
if (isset($_GET['pesan'])) {
   // echo "Pesan : " . $_GET['pesan'];
   $pesan = $_GET['pesan'];
} else {
    $pesan = "";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Form Login</title>
</head>
<body>
    <h2>Login</h2>
    <form action="loginpost.php" method="post">
        <label>Username:</label><br>
        <input type="text" name="userid" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Login</button>
    </form>
    <?php
    echo '<br>';
    echo $pesan;
    ?>
</body>
</html>
