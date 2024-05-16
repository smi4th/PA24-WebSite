@push('create_location')
    <link rel="stylesheet" href="{{ asset('css/travel/create_location.css') }}">
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
@endpush

@section('content')
    <div class="container_form">
        <div class="info">
            <h1>Créer une annonce</h1>
            <p>Remplissez les informations ci-dessous pour créer une annonce</p>
            <p>Le prix de la maison sera appliqué si toutes les chambres sont réservées par la même personne</p>
            <p>Les chambres peuvent être réservées individuellement</p>
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
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <div class="form_layout">
            <form action="/travel/createLocation" method="POST" enctype="multipart/form-data">
                @csrf
                @method('POST')
                <div class="housing">
                        <div class="form-group">
                            <label for="surface">Surface</label>
                            <input type="number" id="surface" name="surface">
                        </div>
                        <div class="form-group">
                            <label for="price">Prix</label>
                            <input type="number" id="price" name="price_housing">
                        </div>
                        <div class="form-group">
                            <label for="street_nb">Numéro de rue</label>
                            <input type="number" id="street_nb" name="street_nb">
                        </div>
                        <div class="form-group">
                            <label for="city">Ville</label>
                            <input type="text" id="city" name="city">
                        </div>
                        <div class="form-group">
                            <label for="zip_code">Code postal</label>
                            <input type="number" id="zip_code" name="zip_code">
                        </div>
                        <div class="form-group">
                            <label for="street">Rue</label>
                            <input type="text" id="street" name="street">
                        </div>

                        <div class="form-group">
                            <label for="description">Titre</label>
                            <input type="text" id="title" name="title" placeholder="Titre de l'annonce">
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description_housing" placeholder="Description de l'annonce"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="house_type">Type de maison</label>
                            <select name="house_type" id="house_type">
                                <option selected disabled hidden>Choisir un type de maison</option>
                                @foreach($house_types as $house_type)
                                    <option value="{{$house_type->uuid}}">{{$house_type->type}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="imgPath">Images du bien (la première photo uploader sera celle qui servira de photo de couverture)</label>
                            <input type="file" id="imgPath" name="imgPathHousing[]" multiple>
                        </div>
                </div>
                <h2>Chambres</h2>
                <div class="bedrooms">
                    <div class="form-group">
                        <label for="nbPlaces">Nombre de places</label>
                        <input type="number" id="nbPlaces_1" name="nbPlaces[1]">
                    </div>
                    <div class="form-group">
                        <label for="price">Prix</label>
                        <input type="number" id="price_1" name="price[1]">
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description_1" name="description[1]" placeholder="Description de la chambre"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="imgPath">Image de la chambre </label>
                        <input type="file" id="imgPathBed_1" name="imgPath[1]">
                    </div>
                </div>
                <div class="CTA">
                    <button type="submit">Créer l'annonce</button>
                </div>
            </form>
            <button onclick="addChamber()">Ajouter une chambre</button>
        </div>
    </div>
    <script>
        var countChamber = 1;
        function addChamber(){
            let form = document.querySelector('form');
            let newChamber = document.createElement('div');
            countChamber++;
            newChamber.classList.add('bedrooms');
            newChamber.innerHTML = `
                <div class="form-group">
                    <label for="nbPlaces">Nombre de places</label>
                    <input type="number" id="nbPlaces_${countChamber}" name="nbPlaces[${countChamber}]">
                </div>
                <div class="form-group">
                    <label for="price">Prix</label>
                    <input type="number" id="price_${countChamber}" name="price[${countChamber}]">
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description_${countChamber}" name="description[${countChamber}]" placeholder="Description de la chambre"></textarea>
                </div>
                <div class="form-group">
                    <label for="imgPath">Image de la chambre </label>
                    <input type="file" id="imgPathBed_${countChamber}" name="imgPath[${countChamber}]">
                </div>
                <button type="button" onclick="removeChamber(this)">Supprimer la chambre</button>
            `;
            form.insertAdjacentElement('beforeend', newChamber);
        }
        function removeChamber(button){
            button.parentElement.remove();
        }
    </script>
@endsection
