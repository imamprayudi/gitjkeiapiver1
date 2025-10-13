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

  <script>
    document.getElementById('loginForm').addEventListener('submit', async function (e) {
      e.preventDefault(); // cegah reload halaman

      const nama = document.getElementById('nama').value;
      const password = document.getElementById('password').value;
      const alamat = 'https://svr1.jkei.jvckenwood.com/api/test.php'; // ganti sesuai alamat server Anda
      // const alamat = 'http://136.198.117.118/api/test1.php'; // ganti sesuai alamat server Anda
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

        const reply = await response.text(); // ambil balasan dari PHP
        document.getElementById('hasil').innerHTML = reply;

        // contoh: cek isi balasan dari PHP
        if (reply.includes('success')) {
          const isidata = JSON.parse(reply);
          const nama = isidata.data[0];
          const level = isidata.data[1];
          // Buat URL dengan parameter GET
          const url = `makesession.php?nama=${encodeURIComponent(nama)}&level=${encodeURIComponent(level)}`;
          console.log(url);
          // Redirect ke halaman PHP
          window.location.href = url;
         //window.location.href = 'makesession.php';
        }
      } catch (error) {
        document.getElementById('hasil').innerHTML = 'Terjadi kesalahan koneksi.';
        console.error(error);
      }
    });
  </script>
</body>
</html>
