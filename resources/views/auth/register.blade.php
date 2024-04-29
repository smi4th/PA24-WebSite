<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Login page</title>
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
<main>
    <div class="layout">
        <div class="title">
            <h1>Espace inscription</h1>
            <h3>Bonjour, ravi de vous voir</h3>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{route('auth.register')}}" method="post">
            @method('POST')
            @csrf
            <div class="input">
                <label for="firstname">Prénom</label>
                <input type="text" name="firstname" id="firstname" value="{{ old('firstname') }}">
            </div>
            <div class="input">
                <label for="lastname">Nom</label>
                <input type="text" name="lastname" id="lastname" value="{{ old('lastname') }}">
            </div>
            <div class="input">
                <label for="username">Nom d'utilisateur</label>
                <input type="text" name="username" id="username" value="{{ old('username') }}">
            </div>

            <div class="input">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}">
            </div>
            <div class="input">
                <label for="password">Mot de passe</label>
                <input type="password" name="password" id="password">
            </div>
            <div class="input">
                <label for="password_confirmation">Confirmer le mot de passe</label>
                <input type="password" name="password_confirmation" id="password_confirmation">
            </div>

                <div class="input">
                    <label for="account_type">Type de compte</label>
                    <select name="account_type" id="account_type">
                        @foreach($data as $account)
                            <option value="{{$account->id}}">{{$account->title}}</option>
                        @endforeach
                    </select>
                </div>

            <div class="input_cta">
                <button type="submit">Inscription</button>
            </div>
        </form>
    </div>
</main>
</body>
</html>
