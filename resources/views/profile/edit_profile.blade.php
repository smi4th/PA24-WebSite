<!DOCTYPE html>
<html>

<head>
    <title>Modifier le {{ $nom }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/profile/main_profile.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css?family=Roboto|Inter|Karla|Manrope&display=swap" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="icon" href="{{ asset('logo.png') }}" />
</head>

<body>
    <x-header :connected="true" :profile="true" :light="false" />

    @if($inputName != 'password' && $inputName != 'name')
    <div class="page" id="page1">
        <h1>Changer de {{ $nom }}</h1>
        <div class="seperate"></div>
        <div class="second-section">
            <form method="post" action="{{ route('profile') }}">
                @csrf
                <div class="inputbox">
                    <label for="name">Nouveau {{ $nom }}</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                </div>

                <div class="inputbox">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary">Valider</button>
            </form>
        </div>
    </div>
    @elseif($inputName == 'password')
    <div class="page" id="page1">
        <h1>Changer de {{ $nom }}</h1>
        <div class="seperate"></div>
        <div class="second-section">
            <form method="post" action="{{ route('update_profile') }}">
                @csrf
                <div class="inputbox">
                    <label for="newpassword">Nouveau {{ $nom }}</label>
                    <input type="password" id="newpassword" name="newpassword" required>
                </div>

                <div class="inputbox">
                    <label for="newpasswordconfirm">Confirmer le {{ $nom }}</label>
                    <input type="password" id="newpasswordconfirm" name="newpasswordconfirm" required>
                </div>

                <div class="inputbox">
                    <label for="password">Ancien {{ $nom }}</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary">Valider</button>
            </form>
        </div>
    </div>
    @elseif($inputName == 'name')
    <div class="page" id="page1">
        <h1>Changer de {{ $nom }}</h1>
        <div class="seperate"></div>
        <div class="second-section">
            <form method="post" action="{{ route('update_profile') }}">
                @csrf
                <div class="inputbox">
                    <label for="firstname">Nouveau prénom</label>
                    <input type="text" id="firstname" name="firstname" value="{{ old('firstname') }}" required>
                </div>

                <div class="inputbox">
                    <label for="lastname">Nouveau nom</label>
                    <input type="text" id="lastname" name="lastname" value="{{ old('lastname') }}" required>
                </div>

                <div class="inputbox">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary">Valider</button>
            </form>
        </div>
    @endif

    <x-footer />
</body>

</html>