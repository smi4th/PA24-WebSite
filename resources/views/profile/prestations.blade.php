@push('prestations_profile')
    <link rel="stylesheet" href="{{ asset('css/profile/prestations.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
@endpush

@section('content')

    <div class="container_layout">
        <div class="title">
            <h1>Vos services</h1>
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
            <a href="{{route('createPrestation')}}">Créer une prestation</a>
        </div>

        <div class="all_services">

            @if(empty($prestations))
                <div class="noPresta">
                    <h1>Vous n'avez pas encore de prestation, allez en créer une !</h1>
                </div>
            @endif
            @foreach($prestations as $prestation)
                <div class="card_layout">
                    <div class="card">
                        <div class="image">
                            @if(strtolower($prestation->imgPath) == "null")
                                <img src="{{ asset('/assets/images/default_services.jpg') }}" alt="image">
                            @else
                                <img
                                    src="{{ asset(Storage::disk('wasabi')->url('services/'.$prestation->uuid.'/'.$prestation->imgPath))}}" alt="image">
                            @endif
                        </div>
                        <div class="card_content">
                            @if ($prestation->validated == 0)
                                <h3 class="info">Votre prestation est en attente de validation</h3>
                            @endif
                            <form action="{{route('updatePrestation',['id'=>$prestation->uuid])}}" method="post" enctype="multipart/form-data">
                                @csrf
                                @method('post')
                                <input type="text" name="description" value="{{$prestation->description}}">
                                <input type="time" name="duration" value="{{$prestation->duration}}">
                                <input type="number" name="price" value="{{$prestation->price}}">

                                <input type="file" name="image" accept="image/*">

                                <select name="category">
                                    @foreach($servicesTypes as $serviceType)
                                        @if ($serviceType->uuid == $prestation->service_type)
                                            <option value="{{$serviceType->uuid}}" selected>{{$serviceType->type}}</option>
                                        @else
                                            <option value="{{$serviceType->uuid}}">{{$serviceType->type}}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <input type="submit" value="Modifier">
                                <h3 class="info">Si vous modifier votre offre elle sera soumise à nouveau à modification !</h3>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

@endsection
