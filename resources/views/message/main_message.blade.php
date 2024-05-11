<!DOCTYPE html>
<html>

<head>
    <title>Message</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/profile/main_profile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/message/main_message.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css?family=Roboto|Inter|Karla|Manrope&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script src="{{ mix('js/app.js') }}"></script>
    <link rel="icon" href="{{ asset('logo.png') }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
@php
//dd($users, $data, $messages);
@endphp

<body>
    <x-header :connected="true" :profile="true" :light="false" />
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <select id="userSelect" class="form-select mb-3">
                    @foreach($users->data as $user)
                    @if ($user->uuid == $data->data[0]->uuid)
                    @continue
                    @endif
                    <option value="{{ $user->uuid }}">{{ $user->username }}</option>
                    @endforeach
                </select>
                </select>
                <div class="messages" id="messageBox">
                    <div class="container">
                        <div class="row ">
                            <div class="col-12 col-md-12 tulaspaslui">
                                @foreach ($messages->data as $message)
                                <div class="message {{ $message->account == $data->data[0]->uuid ? 'left' : 'right' }}" data-author="{{ $message->author }}" data-recipient="{{ $message->account }}" data-date="{{ $message->creation_date }}">
                                    <span class="message-content">{{ $message->content }}</span>
                                    <span class="message-date">{{ \Carbon\Carbon::parse($message->creation_date)->format('M d, Y h:i A') }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <textarea id="messageInput" class="form-control mb-3" placeholder="Write a message..."></textarea>
                <button onclick="sendMessage()" class="btn btn-primary">Send</button>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('userSelect').addEventListener('change', function() {
            updateMessageVisibility();
        });

        document.addEventListener('DOMContentLoaded', function() {
            const messageBox = document.querySelector('.tulaspaslui');
            messageBox.scrollTop = messageBox.scrollHeight;
            //prend le dernier message au chargement
            const messages = document.querySelectorAll('.message');
            if (messages.length > 0) {
                const lastMessage = messages[messages.length - 1];
                lastMessage.scrollIntoView({
                    behavior: 'smooth',
                    block: 'end'
                });
            }

            document.getElementById('userSelect').addEventListener('change', function() {
                updateMessageVisibility();
            });
        });

        function addMessageToDOM(message) {
            const messageBox = document.querySelector('.tulaspaslui');
            const isRight = message.author_uuid === "{{ $data->data[0]->uuid }}";
            const messageClass = isRight ? 'right' : 'left';

            // Créer le conteneur du message
            const newMessageDiv = document.createElement('div');
            newMessageDiv.className = `message ${messageClass}`;
            newMessageDiv.dataset.author = message.author_uuid;
            newMessageDiv.dataset.recipient = message.recipient_account;

            // Créer et ajouter le contenu du message
            const messageContentSpan = document.createElement('span');
            messageContentSpan.className = 'message-content';
            messageContentSpan.textContent = message.message_content;
            newMessageDiv.appendChild(messageContentSpan);

            // Créer et ajouter la date du message
            const messageDateSpan = document.createElement('span');
            messageDateSpan.className = 'message-date';
            const date = new Date();
            const formattedDate = formatDate(date);
            messageDateSpan.textContent = formattedDate;
            newMessageDiv.appendChild(messageDateSpan);


            messageBox.appendChild(newMessageDiv);
            newMessageDiv.scrollIntoView({
                behavior: 'smooth',
                block: 'end'
            });
            updateMessageVisibility();
        }

        function formatDate(date) {
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            }).replace(',', '');
        }


        function sendMessage() {
            const recipient = document.getElementById('userSelect').value;
            const messageContent = document.getElementById('messageInput').value;

            const message = {
                author_uuid: "{{ $data->data[0]->uuid }}",
                recipient_account: recipient,
                message_content: messageContent
            };

            addMessageToDOM(message);


            axios.post('/message/send-message', {
                recipient: recipient,
                message: messageContent
            }).then(response => {
                console.log('Response:', response.data);
                document.getElementById('messageInput').value = '';
            }).catch(error => {
                if (error.response) {
                    console.error('Error sending message:', error.response.data);
                    alert('Error: ' + JSON.stringify(error.response.data.errors));
                } else if (error.request) {
                    console.error('Error sending message:', error.request);
                } else {
                    console.error('Error 2:', error.message);
                }
            });
        }

        function updateMessageVisibility() {
            const selectedUUID = document.getElementById('userSelect').value;
            const messages = document.querySelectorAll('.message');

            messages.forEach(msg => {
                const isAuthor = msg.dataset.author === selectedUUID;
                const isRecipient = msg.dataset.recipient === selectedUUID;
                msg.style.display = isAuthor || isRecipient ? '' : 'none';
            });
        }

        updateMessageVisibility();
    </script>
</body>

</html>