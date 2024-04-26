<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Login page</title>
    <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        html{
            width: 100vw;
            overflow: scroll;
        }
        /*html, body{min-height:100%;}*/
        body{
            width: 100%;
            height: 100vh;
            min-height: 100vh;
        }
        main{
            width: 100vw;
            height: 100%;
        }
    </style>
</head>
<body>
    <x-header :connected="true" :light="false" :profile="true" />
    <main>

        <h1>Lorem Ipsum</h1>
        <p>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed ut purus eget nunc ultrices
            fermentum. Nullam nec nisl nec nunc ultrices fermentum. Nullam nec nisl nec nunc ultrices
            fermentum. Nullam nec nisl nec nunc ultrices fermentum. Nullam nec nisl nec nunc ultrices
            fermentum. Nullam nec nisl nec nunc ultrices fermentum. Nullam nec nisl nec nunc ultrices
            fermentum. Nullam nec nisl nec nunc ultrices fermentum. Nullam nec nisl nec nunc ultrices
            fermentum. Nullam nec nisl nec nunc ultrices fermentum. Nullam nec nisl nec nunc ultrices
        </p>
    </main>
    <x-footer/>
</body>
</html>
