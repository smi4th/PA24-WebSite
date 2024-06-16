@push('sub_prestation')
    <link rel="stylesheet" href="{{ asset('css/prestation/main_prestation.css') }}">
@endpush

@section('content')
    <div class="container">
        <div class="all_services">
        @foreach($services as $service)
            <div class="card_layout">
                <div class="card">
                    <div class="image">
                        @if(strtolower($service->imgPath) == "null")
                            <img src="{{ asset('/assets/images/default_services.jpg') }}" alt="image">
                        @else
                            <img src="{{ Storage::disk('s3')->url($service->imgPath) }}}}" alt="image">
                        @endif
                    </div>
                    <div class="card_content">
                        <a href="/prestations/{{$type}}/{{$service->uuid}}">{{$service->description}}</a>
                    </div>
                </div>
            </div>

        @endforeach
        </div>
    </div>
@endsection
