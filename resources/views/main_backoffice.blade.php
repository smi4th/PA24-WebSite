<!DOCTYPE html>
<html>
<head>
    <title>Backoffice</title>
    <link rel="stylesheet" href="{{ asset('css/backoffice_style/backoffice.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    @include($file_path ?? 'backoffice.index')

    @if($stack_css !== null)

        @stack($stack_css)
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
                    <i class="start"></i>
                </div>
                <div class="link">
                    <a href="/backoffice">Accueil</a>
                </div>
            </li>
            <li>
                <div class="image">
                    <i class="statistics"></i>
                </div>
                <div class="link">
                    <a href="/backoffice/statistics">Statistiques</a>
                </div>
            </li>
            <!--
            <li>
                <div class="image">
                    <i class="suggests"></i>
                </div>
                <div class="link">
                    <a href="/backoffice/suggests">Suggestions</a>
                </div>
            </li>
            -->
            <li>
                <div class="image">
                    <i class="travelers"></i>
                </div>
                <div class="link">
                    <a href="/backoffice/users">Utilisateurs</a>
                </div>
            </li>
            <!--
            <li>
                <div class="image">
                    <i class="prestations"></i>
                </div>
                <div class="link">
                    <a href="/backoffice/staff">Staff</a>
                </div>
            </li>
            <li>
                <div class="image">
                    <i class="prestations-companies"></i>
                </div>
                <div class="link">
                    <a href="/backoffice/prestations-companies">Entreprises prestataires</a>
                </div>
            </li>
            <li>
                <div class="image">
                    <i class="providers"></i>
                </div>
                <div class="link">
                    <a href="/backoffice/providers">Bailleurs</a>
                </div>
            </li>

            <li>
                <div class="image">
                    <i class="supports"></i>
                </div>
                <div class="link">
                    <a href="/backoffice/supports">Support</a>
                </div>
            </li>
            <li>
                <div class="image">
                    <i class="permissions"></i>
                </div>
                <div class="link">
                    <a href="/backoffice/permissions">Permissions</a>
                </div>
            </li>
            <li>
                <div class="image">
                    <i class="settings"></i>
                </div>
                <div class="link">
                    <a href="/backoffice/settings">Paramètres</a>
                </div>
            </li>
            -->
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
        <div class="main_section">
            @yield('content')
        </div>
    </div>
</main>
</body>
</html>

