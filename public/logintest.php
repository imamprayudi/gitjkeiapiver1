<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>LOGIN</title>
</head>
<body>
  <h2>JKEI Login Page</h2>

  <form id="loginForm">
    <label>Nama: <input type="text" id="nama" required></label><br><br>
    <label>Password: <input type="password" id="password" required></label><br><br>
    <button type="submit">Login</button>
  </form>

  <div id="hasil"></div>
  <?php
  $env = parse_ini_file(__DIR__ . '/../config/.env');
  $loginurl = $env['API_LOGIN_URL'];
  //$getkey = $env['GET_KEY'];
  $getkey = 'key123'; //tes
  ?>
  <script>
    document.getElementById('loginForm').addEventListener('submit', async function (e) {
      e.preventDefault(); // cegah reload halaman

      const nama = document.getElementById('nama').value;
      const password = document.getElementById('password').value;
      const alamat = '<?=$loginurl?>';
      try {
        const response = await fetch(alamat, {
          method: 'POST',
          credentials: "include",
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: new URLSearchParams({
            nama: nama,
            password: password
          })
        });

        const reply = await response.text();
        document.getElementById('hasil').innerHTML = reply;

        if (reply.includes('success')) {
          const isidata = JSON.parse(reply);
          const nama = isidata.data[0];
          const level = isidata.data[1];
          const url = `../config/makesession.php?nama=${encodeURIComponent(nama)}&level=${encodeURIComponent(level)}&getkey=<?=$getkey?>`;
          window.location.href = url;
        }
      } catch (error) {
        document.getElementById('hasil').innerHTML = 'Terjadi kesalahan koneksi.';
        console.error(error);
      }
    });
  </script>
</body>
</html>
