@push('message_profile')
    <link rel="stylesheet" href="{{ asset('css/message/main_message.css') }}">
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

@endpush

@section('content')
    @if($errors->any())
        <div class="alert alert-danger" role="alert">
            @foreach($errors->all() as $error)
                {{$error}}
            @endforeach
        </div>
    @endif

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4 mt-3">
                <div class="list-group">
                    @if(count($allTickets) == 0)
                        <p class="text-center">Vous n'avez pas de ticket en attente</p>
                    @endif
                    @foreach($allTickets as $ticket)
                        <a href="#" class="list-group-item list-group-item-action" data-uuid="{{$ticket['uuid']}}">
                            {{ $ticket["title"] }} - Statuts : {{ $ticket["status"] }}
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="col-md-8">
                <div class="card" style="height: 100%;">
                    <div class="card-body" style="display: flex; flex-direction: column; justify-content: space-between; height: 100%;">
                        <div id="messages" style="overflow-y: auto; flex-grow: 1;">
                        </div>
                        <div class="send-message-container">
                            <textarea id="messageInput" class="form-control" placeholder="Écrire un message..."></textarea>
                            <button onclick="sendMessage()" class="btn btn-outline-info mt-2">Envoyer</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>


        const myUUID = "{{ $data }}";

        let allMessages = @json($allMessages);
        let currentDiscussion = null;
        let listTickets = @json($allTickets);

        //console.log(allMessages, listTickets,myUUID);

        if(listTickets.length > 0){
            currentDiscussion = listTickets[0].uuid;
            displayCurrentConversation(currentDiscussion);
        }


        function displayCurrentConversation(receiver){
            //console.log(receiver);
            console.log(allMessages);


            let messages = allMessages.filter(message => (message[0].ticket == receiver));
            let messagesDiv = document.getElementById('messages');
            messagesDiv.innerHTML = '';

            console.log("messages ")
            console.log(messages);

            messages[0].sort((a, b) => new Date(a.creation_date) - new Date(b.creation_date));
            messages[0].forEach(message => {
                let messageDiv = document.createElement('div');
                messageDiv.classList.add('message');
                messageDiv.classList.add(message.account == myUUID ? 'right' : 'left');
                messageDiv.innerHTML = `
                           <span class="message-content">${message.content}</span>
                           <span class="message-date">${new Date(message.creation_date).toLocaleString()}</span>
                       `;
                messagesDiv.appendChild(messageDiv);
            });
            //console.log(messagesDiv.scrollHeight);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }
        let listTicketsHtml = document.querySelectorAll('.list-group-item');
        listTicketsHtml.forEach(userHtml => {
            //console.log(userHtml);
            userHtml.addEventListener('click', function(){
                currentDiscussion = userHtml.getAttribute('data-uuid');
                displayCurrentConversation(currentDiscussion);
            });
        });


        function sendMessage() {
            let message = document.getElementById('messageInput').value;
            if (message.length > 0) {
                axios.post('{{route('message_ticket')}}', {
                    account: myUUID,
                    ticket: currentDiscussion,
                    message: message,

                }, {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                }).then(response => {
                    console.log(response);
                    const data = response.data;
                    console.log('data :', data);
                    if (data && data.ok) {
                        displayCurrentConversation(currentDiscussion);
                        document.getElementById('messageInput').value = '';
                    }
                }).catch(error => {
                    console.error(JSON.stringify(error.response.data));
                    alert("Error: " + error.response.data.message);
                });

                upDateAllMessages();
            }
        }

        function upDateAllMessages(){
            fetch('{{route('update_ticket')}}',
            {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
            }).then(response => {
                return response.json();
            }).then(data => {
                console.log(data);
                allMessages = data[1];
                listTickets = data[0];
            });
        }
    </script>
@endsection
