<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css?family=Roboto|Inter|Karla|Manrope&display=swap" rel="stylesheet">
    <title>Cookies</title>
    <link rel="icon" href="{{ asset('logo.png') }}" />
</head>

<body>
    <x-header :connected="false" :profile="false" :light="false" />
    <div class="container mt-5">
        <h2>Politique de Cookies</h2>
        <p><b>1. Utilisation des cookies</b> : Le site <a href="https://pariscaretaker.fr">pariscaretaker.fr</a> utilise des cookies afin d'améliorer l'expérience utilisateur et de fournir des fonctionnalités essentielles pour la navigation. Ces cookies sont nécessaires au bon fonctionnement de notre site.</p>

        <p><b>2. Types de cookies utilisés</b> :</p>
        <ul>
            <li><b>Cookies nécessaires :</b> Ces cookies sont indispensables pour naviguer sur le site et utiliser ses fonctionnalités de base, comme l'accès à des zones sécurisées. Sans ces cookies, des services comme la connexion à votre compte ne peuvent pas être fournis.</li>
            <li><b>Cookies fonctionnels :</b> Ils permettent d'améliorer le confort de navigation, de personnaliser votre expérience sur le site en mémorisant vos préférences et en vous reconnaissant lors de votre retour sur le site.</li>
        </ul>

        <p><b>3. Gestion des cookies</b> : L'utilisateur peut configurer son navigateur pour accepter ou refuser les cookies du site <a href="https://pariscaretaker.fr">pariscaretaker.fr</a>. La plupart des navigateurs acceptent automatiquement les cookies, mais vous pouvez généralement modifier les paramètres de votre navigateur pour refuser les cookies si vous préférez. Cela pourrait toutefois vous empêcher de profiter pleinement du site.</p>

        <p><b>4. Consentement</b> : En utilisant notre site, vous consentez à l'utilisation des cookies conformément à cette politique de cookies. Un bouton pour accepter les cookies est disponible lors de votre première visite. Vous pouvez retirer ce consentement à tout moment en nous contactant à <a href="mailto:contact@pariscaretaker.fr">contact@pariscaretaker.fr</a>.</p>

        <p><b>5. Confidentialité</b> : Nous nous engageons à ne pas partager les données collectées par les cookies avec des tiers et à les utiliser exclusivement dans le cadre défini par cette politique.</p>

        <p>Pour toute question supplémentaire concernant l'utilisation des cookies sur notre site, n'hésitez pas à nous contacter à l'adresse suivante : <a href="mailto:contact@pariscaretaker.fr">contact@pariscaretaker.fr</a>.</p>

        <p>
            Ce site est édité par Paris Caretaker, 15 Rue des Lilas, 75014, Paris.
        </p>
        <p><strong>Ce site fait parti d'un projet étudiant, la société étant fictive et sujet du projet</strong></p>
    </div>
    <x-footer />
</body>

</html>
