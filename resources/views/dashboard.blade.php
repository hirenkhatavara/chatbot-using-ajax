@extends('layouts.app')
@section('content')
<h1>User Listing</h1>
<table id="users-table" class="display">
    <thead>
        <tr>
            <th>ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Phone Number</th>
            <th>Profile Picture</th>
            <th>Registered At</th>
            <th>Action</th>
        </tr>
    </thead>
</table>
<script>
    $(document).ready(function() {
        var table = $('#users-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('users.data') }}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'first_name', name: 'first_name' },
                { data: 'last_name', name: 'last_name' },
                { data: 'email', name: 'email' },
                { data: 'phone_number', name: 'phone_number' },
                { data: 'profile_picture', name: 'profile_picture', orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }

            ]
        });

        setInterval(function() {
            table.ajax.reload(null, false);
        }, 10000); // 10 seconds

        $(document).on('click', '.send-request', function() {
            var id = $(this).data('id');
            sendFriendRequest(id);
        });

        function sendFriendRequest(id) {
            $.ajax({
                url: '{{ route('friend-request.send') }}',
                type: 'POST',
                data: {
                    receiver_id: id,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    alert(response.message);
                    $('#users-table').DataTable().ajax.reload();
                }
            });
        }

        $(document).on('click', '.cancel-request', function() {
            var id = $(this).data('id');
            cancelFriendRequest(id);
        });


        function cancelFriendRequest(id) {
            $.ajax({
                url: '{{ route('friend-request.cancel') }}',
                type: 'POST',
                data: {
                    receiver_id: id,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    alert(response.message);
                    $('#users-table').DataTable().ajax.reload();
                }
            });
        }

        $(document).on('click', '.accept-request', function() {
            var id = $(this).data('id');
            acceptFriendRequest(id);
        });

        function acceptFriendRequest(id) {
            $.ajax({
                url: '{{ route('friend-request.accept') }}',
                type: 'POST',
                data: {
                    sender_id: id,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    alert(response.message);
                    $('#users-table').DataTable().ajax.reload();
                }
            });
        }

        $(document).on('click', '.reject-request', function() {
            var id = $(this).data('id');
            rejectFriendRequest(id);
        });

        function rejectFriendRequest(id) {
            $.ajax({
                url: '{{ route('friend-request.reject') }}',
                type: 'POST',
                data: {
                    sender_id: id,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    alert(response.message);
                    $('#users-table').DataTable().ajax.reload();
                }
            });
        }

    });
</script>
@endsection
