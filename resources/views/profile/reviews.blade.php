@push('reviews')
    <link rel="stylesheet" href="{{ asset('css/profile/reviews.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

@endpush

@section('content')

    <div class="container">
    @foreach($errors->all() as $error)
            <div class="alert alert-danger" role="alert">
                {{$error}}
            </div>
        @endforeach
        @if(session('success'))
            <div class="alert alert-success" role="alert">
                {{session('success')}}
            </div>
        @endif
        <h1>Donnez votre avis</h1>

        <div class="all_reviews">
            @foreach($basketsPaid as $basket)
                <h1>Panier</h1>
                @foreach($basket->HOUSING as $housing)
                    @php
                        $find = false;
                    @endphp
                    @foreach($reviews as $review)
                        @if($review->housing == $housing->uuid)
                            @php
                                $find = true;
                            @endphp
                        @endif
                    @endforeach
                    @if(!$find)
                        <div class="card_layout">
                            <h2>{{$housing->description}}</h2>
                            <div class="image">
                                @if(strtolower($housing->imgPath) == "null")
                                    <img src="{{ asset('/assets/images/maison_default.png') }}" alt="images">
                                @else
                                    <img src="{{ asset($housing->imgPath)}}" alt="image">
                                @endif
                            </div>
                            <form method="POST" action="/profile/addReview">
                                @csrf
                                <input type="hidden" name="housing" value="{{$housing->uuid}}">
                                <input type="text" name="comment" placeholder="Votre avis" value = {{old('comment')}} >
                                <input type="number" name="note" min="1" max="5" placeholder="Note" value = {{old('note')}} >
                                <input type="submit" value="Envoyer">
                            </form>
                        </div>
                    @else
                        <div class="card_layout">
                            <div class="card_content">
                                <p>Vous avez déjà laissé un avis pour ce logement</p>
                            </div>
                        </div>
                    @endif
                @endforeach
                @foreach($basket->BEDROOMS as $bedRooms)
                    @php
                        $find = false;
                    @endphp
                    @foreach($reviews as $review)
                        @if($review->bedRoom == $bedRooms->uuid)
                            @php
                                $find = true;
                            @endphp
                        @endif
                    @endforeach
                    @if(!$find)
                        <div class="card_layout">
                            <h2>{{$bedRooms->description}}</h2>
                            <div class="image">
                                @if(strtolower($bedRooms->imgPath) == "null")
                                    <img src="{{ asset('/assets/images/default_bed_room.jpg') }}" alt="images">
                                @else
                                    <img src="{{ asset($bedRooms->imgPath)}}" alt="image">
                                @endif
                            </div>
                            <form method="POST" action="/profile/addReview">
                                @csrf
                                <input type="hidden" name="bedroom" value="{{$bedRooms->uuid}}">
                                <input type="text" name="comment" placeholder="Votre avis" value = {{old('comment')}} >
                                <input type="number" name="note" min="1" max="5" placeholder="Note" value = {{old('note')}} >
                                <input type="submit" value="Envoyer">

                            </form>
                        </div>
                    @else
                        <div class="card_layout">
                            <div class="card_content">
                                <p>Vous avez déjà laissé un avis pour cette chambre</p>
                            </div>
                        </div>
                    @endif
                @endforeach
                @foreach($basket->SERVICES as $services)
                    @php
                        $find = false;
                    @endphp
                    @foreach($reviews as $review)
                        @if($review->service == $services->uuid)
                            @php
                                $find = true;
                            @endphp
                        @endif
                    @endforeach
                    @if(!$find)

                        <div class="card_layout">
                            <h2>{{$services->type}}</h2>
                            <p>{{$services->description}}</p>
                            <div class="image">
                                @if(strtolower($services->imgPath) == "null")
                                    <img src="{{ asset('/assets/images/default_services.jpg') }}" alt="images">
                                @else
                                    <img src="{{ asset($services->imgPath)}}" alt="image">
                                @endif
                            </div>
                            <form method="POST" action="/profile/addReview">
                                @csrf
                                <input type="hidden" name="service" value="{{$services->uuid}}">
                                <input type="text" name="comment" placeholder="Votre avis" value = {{old('comment')}} >
                                <input type="number" name="note" min="1" max="5" placeholder="Note" value = {{old('note')}} >
                                <input type="submit" value="Envoyer">

                            </form>
                        </div>
                    @else
                        <div class="card_layout">
                            <div class="card_content">
                                <p>Vous avez déjà laissé un avis pour ce service</p>
                            </div>
                        </div>
                    @endif
                @endforeach
            @endforeach
        </div>
    </div>

@endsection
