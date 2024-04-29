<!DOCTYPE html>
<html>

<head>
    <title>Erreur : ></title>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href={{asset("css/error.css")}}>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=yes">

</head>
<body>
<div class='circle'></div>
<div class='circle'></div>
<div class='circle'></div>
<main>
    <div class='first-part'>
        <div class='main-title'>
            <h1>Oups ...</h1>
        </div>
        <div class='sub-title'>
            @php
                echo "<h2>Erreur : ".$code."</h2>";
                echo "<h2>".$message."</h2>";
            @endphp
        </div>

        <div class='content'>
            <p>
                il semblerait que vous vous êtes perdus en cours de route, vous pouvez retourner à l’accueil ou continuer d’admirer ce chat ...
            </p>
        </div>
        <div class='back-cta'>
            <button onclick="document.location.href='/'">Retour à l'accueil</button>
        </div>
    </div>
    <div class='second-part'>
        <div class='cat'>
            <img src={{asset("/assets/images/404.svg")}}>
        </div>
    </div>
</main>
</body>
</html>
