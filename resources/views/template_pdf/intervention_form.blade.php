<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Fiche d'intervention</div>
                <div class="card-body">
                    <h3 class="text-align text-center">Informations du client</h3>
                    <p>Nom: {{$customer}}</p>
                    <h3>Intervention</h3>
                    <p>Date : {{ $startTime }}</p>
                    <p>Durée : {{ $duration }}</p>
                    <p>Prix : {{ $price }} €</p>
                    <hr>
                    <h3 class="text-center">Commentaire</h3>
                    <p>{{$comment}}</p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
