@extends('layouts.app')
@section('content')
<h1>Chat with {{ $user->first_name }} {{ $user->last_name }}</h1>
<div id="chat-box"></div>
<hr>
<div class="mx-auto">
    <label for="message-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Enter Message</label>
<textarea id="message-input" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Write your thoughts here..."></textarea>

<button id="send-message" class=" bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Send</button>
</div>


<script>
    $(document).ready(function() {
        var receiverId = {{ $user->id }};

        $('#send-message').on('click', function() {
            var message = $('#message-input').val();
            $.ajax({
                url: '{{ route('message.send') }}',
                type: 'POST',
                data: {
                    receiver_id: receiverId,
                    message: message,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#message-input').val('');
                    loadMessages();
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseJSON.message);
                }
            });
        });

        function loadMessages() {
            $.ajax({
                url: '{{ route('messages.get', ['receiver_id' => $user->id]) }}',
                type: 'GET',
                success: function(messages) {
                    var chatBox = $('#chat-box');
                    chatBox.empty();
                    console.log(messages);
                    messages.forEach(function(message) {
                        var messageHtml = '<div><strong>' + message.sender.full_name + ':</strong> ' + message.message + '</div>';
                        chatBox.append(messageHtml);
                    });
                },
                error: function(xhr, status, error) {
                    alert('Failed to load messages');
                }
            });
        }

        loadMessages();

        // Polling to refresh messages every 5 seconds
        setInterval(loadMessages, 5000);
    });
</script>
@endsection
