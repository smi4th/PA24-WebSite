@push('management_profile')
    <link rel="stylesheet" href="{{ asset('css/profile/management_prestation.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
@endpush

@section('content')

    <div class="container_layout">
        <div class="title">
            <h1>Vos factures et fiche d'intervention</h1>
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
                        <h2>Vous n'avez pas de facture</h2>
                    </div>
                </div>
            @endif

            @foreach($prestations as $prestation)

            @endforeach
        </div>
@endsection
