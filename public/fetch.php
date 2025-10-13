
<script>
fetch('http://136.198.117.118/api/test1.php?nama=Imam&password=12345')
  .then(response => response.json())
  .then(data => {
    console.log('Balasan dari server:', data);
  })
  .catch(error => {
    console.error('Terjadi error:', error);
  });

</script>
