<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f6f9;
        }
        .login-card {
            border-radius: 15px;
            padding: 30px;
            background: white;
            box-shadow: 0px 4px 12px rgba(0,0,0,0.1);
        }
        .login-title {
            font-weight: 700;
        }
    </style>
</head>
<body>

    <!DOCTYPE html>
<html>
<head>
    <title>Form Login</title>
</head>
<body>
  <h2>Login</h2>
  <div id="formlogin" class="login-card">
    <form>
      <label for="userid">User ID</label><br>
      <input type="text" name="userid" id="userid" required><br><br>
      <label for="password">Password</label><br>
      <input type="password" name="password" id="password" required><br><br>
      <button id="btn" type="submit">Login</button>
    </form>
  </div>
  <div id="pesan"></div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
let postkey = '';
let loginurl = '';
let userid = '';
let password = '';
let appkey = '';
let level = '';

fetch('getenv.php', {
  method: 'GET',
  headers: {
  'X-Requested-With': 'XMLHttpRequest'
}
})
.then(response => response.json())
.then(data => 
{
  postkey = data.postkey;
  loginurl = data.loginurl;
}) 
.catch(err => console.error(err));

document.addEventListener('submit', function(e) {
  e.preventDefault();
  const constuserid = document.getElementById('userid').value;
  const constpassword = document.getElementById('password').value;
  getLogin(postkey,constuserid,constpassword);
});


async function getLogin(postkey,userid,password)
{
  try 
  {
    const response = await fetch(loginurl, 
    {
      method: 'POST',
      credentials: "include",
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: new URLSearchParams({
        postkey: postkey,
        userid: userid,
        password: password
      })
    });

    const reply = await response.text(); // ambil balasan dari PHP
    const isidata = JSON.parse(reply);  
    console.log(isidata);
    const status = isidata.status;
    if (status === 'success')
    {
      userid = isidata.data[0];
      level = isidata.data[1];
      appkey = isidata.data[2];
      createSession(userid,level,appkey);
    }
    else
    {
      const pesanBox = document.getElementById("pesan");
      pesanBox.style.display = "block";
      pesanBox.style.backgroundColor = "red";
      pesanBox.style.color = "#1b5e20";
      pesanBox.innerText = "Login Failed: " + status; 
      
    }
  } catch (error) 
  {        
    console.error(error);
  }  
}

async function createSession(userid,level,appkey)
{
  try 
  {
    const response = await fetch('makesession.php', 
    {
      method: 'POST',
      credentials: "include",
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: new URLSearchParams({
        userid: userid,
        level: level,
        appkey: appkey
      })
    });

    const reply = await response.text(); // ambil balasan dari PHP
    const isidata = JSON.parse(reply);  
    const status = isidata.status;
    console.log('status reply dari makesession : ', status);
    if (status === 'success'){
      window.location.href = "dashboard.php";
    }
  } catch (error) 
  {        
    console.error(error);
  }
}
</script>
</body>
</html>
