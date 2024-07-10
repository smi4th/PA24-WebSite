<!DOCTYPE HTML>
<html>
<head>
    <title>Accueil</title>
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <meta charset="utf-8">
    <link rel="icon" href="{{ asset('assets/images/logo/white-line-orange-bg-blue.png') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
<main>
    <section>
        @if (session('success'))
            <div class="toast position-absolute align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        @endif
        <div class="header">
            <nav>
                <div class="logo">
                    <img src="{{ asset('assets/images/logo/white-line-orange.png') }}" alt="logo">
                </div>
                <div class="menu">
                    <ul>
                        <li><a href="/travel">Voyager</a></li>
                        <li><a href="/prestations">Prestation</a></li>
                    </ul>
                </div>
                @if(session('auth'))
                    <div class="cta">
                        <button onclick="window.location.href='/profile'">Mon profil</button>
                    </div>
                @else
                    <div class="cta">
                        <button onclick="window.location.href='/login'">Se connecter</button>
                    </div>
                @endif
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
    <section>
        <div class="section_title">
            <h1>Les profils</h1>
            <h2>Quelques soient votre profil, nous avons ce qu'il vous faut</h2>
        </div>
        <div class="profiles">
            <div class="profiles_content">
                <div class="profile_item">
                    <img src="{{ asset('/assets/images/bailleurs.png') }}" alt="Paris">
                    <h3>Bailleurs</h3>
                </div>
                <div class="profile_item">
                    <img src="{{ asset('/assets/images/prestataires.png') }}" alt="Lyon">
                    <h3>Prestataires</h3>
                </div>
                <div class="profile_item">
                    <img src="{{ asset('/assets/images/voyageurs.png') }}" alt="Marseille">
                    <h3>Voyageurs</h3>
                </div>
            </div>
        </div>
    </section>
    <section>
        <div class="section_title">
            <h1>La newsletter</h1>
            <h2>Pour être tenu à jour des dernières nouveautés et des offres en cours</h2>
        </div>
        <div class="newsletter_layout">
            <div class="newsletter">
                <form action="">
                    <input type="email" name="email" id="email" placeholder="Votre adresse email">
                    <button type="submit">S'abonner</button>
                </form>
            </div>
        </div>
    </section>
</main>
<div class="chatbot-button">
        <button type="button" class="btn btn-primary" id="open-chatbot">
            <i class="bi bi-chat-dots"></i>
        </button>
    </div>
    <div class="chatbot-interface d-none">
        <div class="card">
            <div class="card-header">
                Chatbot
                <button type="button" class="btn-close" aria-label="Close" id="close-chatbot"></button>
            </div>
            <div class="card-body">
                <div class="chat-container">
                    <div class="chat-message"><b>Bot:</b> Bonjour, comment puis-je vous aider ?</div>
                </div>
                <textarea class="form-control" placeholder"Entrez votre question..." id="chat-input"></textarea>
                <button type="button" class="btn btn-primary mt-2" id="send-message">Send</button>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('open-chatbot').addEventListener('click', function() {
            document.querySelector('.chatbot-interface').classList.remove('d-none');
        });

        document.getElementById('close-chatbot').addEventListener('click', function() {
            document.querySelector('.chatbot-interface').classList.add('d-none');
        });

        document.getElementById('send-message').addEventListener('click', function() {
        var message = document.getElementById('chat-input').value;
        var chatContainer = document.querySelector('.chat-container');
        var newMessage = `<div class="chat-message"><b>Vous:</b> ${message}</div>`;
        chatContainer.innerHTML += newMessage;
        document.getElementById('chat-input').value = '';

        fetch(`/chatbot?keyword=${message}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Réponse du chatbot:', data);
            let message = data.messages[0].text;
            var botMessage = `<div class="chat-message"><b>Bot:</b> ${message}</div>`;
            chatContainer.innerHTML += botMessage;
        })
        .catch((error) => {
            console.error('Erreur:', error);
        });
    });
    </script>
<x-footer/>
</body>

</html>
