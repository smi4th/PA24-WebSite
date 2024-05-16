@push('offers')
    <link rel="stylesheet" href="{{ asset('css/travel/offers.css') }}">

@endpush
@section('content')

    <div class="container">
        <div class="offer_image_layout">
            @if($location->imgPath == "NULL")
                <div class="image">
                    <img src="{{ asset('/assets/images/maison_default.png') }}" alt="image">
                </div>
            @else
                <div class="image">
                    <img src="{{ asset($location->imgPath)}}" alt="image">
                </div>
            @endif
            <div class="content">
                <div class="title">{{$location->description}}</div>
                <div class="price">{{$location->price}}€/nuit</div>
                <div class="addresse">{{$location->street_nb}} {{$location->street}} {{$location->city}} {{$location->zip_code}}</div>
            </div>
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

        <div class="offer_form">
            <form action="reservation/{{$location->uuid}}" method="GET">
                @csrf
                @method('GET')
                <div class="form-group">
                    <label for="date_start">Date d'arriver</label>
                    <input type="date" id="date_start" name="date_start">
                </div>
                <div class="form-group">
                    <label for="date_end">Date de départ</label>
                    <input type="date" id="date_end" name="date_end">
                </div>
                <button type="submit">Réserver</button>
            </form>
        </div>
        <div class="offer_review">
            <div class="title">Avis</div>
            <div class="layout">
                @for($i = 0; $i < count($reviews); $i++)
                    <div class="review">
                        <div class="user_data">
                            @if(strtolower($users[$i]->imgPath) == "null")
                                <div class="profile_picture">
                                    <img src="{{ asset('/assets/images/default_user.png') }}" alt="image">
                                </div>
                            @else
                                <div class="profile_picture">
                                    <img src="{{ asset($users[$i]->imgPath) }}" alt="image">
                                </div>
                            @endif
                            <div class="user">{{$users[$i]->username}}</div>
                        </div>
                        <div class="content">{{$reviews[$i]->content}}</div>
                        <div class="rating">
                            @for($j = 0; $j < $reviews[$i]->note; $j++)
                                <i class="star"></i>
                            @endfor
                        </div>
                    </div>
                @endfor
            </div>
        </div>
        <div class="offer_equipement">
            <div class="title">Equipement</div>
            <div class="content">
                <ul>
                    @foreach($equipments as $equipment)
                        <li class="equipment_item">
                            <div class="layout">
                                <div class="image">
                                    @if(strtolower($equipment->imgPath) == "null")
                                        <img src="{{ asset('/assets/images/equipment_default.jpg') }}" alt="image">
                                    @else
                                        <img src="{{ asset($equipment->imgPath) }}" alt="image">
                                    @endif
                                </div>
                            </div>
                            <div class="content">
                                <div class="name">{{$equipment->name}}</div>
                                <div class="description">{{$equipment->description}}</div>
                                @if($equipment->price <= 0)
                                    <div class="price">Gratuit</div>
                                @else
                                    <div class="price">{{$equipment->price }}€</div>
                                @endif
                            </div>

                        </li>
                    @endforeach
                </ul>
        </div>
    </div>
@endsection
