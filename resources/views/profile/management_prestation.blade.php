@push('management_profile')
    <link rel="stylesheet" href="{{ asset('css/profile/management_prestation.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
@endpush

@section('content')

    <div class="container_layout">
        <div class="title">
            <h1>Fiches d'interventions</h1>
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
        <div class="list_receipts">
            @if (count($prestations) == 0)
                <div class="receipt">
                    <div class="receipt_title">
                        <h2>Vous n'avez pas de fiche d'intervention à remplir</h2>
                    </div>
                </div>
            @endif

            @foreach($prestations as $prestation)

                <div class="receipt">
                    <div class="receipt_title">
                        <h2>{{$prestation->description}}</h2>
                    </div>
                    <div class="receipt_content">
                        <form method="POST" action="{{route("generateInterventionForm")}}">
                            @csrf
                            @method('POST')
                            <div class="receipt_info">
                                <p>Client : {{$customers[$prestation->customer]}}</p>
                                <input type="hidden" name="customer" value="{{$customers[$prestation->customer]}}">

                                <p>Date : {{$prestation->startTime}}</p>
                                <input type="hidden" name="startTime" value="{{$prestation->startTime}}">

                                <p>Durée : {{$prestation->duration}}</p>
                                <input type="hidden" name="duration" value="{{$prestation->duration}}">

                                <p>Payé ? : {{$prestation->status == 1 ? 'Oui' : 'Non'}}</p>
                                <input type="hidden" name="status" value="{{$prestation->status}}">

                                <p>Prix : {{$prestation->price}} €</p>
                                <input type="hidden" name="price" value="{{$prestation->price}}">

                                <textarea name="comment" placeholder="Commentaire (max 255)"></textarea>
                            </div>
                            <input type="hidden" name="prestation" value="{{$prestation->uuid}}">
                            <input type="hidden" name="basket" value="{{$prestation->basket}}">

                            <input type="submit" value="Fiche d'intervention">

                            @if(Storage::disk('wasabi')->exists('interventions/intervention_'.$prestation->basket.'.pdf'))
                                <a href="{{asset(Storage::disk('wasabi')->url('interventions/intervention_'.$prestation->basket.'.pdf'))}}" target="_blank">Voir la fiche d'intervention</a>
                            @endif
                        </form>
                    </div>
                    <div class="receipt_footer">
                        <form method="POST" action="">
                            @csrf
                            <input type="hidden" name="prestation" value="{{$prestation->uuid}}">
                            <input type="submit" value="Ouvrir la discussion">
                        </form>
                        <!--
                        <form method="POST" action="">
                            @csrf
                            <input type="hidden" name="prestation" value="{{$prestation->uuid}}">
                            <input type="submit" data-css="non" value="Refuser la commande">
                        </form>
                        -->
                    </div>
                </div>
            @endforeach

        </div>
@endsection
