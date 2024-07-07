@push('sub_prestation')
    <link rel="stylesheet" href="{{ asset('css/prestation/main_prestation.css') }}">
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css' rel='stylesheet'>
@endpush

@section('content')
    <div class="container">
        <div class="all_services">
        @foreach($services as $service)
            @if($service->validated == 1)
                <div class="card_layout">
                    <div class="card">
                        <div class="image">
                            @if(strtolower($service->imgPath) == "null")
                                <img src="{{ asset('/assets/images/default_services.jpg') }}" alt="image">
                            @else
                                <img src="{{ asset(Storage::disk('wasabi')->url('services/'.$service->uuid.'/'.$service->imgPath))}}" alt="image">
                            @endif
                        </div>
                        <div class="card_content">
                            <a href="/prestations/{{$type}}/{{$service->uuid}}">{{$service->description}}</a>
                        </div>
                    </div>
                </div>
                @elseif($service->validated == 0 && $admin)
                <div class="card_layout">
                    <div class="action">
                        <button onclick="window.location.href='/prestations/{{$type}}/{{$service->uuid}}/delete'" class="btn btn-danger">Supprimer</button>
                        <button onclick="window.location.href='/prestations/{{$type}}/{{$service->uuid}}/approuve'" class="btn btn-success">Approuver</button>
                    </div>
                    <div class="card" style="border: 4px solid rgba(255,0,0,0.54);">
                        <div class="image">
                            @if(strtolower($service->imgPath) == "null")
                                <img src="{{ asset('/assets/images/default_services.jpg') }}" alt="image">
                            @else
                                <img src="{{ asset(Storage::disk('wasabi')->url('services/'.$service->uuid.'/'.$service->imgPath))}}" alt="image">
                            @endif
                        </div>
                        <div class="card_content">
                            <a href="/prestations/{{$type}}/{{$service->uuid}}">{{$service->description}}</a>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
        </div>
    </div>
@endsection
