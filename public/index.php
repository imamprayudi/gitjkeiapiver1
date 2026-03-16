<?php
require_once "security.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>JVCKENWOOD Web Services</title>

<style>

body{
    margin:0;
    font-family: Arial, Helvetica, sans-serif;
    background: linear-gradient(135deg,#eef2f7,#d8e2f1);
}

/* HEADER */

.header{
    width:100%;
    background:#003366;
    padding:15px 0;
    text-align:center;
}

.header img{
    width:90px;
}

/* CONTAINER */

.container{
    display:flex;
    justify-content:center;
    align-items:center;
    min-height:80vh;
}

/* CARD */

.card{
    background:white;
    width:700px;
    text-align:center;
    padding:50px;
    border-radius:12px;
    box-shadow:0 10px 30px rgba(0,0,0,0.15);

    animation: fadeIn 1.5s ease;
}

/* FADE IN ANIMATION */

@keyframes fadeIn{

    from{
        opacity:0;
        transform: translateY(30px);
    }

    to{
        opacity:1;
        transform: translateY(0);
    }

}

/* TITLE */

.title{
    font-size:30px;
    font-weight:600;
    letter-spacing:2px;
    color:#333;
}

/* LOGO */

.jvc-logo{
    width:320px;
    margin:30px 0;

    animation: fadeIn 2s ease;
}

/* BUTTON */

.login-btn{
    display:inline-block;
    margin-top:20px;
    padding:14px 40px;
    background:#0056b3;
    color:white;
    text-decoration:none;
    border-radius:30px;
    font-size:16px;
    font-weight:bold;
    transition:0.3s;
}

.login-btn:hover{
    background:#003f85;
    transform:translateY(-2px);
}

/* FOOTER */

.footer{
    margin-top:40px;
}

.footer h2{
    font-size:20px;
}

.footer p{
    font-size:14px;
    line-height:1.6;
}

/* BOTTOM BAR */

.footer-bar{
    text-align:center;
    font-size:12px;
    color:#666;
    padding:15px;
}

@media(max-width:768px){

.card{
    width:90%;
    padding:30px;
}

.jvc-logo{
    width:220px;
}

}

#particles-js{
    position:fixed;
    width:100%;
    height:100%;
    top:0;
    left:0;
    z-index:-1;
    background: linear-gradient(135deg,#eef2f7,#d8e2f1);
}

</style>

</head>

<body>
<div id="particles-js"></div>
<div class="header">
<img src="assets/gambar/logogit.png">
</div>

<div class="container">

<div class="card">

<div class="title">
WEB SERVICES FOR
</div>

<img src="assets/gambar/jvclogo.png" class="jvc-logo">

<br>

<a href="login.php" class="login-btn">
Login System
</a>

<div class="footer">

<h2>PT JVCKENWOOD ELECTRONICS INDONESIA</h2>

<p>
JL. Surya Lestari Kav.I-16B Suryacipta City of Industry<br>
Desa Kutamekar Kecamatan Ciampel Kabupaten Karawang<br>
Jawa Barat 41363, INDONESIA
</p>

</div>

</div>

</div>

<div class="footer-bar">
© 2026 PT JVCKENWOOD ELECTRONICS INDONESIA
</div>
<script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>

<script>

particlesJS("particles-js", {

  particles: {
    number: {
      value: 60
    },

    color: {
      value: "#1e90ff"
    },

    shape: {
      type: "circle"
    },

    opacity: {
      value: 0.5
    },

    size: {
      value: 3
    },

    move: {
      enable: true,
      speed: 2
    },

    line_linked: {
      enable: true,
      distance: 150,
      color: "#1e90ff",
      opacity: 0.4,
      width: 1
    }

  }

});

</script>
</body>
</html>