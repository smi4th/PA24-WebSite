@push('main_prestation')
    <link rel="stylesheet" href="{{ asset('css/prestation/main_prestation.css') }}">
@endpush

@section('content')
    <div class="container">
        <div class="all_services">
        @foreach($servicesTypes as $services)
            <div class="card_layout">
                <div class="card">
                    <div class="image">
                        @if(strtolower($services->imgPath) == "null")
                            <img src="{{ asset('/assets/images/default_services.jpg') }}" alt="images">
                        @else
                            <img src="{{ Storage::disk('s3')->url($services->imgPath) }}}}" alt="image">
                        @endif
                    </div>
                    <div class="card_content">
                        <a href="/prestations/{{$services->uuid}}">{{$services->type}}</a>
                    </div>
                </div>
            </div>

        @endforeach
        </div>
    </div>

@endsection
