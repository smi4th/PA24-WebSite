@push('locations_profile')

    <link rel="stylesheet" href="{{ asset('css/profile/prestations.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
@endpush

@section('content')


    <div class="container_layout">
        <div class="title">
            <h1>Vos locations</h1>
        </div>
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li> {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="create">
            <a href="{{route('showCreateLocation')}}">Créer une location</a>
        </div>

        <div class="all_services">

            @if(empty($locations))
                <div class="noPresta">
                    <h1>Vous n'avez pas encore de location, allez en créer une !</h1>
                </div>
            @endif
                <div class="container mt-4">
                    <div class="row">
                        @foreach($locations as $location)
                            <div class="col-md-4 mb-4">
                                <div class="card">

                                    @php
                                        $directory = 'locations/'.$location->uuid;
                                        $files = Storage::disk('wasabi')->files($directory);
                                        $imagePath = !empty($files) ? Storage::disk('wasabi')->url($files[0]) : asset('/assets/images/maison_default.png');
                                    @endphp

                                    <img src="{{ $imagePath }}" class="card-img-top" alt="Location Image">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $location->title }}</h5>
                                        <p class="card-text">{{ $location->description }}</p>
                                        <p class="card-text"><small class="text-muted">{{ $location->city }}, {{ $location->zip_code }}</small></p>
                                        @if ($location->validated == 0)
                                            <h3 class="info alert alert-secondary">Votre location est en attente de validation</h3>
                                        @else
                                            <h3 class="info alert alert-success">Votre location est validée</h3>
                                        @endif
                                        <a href="/travel/{{$location->uuid}}" class="btn btn-info link-light">L'offre</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
        </div>
    </div>


@endsection
