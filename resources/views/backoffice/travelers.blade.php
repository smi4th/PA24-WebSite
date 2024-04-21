@section("content")
    <table class="table table-striped">
        <thead>
        <tr>
            @foreach ($users[0] as $key => $value)
                <th>{{ $key }}</th>
            @endforeach
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($users as $user)
            <tr>
                @foreach ($user as $key => $value)
                    <td>{{ $value }}</td>
                @endforeach
                <td>
                    <button class="btn btn-primary btn-sm" onclick="editUser('{{ $user['id'] }}')">Edit</button>
                    <button class="btn btn-danger btn-sm" onclick="deleteUser('{{ $user['id'] }}')">Delete</button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <script>
        function editUser(id) {
            window.location.href = '/backoffice/travelers/' + id + '/edit';
        }

        function deleteUser(id) {
            window.location.href = '/backoffice/travelers/' + id + '/delete';
        }
@endsection
