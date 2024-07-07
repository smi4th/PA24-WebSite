<link rel="stylesheet" href="{{ asset('css/components_style/header.css') }}">
@props([
'connected' => false,
'profile' => false,
'light' => false
])
@php

    $imgPath = $dataUser->data[0]->imgPath;

@endphp
<header>
    <nav>
        <div class="logo">
            <img src="{{ asset('assets/images/logo/white-line-orange-bg-blue.png') }}" alt="logo" onclick="window.location.href='/'">
        </div>
        <div class="menu">
            @php

                $menuItems = $profile ? [
                    ['link' => '/profile', 'text' => 'Profil'],
                    ['link' => '/profile/prestations/management', 'text' => 'Gestion'],
                    ['link' => '/message', 'text' => 'Messages'],
                    ['link' => '/bills', 'text' => 'Factures'],
                    ['link' => '/basketPayment', 'text' => 'Panier'],
                    ['link' => '/planning', 'text' => 'Planning'],
                    ['link' => '#', 'text' => 'Avis'],
                ] : [
                    ['link' => '/profile', 'text' => 'Profil'],
                    ['link' => '#', 'text' => 'Voyager'],
                    ['link' => '#', 'text' => 'Prestation'],
                    ['link' => '#', 'text' => 'Louer'],
                    ['link' => '#', 'text' => 'Avis'],
                ];

                if($profile){
                    switch ($accountType){
                        case 'Handyman':
                            $menuItems[] = ['link' => '/profile/prestations', 'text' => 'Prestations'];
                            break;
                        case 'Loueur':
                            $menuItems[] = ['link' => '/profile/locations', 'text' => 'Locations'];
                            break;
                        default:
                            break;
                    }
                }

            @endphp
            <ul class="{{ $light ? '' : 'dark' }}">
                @foreach($menuItems as $item)
                <li class="{{ $light ? '' : 'dark' }}"><a href="{{ $item['link'] }}">{{ $item['text'] }}</a></li>
                @endforeach
            </ul>
        </div>
        @if($connected == true)
        <div class="profile">
            @if(strtolower($imgPath) == "null")
                <img src="{{ asset('/assets/images/default_user.png')}}" alt="profile">
            @else
                <img src="{{ asset(Storage::disk('wasabi')->url('pfp/'.$imgPath))}}" alt="profile">
            @endif
        </div>
        @else
        <div class="cta">
            <button onclick="window.location.href='/login'">Se connecter</button>
        </div>
        @endif
    </nav>
</header>
