
@push('styles_index')
    <link rel="stylesheet" href="{{ asset('css/backoffice_accueil.css') }}">
@endpush

@section('content')
<div class="main_section">
    <div class="section">
        <ul class="stat_card">
            <li>
                <h3>Nombre de visites</h3>
                <p>100</p>
            </li>
            <li>
                <h3>Nouveaux utilisateurs</h3>
                <p>10</p>
            </li>
            <li>
                <h3>Nombre de réservations</h3>
                <p>5</p>
            </li>
            <li>
                <h3>Prestataires actifs</h3>
                <p>20</p>
            </li>
        </ul>
    </div>
    <div class="section">
        <div class="second_stat">
            <div class="graph">
            </div>
            <div class="graph_stats">
                <ul>
                    <li>
                        <h4>Ticket résolu</h4>
                        <p>100</p>
                    </li>
                    <li>
                        <h4>Temps moyen de réponse</h4>
                        <p>10 minutes</p>
                    </li>
                    <li>
                        <h4>Temps moyen de résolution</h4>
                        <p>1 heure</p>
                    </li>
                    <li>
                        <h4>Nombre de tickets ouverts</h4>
                        <p>5</p>
                    </li>
                    <li>
                        <h4>Nombre de tickets fermés</h4>
                        <p>10</p>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="section">
        <div class="task_card">
            <div class="task_card_header">
                <h3>Tickets en cours</h3>
                <a href="/">Tout voir</a>
            </div>
            <div class="task_card_content">
                <ul>
                    <li>
                        <h4>Problème de connexion</h4>
                        <p>En attente</p>
                    </li>
                    <li>
                        <h4>Problème de paiement</h4>
                        <p>En attente</p>
                    </li>
                    <li>
                        <h4>Problème de réservation</h4>
                        <p>En attente</p>
                    </li>
                    <li>
                        <h4>Problème de connexion</h4>
                        <p>En attente</p>
                    </li>
                    <li>
                        <h4>Problème de connexion</h4>
                        <p>En attente</p>
                    </li>
                </ul>
            </div>
        </div>
        <div class="task_card">
            <div class="task_card_header">
                <h3>Prochaines réservations</h3>
                <a href="/">Tout voir</a>
            </div>
            <div class="task_card_content">
                <ul>
                    <li>
                        <h4>Problème de connexion</h4>
                        <p>En attente</p>
                    </li>
                    <li>
                        <h4>Problème de paiement</h4>
                        <p>En attente</p>
                    </li>
                    <li>
                        <h4>Problème de réservation</h4>
                        <p>En attente</p>
                    </li>
                    <li>
                        <h4>Problème de connexion</h4>
                        <p>En attente</p>
                    </li>
                    <li>
                        <h4>Problème de connexion</h4>
                        <p>En attente</p>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
