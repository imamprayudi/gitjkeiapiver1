<?php
require_once "security.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login Page</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    margin:0;
    font-family:Arial;
}

/* PARTICLE BACKGROUND */

#particles-js{
    position:fixed;
    width:100%;
    height:100%;
    top:0;
    left:0;
    z-index:-1;
    background:linear-gradient(135deg,#0f2027,#203a43,#2c5364);
}

/* LOGIN CARD */

.login-card{
    border-radius:18px;
    padding:40px;
    backdrop-filter: blur(15px);
    background:rgba(255,255,255,0.15);
    box-shadow:0 10px 30px rgba(0,0,0,0.25);
    animation:fadeIn 1.2s ease;
}

/* TITLE */

.login-title{
    font-weight:700;
    color:white;
}

/* INPUT */

.form-control{
    border-radius:10px;
}

/* BUTTON */

.btn-primary{
    border-radius:25px;
    padding:10px;
    font-weight:bold;
    transition:0.3s;
}

.btn-primary:hover{
    transform:translateY(-2px);
    box-shadow:0 5px 15px rgba(0,0,0,0.3);
}

/* MESSAGE */

#pesan{
    margin-top:15px;
    padding:10px;
    border-radius:6px;
    display:none;
}

/* LOGO */

.logo{
    width:160px;
    margin-bottom:20px;
    animation:glow 3s infinite alternate;
}

/* ANIMATION */

@keyframes fadeIn{
    from{
        opacity:0;
        transform:translateY(30px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}

@keyframes glow{
    from{
        filter:drop-shadow(0 0 5px #4da6ff);
    }
    to{
        filter:drop-shadow(0 0 20px #4da6ff);
    }
}

.logo-git{
    width:120px;
    margin-top:10px;
    opacity:0.9;
    transition:0.3s;
}

.logo-git:hover{
    transform:scale(1.05);
}

</style>
</head>

<body>

<div id="particles-js"></div>

<div class="container vh-100 d-flex justify-content-center align-items-center">

<div class="login-card col-md-4 text-center">

<img src="assets/gambar/jvclogo.png" class="logo">

<h3 class="login-title mb-4">Login System</h3>

<form id="loginForm" method="post">

<div class="mb-3 text-start">
<label class="form-label text-white">User ID</label>
<input type="text" class="form-control" id="userid" required>
</div>

<div class="mb-3 text-start">
<label class="form-label text-white">Password</label>
<input type="password" class="form-control" id="password" required>
</div>

<button type="submit" class="btn btn-primary w-100">Login</button>

<div class="text-center mt-4">
    <img src="assets/gambar/logogit.png" class="logo-git">
</div>

</form>

<div id="pesan"></div>

</div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- PARTICLE JS -->
<script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>

<script>

particlesJS("particles-js",{
particles:{
number:{value:70},
color:{value:"#ffffff"},
shape:{type:"circle"},
opacity:{value:0.5},
size:{value:3},
line_linked:{
enable:true,
distance:150,
color:"#ffffff",
opacity:0.4,
width:1
},
move:{
enable:true,
speed:2
}
}
});

</script>


<script>

let postkey='';
let loginurl='';
let userid='';
let password='';
let appkey='';
let level='';

fetch('getenv.php',{
method:'GET',
headers:{'X-Requested-With':'XMLHttpRequest'}
})
.then(response=>response.json())
.then(data=>{
postkey=data.postkey;
loginurl=data.loginurl;
})
.catch(err=>console.error(err));


document.getElementById("loginForm").addEventListener("submit",function(e){

e.preventDefault();

const constuserid=document.getElementById('userid').value;
const constpassword=document.getElementById('password').value;

getLogin(postkey,constuserid,constpassword);

});


async function getLogin(postkey,userid,password){

try{

const response=await fetch(loginurl,{
method:'POST',
credentials:"include",
headers:{
'Content-Type':'application/x-www-form-urlencoded'
},
body:new URLSearchParams({
postkey:postkey,
userid:userid,
password:password
})
});

const reply=await response.text();
const isidata=JSON.parse(reply);

const status=isidata.status;

if(status==='success'){

userid=isidata.data[0];
level=isidata.data[1];
appkey=isidata.data[2];

createSession(userid,level,appkey);

}else{

const pesanBox=document.getElementById("pesan");

pesanBox.style.display="block";
pesanBox.style.backgroundColor="red";
pesanBox.style.color="white";
pesanBox.innerText="Login Failed : "+status;

}

}catch(error){

console.error(error);

}

}


async function createSession(userid,level,appkey){

try{

const response=await fetch('makesession.php',{
method:'POST',
credentials:"include",
headers:{
'Content-Type':'application/x-www-form-urlencoded'
},
body:new URLSearchParams({
userid:userid,
level:level,
appkey:appkey
})
});

const reply=await response.text();
const isidata=JSON.parse(reply);

const status=isidata.status;

if(status==='success'){
window.location.href="dashboard.php";
}

}catch(error){
console.error(error);
}

}

</script>

</body>
</html>
