<link rel="stylesheet" href="{{ asset('css/components_style/header.css') }}">
@props([
    'connected' => false,
    'profile' => false,
    'light' => false
])
<header>
    <nav>
        <div class="logo">
            <img src="{{ asset('/assets/images/SiteLogo.svg') }}" alt="logo" onclick="window.location.href='/'">
        </div>
        <div class="menu">
            @if($profile == true)
                @if($light == true)
                    <ul>
                        <li><a href="/profile">Profil</a></li>
                        <li><a href="#">Gestion</a></li>
                        <li><a href="#">Messages</a></li>
                        <li><a href="#">Prestations</a></li>
                        <li><a href="#">Planning</a></li>
                        <li><a href="#">Avis</a></li>
                    </ul>
                @else
                    <ul>
                        <li class ="dark"><a href="/profile">Profil</a></li>
                        <li class ="dark"><a href="#">Gestion</a></li>
                        <li class ="dark"><a href="#">Messages</a></li>
                        <li class ="dark"><a href="#">Prestations</a></li>
                        <li class ="dark"><a href="#">Planning</a></li>
                        <li class ="dark"><a href="#">Avis</a></li>
                    </ul>
                @endif
            @else
                @if($light == true)
                    <ul>
                        <li><a href="/profile">Profil</a></li>
                        <li><a href="/travel">Voyager</a></li>
                        <li><a href="#">Prestation</a></li>
                        <li><a href="#">Louer</a></li>
                        <li><a href="#">Avis</a></li>
                    </ul>
                @else
                    <ul>
                        <li class ="dark"><a href="/profile">Profil</a></li>
                        <li class ="dark"><a href="/travel">Voyager</a></li>
                        <li class ="dark"><a href="#">Prestation</a></li>
                        <li class ="dark"><a href="#">Louer</a></li>
                        <li class ="dark"><a href="#">Avis</a></li>
                    </ul>
                @endif
            @endif
        </div>
        @if($connected == true)
            <div class="profile">
                <img src="{{ asset('/assets/images/default_user.png')}}" alt="profile">
            </div>
        @else
        <div class="cta">
            <button onclick="window.location.href='/login'">Se connecter</button>
        </div>
        @endif
    </nav>
</header>
