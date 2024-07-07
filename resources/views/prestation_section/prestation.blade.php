@push('prestation')
    <link rel="stylesheet" href="{{ asset('css/prestation/reservation_prestation.css') }}">
    <script src=" https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js " media="print"></script>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css' rel='stylesheet'>
@endpush
@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="container_layout">
        <div class="title text-center">
            <h1>Reservation</h1>
        </div>
        <div class="action">
            <button onclick="window.location.href='/prestations/{{$type}}'" class="btn btn-info">Retour</button>
            @if($admin)
                <button onclick="window.location.href='/prestations/{{$type}}/{{$id}}/delete'" class="btn btn-danger">Supprimer</button>
            @endif
        </div>
        <div class="calendar">
            <div id='calendar'></div>
        </div>
        <form action="/prestations/{{$type}}/{{$id}}/reservation" method="POST">
            @csrf
            <input type="hidden" name="start_date" id="start_date">
            <button type="submit">Reserver</button>

        </form>
    </div>

    <script>
        const businessHours = {
            daysOfWeek: [1, 2, 3, 4, 5, 6],
            startTime: '08:00',
            endTime: '20:00',
        };

        var date;

        function isWithinBusinessHours(start, end) {
            const businessStartTime = new Date(start);
            const businessEndTime = new Date(end);

            businessStartTime.setHours(parseInt(businessHours.startTime.split(':')[0]));
            businessStartTime.setMinutes(parseInt(businessHours.startTime.split(':')[1]));
            businessEndTime.setHours(parseInt(businessHours.endTime.split(':')[0]));
            businessEndTime.setMinutes(parseInt(businessHours.endTime.split(':')[1]));

            return start >= businessStartTime && end <= businessEndTime;
        }

        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');


            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                initialDate: new Date().currentDate,
                height: '85%',
                locale: 'fr',
                timeZone: 'UTC',
                aspectRatio: 1,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'timeGridWeek'
                },
                eventTimeFormat: {
                    hour: 'numeric',
                    minute: '2-digit',
                    omitZeroMinute: false,
                    meridiem: 'short'
                },
                selectable: true,
                businessHours: businessHours,
                select: function(info) {
                    if (!isWithinBusinessHours(info.start, info.end)) {
                        //calendar.unselect();
                        alert('Selectionner une date entre 8h et 20h, le prestataire peut ne pas être disponible');
                    }else{
                        date = info.startStr;
                        document.getElementById('start_date').value = date;
                        alert('Date selectionnée: ' + date)
                    }
                },
                events: [
                    @foreach($disponibility as $reservation)

                    {
                        title: 'Réservé',
                        start: '{{$reservation->start_date}}',
                        end: '{{$reservation->end_date}}',
                        color: 'coral'
                    },
                    @endforeach
                ]
            });

            calendar.render();
        });
    </script>

@endsection
