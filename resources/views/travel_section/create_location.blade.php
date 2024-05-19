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
                        <input type="number" id="surface" name="surface" value="{{ old('surface') }}">
                    </div>
                    <div class="form-group">
                        <label for="price_housing">Prix</label>
                        <input type="number" id="price_housing" name="price_housing" value="{{ old('price_housing') }}">
                    </div>
                    <div class="form-group">
                        <label for="street_nb">Numéro de rue</label>
                        <input type="number" id="street_nb" name="street_nb" value="{{ old('street_nb') }}">
                    </div>
                    <div class="form-group">
                        <label for="city">Ville</label>
                        <input type="text" id="city" name="city" value="{{ old('city') }}">
                    </div>
                    <div class="form-group">
                        <label for="zip_code">Code postal</label>
                        <input type="number" id="zip_code" name="zip_code" value="{{ old('zip_code') }}">
                    </div>
                    <div class="form-group">
                        <label for="street">Rue</label>
                        <input type="text" id="street" name="street" value="{{ old('street') }}">
                    </div>

                    <div class="form-group">
                        <label for="title">Titre</label>
                        <input type="text" id="title" name="title" placeholder="Titre de l'annonce" value="{{ old('title') }}">
                    </div>
                    <div class="form-group">
                        <label for="description_housing">Description</label>
                        <textarea id="description_housing" name="description_housing" placeholder="Description de l'annonce">{{ old('description_housing') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="house_type">Type de maison</label>
                        <select name="house_type" id="house_type">
                            <option selected disabled hidden>Choisir un type de maison</option>
                            @foreach($house_types as $house_type)
                                <option value="{{ $house_type->uuid }}">{{ $house_type->type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="imgPathHousing">Images du bien (la première photo uploader sera celle qui servira de photo de couverture)</label>
                        <input type="file" id="imgPathHousing" name="imgPathHousing[]" multiple>
                    </div>
                </div>
                <h2>Chambres</h2>
                <div class="bedrooms">
                    <div class="form-group">
                        <label for="nbPlaces_1">Nombre de places</label>
                        <input type="number" id="nbPlaces_1" name="nbPlaces[1]" value="{{ old('nbPlaces[1]') }}">
                    </div>
                    <div class="form-group">
                        <label for="price_1">Prix</label>
                        <input type="number" id="price_1" name="price[1]" value="{{ old('price[1]') }}">
                    </div>
                    <div class="form-group">
                        <label for="description_1">Description</label>
                        <textarea id="description_1" name="description[1]" placeholder="Description de la chambre">{{ old('description[1]') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="imgPathBed_1">Image de la chambre </label>
                        <input type="file" id="imgPathBed_1" name="imgPath[1]">
                    </div>
                </div>
                <h2>Equipements</h2>
                <div class="equipments">
                    <div class="form-group">
                        <label for="equipment_type_1">Type d'équipement</label>
                        <select name="equipment_type[1]" id="equipment_type_1">
                            <option selected disabled hidden>Choisir un type d'équipement</option>
                            @foreach($equipment_types as $equipment_type)
                                <option value="{{ $equipment_type->uuid }}">{{ $equipment_type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="nameEquipment_1">Nom de l'équipement</label>
                        <input type="text" id="nameEquipment_1" name="nameEquipment[1]" value="{{ old('nameEquipment[1]') }}">
                    </div>
                    <div class="form-group">
                        <label for="descriptionEquipment_1">Description de l'équipement</label>
                        <textarea id="descriptionEquipment_1" name="descriptionEquipment[1]" placeholder="Description de l'équipement">{{ old('descriptionEquipment[1]') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="imgPathEquipment_1">Image de l'équipement</label>
                        <input type="file" id="imgPathEquipment_1" name="imgPathEquipment[1]" value="{{ old('imgPathEquipment[1]') }}">
                    </div>
                    <div class="form-group">
                        <label for="priceEquipement_1">Prix</label>
                        <input type="number" id="priceEquipement_1" name="priceEquipement[1]" value="{{ old('priceEquipement[1]') }}">
                    </div>

                </div>
                <div class="CTA">
                    <button type="submit">Créer l'annonce</button>
                </div>
            </form>
            <button onclick="addChamber()">Ajouter une chambre</button>
            <button onclick="addEquipment()">Ajouter un équipement</button>
        </div>
    </div>
    <script>
        var countChamber = 1;
        var countEquipment = 1;

        function generateChamberOptions() {
            let options = '';
            for (let i = 1; i <= countChamber; i++) {
                options += `<option value="${i}">Chambre ${i}</option>`;
            }
            return options;
        }


        function addChamber(){
            let form = document.querySelector('form');
            let newChamber = document.createElement('div');
            countChamber++;
            newChamber.classList.add('bedrooms');
            newChamber.innerHTML = `
            <div class="form-group">
                <label for="nbPlaces_${countChamber}">Nombre de places</label>
                <input type="number" id="nbPlaces_${countChamber}" name="nbPlaces[${countChamber}]">
            </div>
            <div class="form-group">
                <label for="price_${countChamber}">Prix</label>
                <input type="number" id="price_${countChamber}" name="price[${countChamber}]">
            </div>
            <div class="form-group">
                <label for="description_${countChamber}">Description</label>
                <textarea id="description_${countChamber}" name="description[${countChamber}]" placeholder="Description de la chambre"></textarea>
            </div>
            <div class="form-group">
                <label for="imgPathBed_${countChamber}">Image de la chambre </label>
                <input type="file" id="imgPathBed_${countChamber}" name="imgPath[${countChamber}]">
            </div>
            <button type="button" onclick="removeChamber(this)">Supprimer la chambre</button>
        `;
            form.insertAdjacentElement('beforeend', newChamber);
        }

        function addEquipment(){
            let form = document.querySelector('form');
            let newEquipment = document.createElement('div');
            countEquipment++;
            newEquipment.classList.add('equipments');
            newEquipment.innerHTML = `
            <div class="form-group">
                <label for="equipment_type_${countEquipment}">Type d'équipement</label>
                <select name="equipment_type[${countEquipment}]" id="equipment_type_${countEquipment}">
                    <option selected disabled hidden>Choisir un type d'équipement</option>
                    @foreach($equipment_types as $equipment_type)
            <option value="{{ $equipment_type->uuid }}">{{ $equipment_type->name }}</option>
                    @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="nameEquipment_${countEquipment}">Nom de l'équipement</label>
                <input type="text" id="nameEquipment_${countEquipment}" name="nameEquipment[${countEquipment}]">
                </div>
                <div class="form-group">
                    <label for="descriptionEquipment_${countEquipment}">Description de l'équipement</label>
                    <textarea id="descriptionEquipment_${countEquipment}" name="descriptionEquipment[${countEquipment}]" placeholder="Description de l'équipement"></textarea>
                </div>
                <div class="form-group">
                    <label for="imgPathEquipment_${countEquipment}">Image de l'équipement</label>
                    <input type="file" id="imgPathEquipment_${countEquipment}" name="imgPathEquipment[${countEquipment}]">
                </div>
                <div class="form-group">
                    <label for="priceEquipement_${countEquipment}">Prix</label>
                    <input type="number" id="priceEquipement_${countEquipment}" name="priceEquipement[${countEquipment}]">
                </div>

                <button type="button" onclick="removeEquipment(this)">Supprimer l'équipement</button>
        `;
            form.insertAdjacentElement('beforeend', newEquipment);
        }

        function removeChamber(button){
            button.parentElement.remove();
            countChamber--;
        }

        function removeEquipment(button){
            button.parentElement.remove();
            countEquipment--;
        }
    </script>
@endsection
