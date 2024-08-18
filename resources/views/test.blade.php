<p>HELLO TEST</p>

<!DOCTYPE html>
<html>

<head>
    <title>App API Data</title>
</head>

<body>

    <h1>API Dummy Data</h1>

    <div id="data"></div>

    <script>
        // JS to fetch API data
        fetch('/index')
            .then(response => response.json())
            .then(data => {
                console.log(data);

                // data received from API
                document.getElementById('data').innerHTML = (data);
                document.getElementById('data').innerHTML = `
          <h2>Categories</h2>  
          <pre>${JSON.stringify(data.categories)}</pre>
    
          <h2>Products</h2>    
          <pre>${JSON.stringify(data.products)}</pre>

          <h2>Orders</h2>
          <pre>${JSON.stringify(data.orders)}</pre>
        `

            });
    </script>

</body>

</html>
