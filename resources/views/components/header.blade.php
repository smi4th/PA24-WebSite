<link rel="stylesheet" href="{{ asset('css/components_style/header.css') }}">
@props([
'connected' => false,
'profile' => false,
'light' => false
])
@php
$imgPathValue = $data['data'][0]['imgPath'];
$vide = ['NULL', '', ' '];
if (in_array($imgPathValue, $vide)) {
$imgPath = 'default_user.png';
} else {
$imgPath = $imgPathValue;
}
@endphp
<header>
    <nav>
        <div class="logo">
            <img src="{{ asset('/assets/images/SiteLogo.svg') }}" alt="logo" onclick="window.location.href='/'">
        </div>
        <div class="menu">
            @php
            $menuItems = $profile ? [
            ['link' => '/profile', 'text' => 'Profil'],
            ['link' => '#', 'text' => 'Gestion'],
            ['link' => '#', 'text' => 'Messages'],
            ['link' => '#', 'text' => 'Prestations'],
            ['link' => '#', 'text' => 'Planning'],
            ['link' => '#', 'text' => 'Avis'],
            ] : [
            ['link' => '/profile', 'text' => 'Profil'],
            ['link' => '#', 'text' => 'Voyager'],
            ['link' => '#', 'text' => 'Prestation'],
            ['link' => '#', 'text' => 'Louer'],
            ['link' => '#', 'text' => 'Avis'],
            ];
            @endphp
            <ul class="{{ $light ? '' : 'dark' }}">
                @foreach($menuItems as $item)
                <li class="{{ $light ? '' : 'dark' }}"><a href="{{ $item['link'] }}">{{ $item['text'] }}</a></li>
                @endforeach
            </ul>
        </div>
        @if($connected == true)
        <div class="profile">
            <img src="{{ asset('/assets/images/pfp/' . $imgPath)}}" alt="profile">
        </div>
        @else
        <div class="cta">
            <button onclick="window.location.href='/login'">Se connecter</button>
        </div>
        @endif
    </nav>
</header>