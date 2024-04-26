<!DOCTYPE HTML>
<html>
<head>
    <title>Accueil</title>
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <meta charset="utf-8">
</head>
<body>
<main>
    <section>

        <div class="header">
            <nav>
                <div class="logo">
                    <img src="{{ asset('/assets/images/SiteLogo.svg') }}" alt="logo">
                </div>
                <div class="menu">
                    <ul>
                        <li><a href="#">Voyager</a></li>
                        <li><a href="#">Prestation</a></li>
                        <li><a href="#">Louer</a></li>
                        <li><a href="#">Avis</a></li>
                    </ul>
                </div>
                <div class="cta">
                    <button onclick="window.location.href='/login'">Se connecter</button>
                </div>
            </nav>
        </div>
        <div class="main_title">
            <h1>Paris Caretaker Services</h1>
            <h2>Votre chaine de conciergerie</h2>
            <div class="select_menu">
                <ul>
                    <li>
                        <i class="map_point"></i><select>
                            <option selected disabled hidden>Choisir un endroit</option>
                            <option value="Paris">Paris</option>
                            <option value="Lyon">Lyon</option>
                            <option value="Marseille">Marseille</option>
                            <option value="Bordeaux">Bordeaux</option>
                        </select>
                    </li>
                    <li>
                        <input type="date" name="Séléctionner une date" id="date">
                    </li>
                    <li>
                        <button onclick="window.location.href='/login'">Rechercher</button>
                    </li>
                </ul>
            </div>

        </div>

    </section>
    <section>
        <ul>
            <li>
                <div class="number">
                    <h3>100</h3>
                </div>
                <div class="text">
                    <p>Nombre de visites</p>
                </div>
            </li>
            <li>
                <div class="number">
                    <h3>10</h3>
                </div>
                <div class="text">
                    <p>Nouveaux utilisateurs</p>
                </div>
            </li>
            <li>
                <div class="number">
                    <h3>5</h3>
                </div>
                <div class="text">
                    <p>Nombre de réservations</p>
                </div>
            </li>
        </ul>
    </section>
    <section>
        <div class="section_title">
            <h1>Lieux populaires</h1>
            <h2>Un large panel de lieux populaires tout autour du globe et de la France</h2>
        </div>
        <div class="caroussel">

            <div class="caroussel_content">
                <div class="caroussel_item">
                    <img src="{{ asset('/assets/images/paris.jpg') }}" alt="Paris">
                    <h3>Paris</h3>
                </div>
                <div class="caroussel_item">
                    <img src="{{ asset('/assets/images/lyon.jpg') }}" alt="Lyon">
                    <h3>Lyon</h3>
                </div>
                <div class="caroussel_item">
                    <img src="{{ asset('/assets/images/marseille.jpg') }}" alt="Marseille">
                    <h3>Marseille</h3>
                </div>
            </div>

        </div>
    </section>
</main>
<footer>je suis un footer !</footer>
</body>

</html>
