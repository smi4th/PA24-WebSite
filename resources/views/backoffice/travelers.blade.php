@section("content")
    @if(empty($accounts))
        <h1>No travelers found</h1>
    @else
    <h1>Travelers</h1>
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @error('error')
        <div class="alert alert-danger">
            {{ $message }}
        </div>
    @enderror
    <table class="table table-striped">
        <thead>
        <tr>
            @foreach ($accounts[0] as $key => $value)
                <th class="key" >{{ $key }}</th>
            @endforeach
            <th>Actions</th>
        </tr>
        </thead>
        <tbody id="tbody">
        @foreach ($accounts as $user)

            <tr class="{{$user->uuid}}">
                @foreach ($user as $key => $value)
                    @if ($key == 'uuid' || $key == 'account_type')
                        <td>{{ $value }}</td>
                    @elseif ($key == 'imgPath' && $value != null && $value != "")
                        <td><img src="{{ asset("/assets/images/pfp/".$value) }}" alt="profile picture" style="width: 50px; height: 50px;"></td>
                    @else
                        <td contenteditable="true">{{ $value }}</td>
                    @endif
                @endforeach
                <td>
                    <button class="btn btn-primary btn-sm" onclick="editUser('{{ $user->uuid }}')">Edit</button>
                    <button class="btn btn-danger btn-sm" onclick="deleteUser('{{ $user->uuid }}')">Delete</button>
                    @if ($user->account_type != "Administateur")
                        <button class="btn btn-success btn-sm" onclick="promoteUser('{{ $user->uuid }}')">Promote</button>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <ul class="pagination justify-content-center">
        @for ($i = 1; $i <= $numberPages; $i++)
            <li class="page-item"><a class="page-link" href="/backoffice/users?page={{ $i }}">{{ $i }}</a></li>
        @endfor
    </ul>

    <script>

        var user = {};
        let th = document.getElementsByTagName("th");
        let tbody = document.getElementById("tbody");
        for (let i = 0; i < tbody.children.length; i++) {
            let tr = tbody.children[i];
            for (let j = 1; j < tr.children.length-1; j++) {
                tr.children[j].addEventListener("input", function(event) {

                    let key = th[j].innerText;
                    let value = tr.children[j].innerText;
                    user[key] = value;
                    console.log(key, value);
                });
            }
        }

        function editUser(id) {

            let data = JSON.stringify(user);

            console.log(data);
            window.location.href = '/backoffice/users/' + id + '/edit/' + data;
        }

        function deleteUser(id) {
            window.location.href = '/backoffice/users/' + id + '/delete';
        }

        function promoteUser(id) {
            //window.location.href = '/backoffice/users/' + id + '/promote';
        }
    </script>
    @endif
@endsection
