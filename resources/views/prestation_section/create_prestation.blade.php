@push('create_prestation')
    <link rel="stylesheet" href="{{ asset('css/prestation/create_prestation.css') }}">
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css' rel='stylesheet'>
@endpush

@section('content')

<div class="container_layout">
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li> {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="title">
        <h1>Création d'offre de prestation</h1>
    </div>

    <form action="{{route('createPrestation')}}" method="POST" enctype="multipart/form-data">
        @csrf
        @METHOD('POST')
        <div class="form-group">
            <label for="description">Description</label>
            <input type="text" class="form-control" id="description" name="description" required>
        </div>
        <div class="form-group">
            <label for="price">Prix</label>
            <input type="number" class="form-control" id="price" name="price" required>
        </div>
        <div class="form-group">
            <label for="duration">Durée</label>
            <input type="time" class="form-control" id="duration" name="duration" required min="00:10" max="99:00">
        </div>

        <div class="form-group">
            <label for="imgPath">Image</label>
            <input type="file" class="form-control" id="imgPath" name="imgPath" required>
        </div>
        <div class="form-group">
            <select class="form-select" name="service_id" id="service_id">
                @foreach($servicesTypes as $service)
                    <option value="{{$service->uuid}}">{{$service->type}}</option>
                @endforeach
            </select>
        </div>

        <button type="submit">Créer</button>

    </form>

    <h2>La catégorie de service n'existe pas ? Vous pouvez quand même la créer</h2>
    <form action="{{route('createService')}}" method="POST" enctype="multipart/form-data">
        @csrf
        @METHOD('POST')
        <div class="form-group">
            <label for="service">Catégorie</label>
            <input type="text" class="form-control" id="service" name="service" required>
        </div>
        <div class="form-group">
            <label for="imgPath">Image (optionnel)</label>
            <input type="file" id="imgPath" name="img">
        </div>
        <button type="submit">Créer</button>
    </form>

    <h6>La catégorie comme votre offre sera soumis à vérification</h6>

</div>

@endsection
