<!DOCTYPE html>
<html>
<head>
    <title>Backoffice</title>
    <link rel="stylesheet" href="{{ asset('css/backoffice.css') }}">
    @include($file_path ?? 'backoffice.index')
    @if($stack_name !== null)
        @stack($stack_name)
    @endif
</head>
<body>
<main>
    <nav>
        <div class="title">
            <h3>Espace administration</h3>
        </div>
        <ul id="menu">
            <li>
                <div class="image">
                    <i class="statistics"></i>
                </div>
                <div class="link">
                    <a href="/statistics">Statistiques</a>
                </div>
            </li>
            <li>
                <div class="image">
                    <i class="suggests"></i>
                </div>
                <div class="link">
                    <a href="/suggests">Suggestions</a>
                </div>
            </li>
            <li>
                <div class="image">
                    <i class="travelers"></i>
                </div>
                <div class="link">
                    <a href="/travelers">Voyageurs</a>
                </div>
            </li>
            <li>
                <div class="image">
                    <i class="prestations"></i>
                </div>
                <div class="link">
                    <a href="/prestations">Prestataires</a>
                </div>
            </li>
            <li>
                <div class="image">
                    <i class="prestations-companies"></i>
                </div>
                <div class="link">
                    <a href="/prestations-companies">Entreprises prestataires</a>
                </div>
            </li>
            <li>
                <div class="image">
                    <i class="providers"></i>
                </div>
                <div class="link">
                    <a href="/providers">Bailleurs</a>
                </div>
            </li>
            <li>
                <div class="image">
                    <i class="supports"></i>
                </div>
                <div class="link">
                    <a href="/supports">Support</a>
                </div>
            </li>
            <li>
                <div class="image">
                    <i class="permissions"></i>
                </div>
                <div class="link">
                    <a href="/permissions">Permissions</a>
                </div>
            </li>
            <li>
                <div class="image">
                    <i class="settings"></i>
                </div>
                <div class="link">
                    <a href="/settings">Paramètres</a>
                </div>
            </li>
            <li>
                <div class="image">
                    <i class="home"></i>
                </div>
                <div class="link">
                    <a href="/">Accueil</a>
                </div>
            </li>

        </ul>

    </nav>
    <div class="main_content">

        <div class="header">
            <h2>Accueil gestion du site Paris Caretaker Services</h2>
            <div class="user">
                <img src="{{asset('assets/images/default_user.png')}}" alt="User">
                <p>Patate enjoyer</p>
            </div>
        </div>
        @yield('content')
    </div>
</main>
</body>
</html>

