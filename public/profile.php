<?php
//require_once "security.php";
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
let user = null;
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

// Jika data adalah string → berarti itu userid langsung
if (typeof data === "string") {
    user = { userid: data };
}

// Jika data object → cek kemungkinan lokasi userid
else if (data.user && data.user.userid) {
    user = data.user;
} 
else if (data.user && typeof data.user === "string") {
    user = { userid: data.user };
}
else if (data.userid) {
    user = { userid: data.userid };
}
else if (data.data && data.data.userid) {
    user = data.data;
}

// Jika tetap tidak dapat userid → STOP (hindari error)
if (!user || !user.userid) {
    console.error("GAGAL MENDAPAT USERID!", data);
    return;
}

  //user = data.user;
  level = data.level;
  appkey = data.appkey;
  urlprofileread = data.urlprofileread;
  urlprofileupdate = data.urlprofileupdate;
  urlprofileupdatejkei = data.urlprofileupdatejkei;
  console.log("urlprofileread:", urlprofileread);
  console.log("urlprofileupdate:", urlprofileupdate);
  console.log("urlprofileupdatejkei:", urlprofileupdatejkei);
  loadProfile(data.user);
}) 
.catch(err => console.error(err));

document.getElementById("profileForm").addEventListener("submit", function(e) {
    e.preventDefault(); // stop reload!
    updateProfile();
    updateProfileJkei();
});

// ==============================
// LOAD PROFILE
// ==============================
async function loadProfile(userData) {

    // Jika userData = "yudi" → convert menjadi { userid: "yudi" }
    if (typeof userData === "string") {
        userData = { userid: userData };
    }

    if (!userData || !userData.userid) {
        console.error("loadProfile DIPANGGIL TANPA USERID!", userData);
        return;
    }

    const formData = new FormData();
    formData.append("userid", userData.userid);

    try {
        const response = await fetch(urlprofileread, {
            method: "POST",
            body: formData
        });

        const result = await response.json();

        if (result.status === "success") {
            document.getElementById("username").value = result.data.username;
            document.getElementById("email").value = result.data.useremail;
        } else {
            document.getElementById("msg").innerHTML =
                `<div class="alert alert-danger">${result.message}</div>`;
        }
    } catch (err) {
        console.error("Error:", err);
    }
}

// ==============================
// UPDATE PROFILE
// ==============================
async function updateProfile() {

    const payload = {
        userid   : user.userid, // ← WAJIB
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

// ==============================
// UPDATE PROFILE di JKEI VIA API
// ==============================
// ==============================
// UPDATE PROFILE KE API JKEI
// ==============================
async function updateProfileJkei() {
    if (!user || !user.userid) {
        console.error("Cannot call JKEI API: userid is missing!", user);
        return;
    }

    const payload = {
        userid: user.userid,  // WAJIB
        username: document.getElementById("username").value,
        email: document.getElementById("email").value,
        oldpassword: document.querySelector("input[name='oldpassword']").value,
        newpassword: document.querySelector("input[name='newpassword']").value
    };

    try {
        const response = await fetch(urlprofileupdatejkei, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(payload),
            // mode: 'cors',  // default
            // credentials: 'include', // jika API perlu cookies/session
        });

        // debug: tampilkan response mentah dulu
        const text = await response.text();

        let result;
        try {
            result = JSON.parse(text);
        } catch (err) {
            console.error("❌ Failed to parse JSON:", err);
            console.error("Response text:", text);
            return;
        }

        if (result.status === "success") {
            console.log("✅ Profile updated in JKEI API");
        } else {
            console.warn("⚠️ Failed to update profile in JKEI API:", result.message);
        }

    } catch (error) {
        console.error("🚨 Update JKEI error:", error);
    }
}
</script>
</body>
</html>