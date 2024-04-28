<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Login page</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
    <main>
        <div class="layout">
            <div class="title">
                <h1>Espace connexion</h1>
                <h3>Re bonjour, ravi de vous revoir</h3>
            </div>
            <form action="/" method="post">
                @method('POST')
                @csrf
                <div class="input">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required>
                </div>
                <div class="input">
                    <label for="password">Mot de passe</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <div class="forgot">
                    <a href="/">Mot de passe oublié ?</a>
                </div>
                <div class="input_cta">
                    <button type="submit">Se connecter</button>
                </div>
            </form>
            <div class="cta">
                <p>Vous n'avez pas de compte ? <a href="/register">Vener nous rejoindre</a></p>
            </div>
        </div>
    </main>
</body>
</html>
