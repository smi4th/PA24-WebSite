@push('bills_profile')
    <link rel="stylesheet" href="{{ asset('css/profile/bills.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
@endpush

@section('content')
    <div class="container_layout">
        <div class="title_bills">
            <h1>Factures</h1>
        </div>
        <div class="content_bills">
            @if(count($bills) == 0)
                <h3>Aucune facture enregistrer pour le moment</h3>
            @endif

            @foreach($bills as $bill)
                <div class="bill">
                    @if(!Storage::disk('wasabi')->exists('receipts/receipt_'.$bill.'.pdf')){
                        <h3>Facture indisponible pour le moment</h3>
                    @else
                        <a download="{{'receipt_'.$bill.'.pdf'}}" href="{{Storage::disk('wasabi')->url('receipts/receipt_'.$bill.'.pdf')}}">Facture {{$bill}}</a>
                    @endif
                </div>

          @endforeach
        </div>
    </div>
@endsection
