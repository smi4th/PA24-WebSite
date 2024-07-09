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
                <div class="card-header">Facture</div>
                <div class="card-body">
                    <h3 class="text-align">Informations du client</h3>
                    <p>Nom: {{$account->first_name}} {{$account->last_name}}</p>
                    <h3>Hébergement</h3>
                    @foreach($housing as $h)
                        <p>Description : {{ $h->description }}</p>
                        <p>Prix : {{ $h->price }}</p>
                        <p>Date de début : {{ $h->startTime }}</p>
                        <p>Date de fin : {{ $h->endTime }}</p>
                        <p>Adresse : {{ $h->streetNb }} {{ $h->street }}, {{ $h->city }}</p>
                        <p>Surface : {{ $h->surface }} m²</p>
                    @endforeach
                    <h3>Chambres</h3>
                    @foreach ($bedrooms as $bedroom)
                        <p>Date de début: {{ $bedroom->startTime }}</p>
                        <p>Date de fin: {{ $bedroom->endTime}}</p>
                        <p>Nombre de places: {{ $bedroom->nbPlaces}}</p>
                        <p>Prix: {{ $bedroom->price}}</p>
                        <p>Description: {{ $bedroom->description }}</p>
                    @endforeach
                    <h3>Équipements</h3>
                    @foreach ($equipments as $equipment)
                        <p>Nom: {{ $equipment->name}}</p>
                        <p>Description: {{ $equipment->description }}</p>
                        <p>Prix: {{ $equipment->price }}</p>
                    @endforeach
                    <hr>
                    <h3>Services</h3>
                    @foreach ($services as $service)
                        <p>Description: {{ $service->description }}</p>
                        <p>Prix: {{ $service->price }}</p>
                    @endforeach
                    <h3>Total: {{ $total }}</h3>
                    <h3>Payé TTC: {{ $totaltaxes }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
