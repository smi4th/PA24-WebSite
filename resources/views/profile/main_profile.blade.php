@section('content')
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <h1>Profile accueil</h1>
   @error($errors->any())
   <div class="alert alert-danger">
       <ul>
           @foreach ($errors->all() as $error)
               <li> {{ $error }}</li>
           @endforeach
       </ul>
   </div>
   @enderror
   @if (session('success'))
       <div class="alert alert-success">
           {{ session('success') }}
       </div>
   @endif
@endsection
