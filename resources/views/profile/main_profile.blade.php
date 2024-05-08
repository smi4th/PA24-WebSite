<!DOCTYPE html>
<html>

<head>
   <title>Profil</title>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="{{ asset('css/profile/main_profile.css') }}">
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
   <link href="https://fonts.googleapis.com/css?family=Roboto|Inter|Karla|Manrope&display=swap" rel="stylesheet">
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
   <link rel="icon" href="{{ asset('logo.png') }}" />
</head>

<body>
   <x-header :connected="true" :profile="true" :light="false" />
   <?php
   //Traitement des données users:
   $username = $data['data'][0]['username'];
   $firstname = $data['data'][0]['first_name'];
   $lastname = $data['data'][0]['last_name'];

   $email = $data['data'][0]['email'];
   $parts = explode('@', $email);
   $emailName = $parts[0];
   $domain = $parts[1];

   $formattedEmail = $emailName[0] . str_repeat('*', strlen($emailName) - 1) . '@' . $domain;


   $vide = ['NULL', '', ' '];
   $imgPathValue = $data['data'][0]['imgPath'];
   if (in_array($imgPathValue, $vide)) {
      $imgPath = 'default_user.png';
   } else {
      $imgPath = $imgPathValue;
   }
   ?>


   <main>
      <div class="navbar-layout">
         <ul>
            <li><a href="#1" onclick="show_page('1')">Paramètres</a></li>
            <li><a href="#2" onclick="show_page('2')">Newsletter</a></li>
         </ul>
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
      @if(session('success'))
      <div class="alert alert-success">
         <h2>{{session('success')}}</h2>
      </div>
      @endif
      <div class="page" id="page1">
         <h1>Paramètres</h1>
         <div class="first-section">
            <h5>Photo de profil</h5>
            <div class="picture">
               <img src="{{ asset('/assets/images/' . $imgPath) }}" alt="profile">
            </div>
         </div>
         <div class="seperate"></div>

         <div class="second-section">
            <form>
               @csrf
               <div class="inputbox">
                  <label>Nom complet</label>
                  <input type="text" name="name" value="{{ $firstname . ' ' . $lastname }}" required readonly>
                  <!-- <img src="pen.svg" alt="Edit" class="edit-icon"> -->
               </div>

               <div class="inputbox">
                  <label>Pseudo</label>
                  <input type="text" name="username" value="{{ $username }}" required readonly>
                  <!-- <img src="pen.svg" alt="Edit" class="edit-icon"> -->
               </div>

               <div class="inputbox">
                  <label>Email</label>
                  <input type="email" name="email" value="{{ $formattedEmail }}" required readonly>
                  <!-- <img src="pen.svg" alt="Edit" class="edit-icon"> -->
               </div>

               <div class="inputbox">
                  <label>Mot de passe</label>
                  <input type="password" name="password" value="*************" required readonly>
                  <!-- <img src="pen.svg" alt="Edit" class="edit-icon"> -->
               </div>
            </form>

            <form action="{{ route('auth.logout') }}">
               @csrf
               <input type="submit" value="Déconnexion">
            </form>
         </div>
      </div>
      <div class="page" id="page2">
         <h1>Newsletter</h1>
         <div class="seperate"></div>

         <div class="second-section">
            <div class="inputbox">
               <label>Abonnement à la Newsletter</label>
               <br>
               <input type="text" name="news" value="Vous êtes inscrit" required readonly>
               <!-- <img src="pen.svg" alt="Edit" class="edit-icon"> -->
            </div>
            <!-- bouton rouge  pas cliquable-->
            <button type="submit" class="btn btn-primary" disabled>Me désinscrire</button>
         </div>
      </div>
   </main>


   <script>
      function show_page(page) {
         var pages = document.getElementsByClassName('page');
         for (var i = 0; i < pages.length; i++) {
            pages[i].style.display = 'none';
         }
         document.getElementById('page' + page).style.display = 'block';
      }
      $(document).ready(function() {
         $('.inputbox input').click(function() {
            var inputName = $(this).attr('name');
            if (inputname != 'news') {
               window.location.href = '{{ url("/profile/edit-profile") }}/' + inputName;
            }
         });
      });
   </script>

   <x-footer />
</body>

</html>