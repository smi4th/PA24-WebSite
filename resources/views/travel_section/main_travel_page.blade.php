@push('main_travel')
    <link rel="stylesheet" href="{{ asset('css/travel/main_travel.css') }}">
@endpush

@section('content')
    <div class="container">
        @foreach($locations as $location)
            <div class="card_layout">
                <div class="card">
                    <div class="image">@if($location->imgPath == "NULL")<img src="{{ asset('/assets/images/maison_default.png') }}" alt="image">@else<img src="{{ asset($location->imgPath) ?? asset('/assets/images/default_user.png') }}" alt="image">@endif
                    </div>
                    <div class="card_content">
                        <h3>{{ $location->title}}</h3>
                        <p>{{ $location->price }}</p>
                        <a href="/travel/{{$location->uuid}}">Voir plus</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

@endsection
