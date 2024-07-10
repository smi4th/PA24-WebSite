<!DOCTYPE HTML>
<html>
<head>
    <title>Administration Chatbot</title>
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/profile/main_profile.css') }}">
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        function enableEdit(button, textId) {
            var textElement = document.getElementById(textId);
            textElement.removeAttribute('disabled');
            textElement.focus();
            button.classList.remove('btn-warning');
            button.classList.add('btn-success');
            button.innerText = 'Sauvegarder';
            button.onclick = function() { saveEdit(button, textId); };
        }

        function saveEdit(button, textId) {
            var textElement = document.getElementById(textId);
            var textValue = textElement.value;
            var uuid = button.dataset.uuid;

            fetch(`/chatbot/admin?uuid=${uuid}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ text: textValue })
            }).then(response => {
                if (response.ok) {
                    textElement.setAttribute('disabled', 'disabled');
                    button.classList.remove('btn-success');
                    button.classList.add('btn-warning');
                    button.innerText = 'Modifier';
                    button.onclick = function() { enableEdit(button, textId); };
                } else {
                    alert('Erreur lors de la mise à jour du texte.');
                }
            }).catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de la mise à jour du texte.');
            });
        }
    </script>
</head>
<body>
<x-header :connected="true" :profile="false" :light="false" />
    <div class="container"> 
        <h1>Ajouter un mot-clé et texte associé</h1>
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <form action="/chatbot/admin" method="POST">
            @csrf
            <div class="mb-3">
                <label for="keyword" class="form-label">Mot-clé</label>
                <input type="text" class="form-control" id="keyword" name="keyword" required>
            </div>
            <div class="mb-3">
                <label for="text" class="form-label">Texte associé</label>
                <textarea class="form-control" id="text" name="text" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Ajouter</button>
        </form>
        <h2>Liste des mots-clés et textes associés</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">Mot-clé</th>
                    <th scope="col">Texte associé</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($keywords as $keyword)
                    <tr>
                        <td>{{ $keyword['keyword'] }}</td>
                        <td><textarea id="text-{{ $keyword['uuid'] }}" class="form-control" disabled>{{ $keyword['text'] }}</textarea></td>
                        <td>
                            <button type="button" class="btn btn-warning" data-uuid="{{ $keyword['uuid'] }}" onclick="enableEdit(this, 'text-{{ $keyword['uuid'] }}')">Modifier</button>
                            <form action="/chatbot/admin/{{ $keyword['uuid'] }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <x-footer />
</body>
</html>
