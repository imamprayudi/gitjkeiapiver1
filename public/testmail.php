<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mail Test</title>
</head>
<body>
  <script>
   function sendMail() {
    fetch('http://136.198.117.117/api/apijkeidev/apisendmail.php')
        .then(response => {
            console.log('Status:', response.status);
            return response.text();
        })
        .then(text => {
            console.log('Response:', text);
        })
        .catch(error => {
            console.error('ERROR:', error);
        });
}
  </script>
  <button onclick="sendMail()">Send Test Mail</button>
</body>
</html>