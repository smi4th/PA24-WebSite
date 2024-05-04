<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Login page</title>
    <link rel="stylesheet" href="{{ asset('css/default.css') }}">
</head>
@include($file_path ?? 'error')

@if($stack_css !== null)
    @stack($stack_css)
@endif

<body>
    <x-header :$connected :$profile :$light/>
    <main>
        @yield('content')
    </main>
    <x-footer/>
</body>
</html>
