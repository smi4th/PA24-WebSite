<h1>THIS IS WORKING</h1>

<script>
    await fetch('http://api.localhost')
        .then(response => response.json())
        .then(data => console.log(data));

</script>