@push('main_travel')
    <link rel="stylesheet" href="{{ asset('css/travel/main_travel.css') }}">
@endpush

@section('content')
    <div class="container">
        @php
            $offerCount = 0;
            $pubCount = 0;
            $totalPubs = count($publicities);
        @endphp
        @foreach($locations as $location)
            @if($location->validated == 1 || $admin)
                <div class="card_layout">
                    <div class="card">
                        <div class="image">
                            @if(strtolower($location->imgPath) == "null")
                                <img src="{{ asset('/assets/images/maison_default.png') }}" alt="image">
                            @else
                                <img src="{{ asset('locations/'.$location->uuid.'/'.$location->imgPath) }}" alt="image">
                            @endif
                        </div>
                        <div class="card_content">
                            @if ($location->validated == 0)
                                <h3 style="color: #D48383">{{ $location->title }}</h3>
                            @else
                                <h3>{{ $location->title }}</h3>
                            @endif
                            <p>{{ $location->price }}</p>
                            <a href="/travel/{{$location->uuid}}">Voir plus</a>
                        </div>
                    </div>
                </div>

                @php
                    $offerCount++;
                @endphp

                @if($offerCount % 3 == 0 && $pubCount < $totalPubs)
                    <div class="card_layout">
                        <div class="card">
                            <div class="image">
                                <img src="{{ asset('assets/publicity/' . $publicities[$pubCount]->getFilename()) }}" alt="publicity image">
                            </div>
                            <div class="card_content">
                                <h3>Publicité</h3>
                                <p>Une publicité pour vous</p>
                                <a href="https://www.youtube.com/watch?v=xvFZjo5PgG0">Voir plus</a>
                            </div>
                        </div>

                    </div>
                    @php
                        $pubCount++;
                    @endphp
                @endif
            @endif
        @endforeach

        @if($pubCount < $totalPubs)
            @for($i = $pubCount; $i < $totalPubs; $i++)
                <div class="card_layout">
                    <div class="card">
                        <div class="image">
                            <img src="{{ asset('assets/publicity/' . $publicities[$i]->getFilename()) }}" alt="publicity images">
                        </div>
                        <div class="card_content">
                            <h3>Publicité</h3>
                            <p>Une publicité pour vous</p>
                            <a href="https://www.youtube.com/watch?v=xvFZjo5PgG0">Voir plus</a>
                        </div>
                    </div>
                </div>
            @endfor
        @endif
    </div>
@endsection
