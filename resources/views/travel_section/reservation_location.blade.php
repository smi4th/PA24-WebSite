@push('reservation_location')
    <link rel="stylesheet" href="{{ asset('css/travel/reservation_location.css') }}">
    <script src=" https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js " media="print"></script>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css' rel='stylesheet'>
@endpush
@section('content')

    <div class="container_all_bed">
        <div class="container_bed">
            @foreach($bedRooms as $bedRoom)
                <div class="bedroom">
                    <div class="title">
                        <h3>Chambre de {{$bedRoom->nbPlaces}} </h3>
                    </div>
                    <div class="layout">
                        <div class="bedroom_content">
                            <div class="bedroom_image">
                                @if(strtolower($bedRoom->imgPath) === "null")
                                    <img src="{{ asset('/assets/images/default_bed_room.jpg') }}" alt="image">
                                @else
                                    <img src="{{ Storage::disk('wasabi')->url('locations/'.$bedRoom->housing.'/'.$bedRoom->imgPath) }}" alt="image">
                                @endif
                            </div>
                            <div class="bedroom_description">
                                <div class="title">{{$bedRoom->description}}</div>
                                <div class="price">
                                    <h3>{{$bedRoom->price}}€/nuit</h3>
                                </div>
                                <div class="bedroom_equipment">
                                    <div class="title">
                                        <h3>Equipements</h3>
                                    </div>
                                    <div class="layout_equipment">
                                        @foreach($equipments as $equipment)
                                            <div class="equipment">
                                                <div class="name">
                                                    <h3>{{$equipment->name}}</h3>
                                                </div>
                                                @if($equipment->price > 0)
                                                    <div class="price">
                                                        <h3>{{$equipment->price}}€</h3>
                                                    </div>
                                                    <input type="checkbox" data-bedroom='{{$bedRoom->uuid}}' name="equipment{{$equipment->uuid}}" id="equipment{{$equipment->uuid}}">
                                                @else
                                                    <div class="price">
                                                        <h3>Gratuit</h3>
                                                    </div>
                                                    <input type="checkbox" data-bedroom='{{$bedRoom->uuid}}' disabled checked name="equipment{{$equipment->uuid}}" id="equipment{{$equipment->uuid}}">
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="disponibility">
                        <label for="bedroom{{$bedRoom->uuid}}">Réserver</label>
                        <input type="checkbox" id="bedroom{{$bedRoom->uuid}}" name="bedroom{{$bedRoom->uuid}}" onclick="var a = document.getElementById('bedroom{{$bedRoom->uuid}}');var b=document.getElementById('{{$bedRoom->uuid}}') ; a.checked ? b.style.visibility = 'visible' : b.style.visibility = 'hidden';">
                        <div class="bedroom_calendar" style="visibility: hidden" id="{{$bedRoom->uuid}}"></div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="all">
            <label for="all_bedrooms">Toutes les chambres</label>
            <input type="checkbox" name="all_bedrooms" id="all_bedrooms">
            <label for="all_equipments">Tous les équipements</label>
            <input type="checkbox" name="all_equipements" id="all_equipements">
        </div>

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
        <form action="/travel/reservation/{{$housing}}" method="post">
            @csrf
            @method('POST')
            <input type="hidden" id="dates_form" name="dates_form" value="">
            <input type="hidden" id="equipment_form" name="equipment_form" value="">
            <button type="submit" onclick="addToBasket()">Ajouter au panier</button>
        </form>
        <div class="postscriptum">
            <h3>Post-scriptum</h3>
            <p>Réserver l'ensemble des chambres revient à réserver l'ensemble du bien,<br>vous pouvez toujours réserver les chambres en décalé</p>
        </div>

    </div>

    <script>

        var all_equipments = document.getElementById('all_equipements');
        var all_bedrooms = document.getElementById('all_bedrooms');

        var dates = [];
        var equipment = [];

        //coche toutes les cases pour les chambres (purement visuel)
        all_bedrooms.addEventListener('change', function() {
            var checkboxes = document.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(function(checkbox) {
                if(checkbox.id.includes('bedroom')){
                    checkbox.checked = all_bedrooms.checked;
                    if(checkbox.checked){
                        //display calendar
                        var calendarEls = document.querySelectorAll('.bedroom_calendar');
                        calendarEls.forEach(function(calendarEl) {
                            if (calendarEl.style.visibility === 'hidden')
                                calendarEl.style.visibility = 'visible';
                        });
                    }
                }
            });
            document.getElementById('dates_form').value = JSON.stringify(dates);
        });

        //on ajoute les équipements à la liste des équipements qui sont en disabled
        let getAllCheckBox = document.querySelectorAll('input[type="checkbox"]');
        getAllCheckBox.forEach(function(checkbox) {
            if(checkbox.disabled){
                equipment.push({id_equipment:checkbox.id,id_bedroom : checkbox.dataset.bedroom});
            }
        });
        document.getElementById('equipment_form').value = JSON.stringify(equipment);


        //si le all_equipments est coché alors on coche toutes les cases pour les équipements sauf ceux qui sont disabled
        all_equipments.addEventListener('change', function() {
            var checkboxes = document.querySelectorAll('input[type="checkbox"]');

            if(!all_equipments.checked){
                for (let i = 0; i < equipment.length; i++) {
                    for (let j = 0; j < checkboxes.length; j++) {
                        if(equipment[i].id_equipment === checkboxes[j].id && !checkboxes[j].disabled){
                            equipment.pop(i);
                            checkboxes[j].checked = false;
                        }
                    }
                }
                return
            }
            checkboxes.forEach(function(checkbox) {

                if(checkbox.id.includes('equipment') && !checkbox.disabled && checkbox.dataset.bedroom !== undefined){
                    console.log(checkbox,"\n");

                    checkbox.checked = all_equipments.checked;

                    let find = false;
                    if(equipment.length === 0){
                        equipment.push({id_equipment:checkbox.id,id_bedroom : checkbox.dataset.bedroom});
                    }else{
                        for (let i = 0; i < equipment.length; i++) {
                            if(equipment[i].id_equipment === checkbox.id && equipment[i].id_bedroom === checkbox.dataset.bedroom){
                                equipment[i] = {id_equipment:checkbox.id,id_bedroom : checkbox.dataset.bedroom};
                                find = true;
                            }
                        }
                        if(!find && checkbox.dataset.bedroom !== undefined) {
                            equipment.push({id_equipment: checkbox.id, id_bedroom: checkbox.dataset.bedroom});
                        }
                    }

                    console.log(equipment,checkbox,find);
                }
            });
            document.getElementById('equipment_form').value = JSON.stringify(equipment);
        });

        //si un equipement est coché alors on l'ajoute à la liste des équipements
        getAllCheckBox.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                if(checkbox.id.includes('equipment') && !checkbox.disabled){
                    if(checkbox.checked){
                        equipment.push({id_equipment:checkbox.id,id_bedroom : checkbox.dataset.bedroom});
                    }else{
                        for (let i = 0; i < equipment.length; i++) {
                            if(equipment[i].id_equipment === checkbox.id){
                                equipment.pop(i);
                            }
                        }
                    }
                }
                document.getElementById('equipment_form').value = JSON.stringify(equipment);
            });
        });

        function addToBasket(){
            //object dates object equipment , id housing
            console.log('dates :',dates);
            console.log('equipment :',equipment);

            document.getElementById('dates_form').value = JSON.stringify(dates);
            document.getElementById('equipment_form').value = JSON.stringify(equipment);
        }


        document.addEventListener('DOMContentLoaded', function() {
            var calendarEls = document.querySelectorAll('.bedroom_calendar');

            calendarEls.forEach(function(calendarEl) {
                let id = calendarEl.id;
                console.log(id);

                @foreach($bedRooms as $bedRoom)

                    if(id === '{{$bedRoom->uuid}}') {
                        var calendar = new FullCalendar.Calendar(calendarEl, {
                            initialView: 'dayGridMonth',
                            timeZone: 'UTC',
                            headerToolbar: {
                                left: 'prev,next today',
                                center: 'title',
                                right: 'dayGridMonth'
                            },
                            titleFormat: { year: 'numeric',  month: 'long', day: 'numeric' },
                            selectable: true,
                            themeSystem: 'bootstrap5',
                            select: function(info) {
                                var endDate = new Date(info.end.getTime());
                                endDate.setDate(endDate.getDate() - 1);
                                //alert('Date sélectionnée: ' + info.start.toLocaleDateString() + ' à ' + endDate.toLocaleDateString());

                                //si il y a déjà une sélection on la remplace selon le calendrier séléctionné
                                let find = false;
                                for (let i = 0; i < dates.length; i++) {
                                    if(dates[i].id === '{{$bedRoom->uuid}}'){
                                        dates[i].start = info.start.toLocaleDateString();
                                        dates[i].end = endDate.toLocaleDateString();
                                        find = true;
                                    }
                                }
                                if(!find){
                                    dates.push({id: '{{$bedRoom->uuid}}', start: info.start.toLocaleDateString(), end: endDate.toLocaleDateString()});
                                }
                                console.log(dates,dates.length,'{{$bedRoom->uuid}}');
                                var datesForm = document.getElementById('dates_form');
                                datesForm.value = JSON.stringify(dates);
                            },

                            events: [
                                    @if ($bedRoom->reservations !== null)

                                        @foreach($bedRoom->reservations as $reservation)

                                            {
                                                title: 'Réservé',
                                                start: '{{$reservation->start_time}}',
                                                end: '{{date('Y-m-d', strtotime($reservation->end_time . ' +1 day')) }}',
                                                color: 'lightgrey',
                                                display: 'background',
                                                fontFamily: 'Arial',
                                            },

                                      @endforeach
                                @endif

                            ]
                        });
                        dates.forEach(function(date) {
                            if (date.id === '{{$bedRoom->uuid}}') {
                                calendar.addEvent({
                                    title: 'Réservation en cours',
                                    start: date.start,
                                    end: date.end,
                                    color: 'lightblue',
                                    display: 'background',
                                    fontFamily: 'Arial'
                                });
                            }
                            calendar.refetchEvents()

                        });

                        calendar.render();
                    }
                @endforeach
            });
        });
    </script>

@endsection
