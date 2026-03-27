<?php
require_once "security.php";
session_start();

if (isset($_SESSION['error'])) {
    echo "<div class='alert alert-danger'>".$_SESSION['error']."</div>";
    unset($_SESSION['error']);
}

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
 
$appkey = $_SESSION['appkey'];
$env = parse_ini_file(__DIR__ . '/../config/.env');
$envappkey = $env['APP_KEY'];
if ($appkey !== $envappkey) {
  header("Location: login.php");
  exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Profile</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>


<?php include "menu.php"; ?>

<div class="container mt-4">

<h3>My Profile</h3>
<hr>
<div id="msg"></div>

<form id="profileForm">

  <div class="mb-3">
    <label class="form-label">Username</label>
    <input type="text" id="username" name="username" class="form-control">
  </div>

  <div class="mb-3">
    <label class="form-label">Email</label>
    <input type="email" id="email" name="email" class="form-control">
  </div>

  <div class="mb-3">
    <label class="form-label">Old Password</label>
    <input type="password" name="oldpassword" class="form-control" placeholder="Type old password to confirm changes">
</div>

<div class="mb-3">
    <label class="form-label">New Password</label>
    <input type="password" name="newpassword" class="form-control" placeholder="Leave blank if you don't want to change password">
</div>

  <button type="submit" class="btn btn-primary">Update Profile</button>
</form>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
let urlprofileread = "";
let urlprofileupdate = "";

fetch('getsession.php', 
{
  method: 'GET',
  headers: 
  {
  'X-Requested-With': 'XMLHttpRequest'
  }
})
.then(response => response.json())
.then(data => 
{
  user = data.user;
  level = data.level;
  appkey = data.appkey;
  urlprofileread = data.urlprofileread;
  urlprofileupdate = data.urlprofileupdate;
  loadProfile();
}) 
.catch(err => console.error(err));

document.getElementById("profileForm").addEventListener("submit", function(e) {
    e.preventDefault(); // stop reload!
    updateProfile();
});

// ==============================
// LOAD PROFILE
// ==============================
async function loadProfile() {
    try {
        const response = await fetch(urlprofileread);
        const result = await response.json();

        if (result.status === "success") {
            document.getElementById("username").value = result.data.username;
            document.getElementById("email").value = result.data.useremail;
        } else {
            document.getElementById("msg").innerHTML =
                `<div class="alert alert-danger">${result.message}</div>`;
        }

    } catch (error) {
        console.error("Error loading profile:", error);
    }
}

document.addEventListener("DOMContentLoaded", loadProfile);

// ==============================
// UPDATE PROFILE
// ==============================
async function updateProfile() {

    const payload = {
        username : document.getElementById("username").value,
        email    : document.getElementById("email").value,
        oldpassword : document.querySelector("input[name='oldpassword']").value,
        newpassword : document.querySelector("input[name='newpassword']").value
    };

    try {
        const response = await fetch(urlprofileupdate, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (result.status === "success") {
            document.getElementById("msg").innerHTML =
                `<div class="alert alert-success">Profile has been updated successfully!</div>`;
        } else {
            document.getElementById("msg").innerHTML =
                `<div class="alert alert-danger">${result.message}</div>`;
        }

        return false;

    } catch (error) {
        console.error("Update error:", error);
        document.getElementById("msg").innerHTML =
            `<div class="alert alert-danger">Update failed!</div>`;
        return false;
    }
}
</script>
</body>
</html>