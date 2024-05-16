@push('styles_statistics')
    <link rel="stylesheet" href="{{ asset('css/backoffice_style/statistics.css') }}">

@endpush

@section('content')
    <h1>Statistiques</h1>
    <div class="container">
        <div class="chart"><canvas id="accounts"></canvas></div>
        <div class="chart"><canvas id="numberOffers"></canvas></div>
        <div class="chart"><canvas id="numberReservations"></canvas></div>
        <div class="chart"><canvas id="numberMessages"></canvas></div>
        <div class="chart"><canvas id="prestations"></canvas></div>
    </div>
    <script src=" https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js "></script>

    <script type="module">

        var accounts = @php echo $accounts @endphp ;

        accounts = new Map(Object.entries(accounts));

        var ctx = document.getElementById('accounts').getContext('2d');

        const data = {
            labels: [...accounts.keys()],
            datasets: [{
                label: 'Nombre de comptes',
                data: [...accounts.values()],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderWidth: 1
            }],
            hoverOffset: 4
        };
        const config = {
            type: 'doughnut',
            data: data,
        };

        new Chart(ctx, config);

        var numberMessages = @php echo $numberMessages @endphp ;

        numberMessages = new Map(Object.entries(numberMessages));

        var ctx = document.getElementById('numberMessages').getContext('2d');

        //line chart with number of messages per day
        const dataMessages = {
            labels: [...numberMessages.keys()],
            datasets: [{
                label: 'Nombre de messages',
                data: [...numberMessages.values()],
                fill: false,
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        };
        const configMessages = {
            type: 'line',
            data: dataMessages,
        };

        new Chart(ctx, configMessages);


    </script>


@endsection
