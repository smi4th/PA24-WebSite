<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planning</title>
    <link rel="stylesheet" href="{{ asset('css/profile/main_profile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profile/calendar.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.11/locales/fr.min.js"></script>

    <link rel="icon" href="{{ asset('assets/images/logo/white-line-orange-bg-blue.png') }}" />
</head>

<body>
    <x-header :connected="true" :profile="true" :light="false" />
    <?php
    //dd($reservations);
    $listeReservations = [];
    foreach ($reservations['baskets'] as $index => $reservation) {
        foreach ($reservation['HOUSING'] as $i => $house) {
            $listeReservations['House'][$i] = $house;
        }
        foreach ($reservation['BEDROOMS'] as $j => $bedrooms) {
            $listeReservations['Bedrooms'][$j] = $bedrooms;
        }
        foreach ($reservation['SERVICES'] as $k => $services) {
            $listeReservations['Services'][$k] = $services;
        }
        foreach ($reservation['EQUIPMENTS'] as $l => $equipments) {
            $listeReservations['Equipments'][$l] = $equipments;
        }
    }
    //dd($listeReservations);
    ?>

    <div id='calendar' class="m-3"></div>

    <x-footer />

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var reservationsData = @json($listeReservations);
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                initialView: 'dayGridMonth',
                themeSystem: 'bootstrap',
                height: 'auto',
                locale: 'fr',
                events: formatReservations(reservationsData)
            });
            calendar.render();
        });

        function formatReservations(data) {
            var events = [];
            var colorHouse = '#FFD700',
                colorBedroom = '#FF8C00',
                colorService = '#1E90FF',
                colorEquipment = '#FF4500';
            if (data.House) {
                data.House.forEach(function(item) {
                    events.push({
                        title: 'House: ' + item.description,
                        start: item.startTime,
                        end: item.endTime,
                        color: colorHouse 
                    });
                });
            }
            if (data.Bedrooms) {
                data.Bedrooms.forEach(function(item) {
                    events.push({
                        title: 'Bedroom: ' + item.description,
                        start: item.startTime,
                        end: item.endTime,
                        color: colorBedroom
                    });
                });
            }
            if (data.Services) {
                data.Services.forEach(function(item) {
                    events.push({
                        title: 'Service: ' + item.description,
                        start: item.startTime,
                        end: item.endTime,
                        color: colorService
                    });
                });
            }
            if (data.Equipments) {
                data.Equipments.forEach(function(item) {
                    events.push({
                        title: 'Equipment: ' + item.description,
                        start: item.startTime, 
                        end: item.endTime, 
                        color: colorEquipment
                    });
                });
            }
            return events;
        }
    </script>

</body>

</html>