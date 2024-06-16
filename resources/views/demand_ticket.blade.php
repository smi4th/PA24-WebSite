@push('demand_ticket')
    <link rel="stylesheet" href="{{ asset('css/ticket/demand_ticket.css') }}">
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
@endpush

@section('content')
    <div class="container_page">
        <div class="form_layout">
            <h1>Créer une demande de ticket</h1>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <form action="{{route('createDemand')}}" method="POST">
                @method('POST')
                @csrf
                <div class="form-group">
                    <label for="name">Titre de la demande</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="description">Description de la demande</label>
                    <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label for="status">Priorité de la demande</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="" style="display:none;" selected disabled>Choisir une priorité</option>
                        <option value="1">Basse</option>
                        <option value="2">Moyenne</option>
                        <option value="3">Haute</option>
                    </select>
                </div>
                <div class="CTA">
                    <button type="submit">Créer la demande</button>
                </div>
            </form>
        </div>
    </div>

@endsection
