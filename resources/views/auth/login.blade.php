<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Login page</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
    <main>
        <div class="layout">
            <div class="title">
                <h1>Espace connexion</h1>
                <h3>Re bonjour, ravi de vous revoir</h3>
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
            <form action="{{route('auth.login')}}" method="post">
                @method('POST')
                @csrf
                <div class="input">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}">
                </div>
                <div class="input">
                    <label for="password">Mot de passe</label>
                    <input type="password" name="password" id="password">
                </div>
                <div class="forgot">
                    <a href="/">Mot de passe oublié ?</a>
                </div>
                <div class="input_cta">
                    <button type="submit">Se connecter</button>
                </div>
            </form>
            <div class="cta">
                <p>Vous n'avez pas de compte ? <a href="{{route("auth.register")}}">Vener nous rejoindre</a></p>
            </div>
        </div>
    </main>
</body>
</html>
