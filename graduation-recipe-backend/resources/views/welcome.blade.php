<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Recipe Backend</title>
</head>

<body>
    <h1> This is for only backend laravel</h1>
    <h2> Api docs</h2>
    <p>
        //For get ingredients and recipes
        GET http://127.0.0.1:8000/api/recipes </p>

    <p> //For get single recipe by id
        GET http://127.0.0.1:8000//api/recipes/{id} </p>

    <p> //For get all ingredients
        GET http://127.0.0.1:8000//api/ingredients </p>

    <p> //For post search recipe
        POST http://127.0.0.1:8000//api/recipes/search </p>

    <p> //For Get search recipe
        http://127.0.0.1:8000/api/recipes/search?ingredients=Salt,Tomato </p>

    <p>
        //For get pantry items
        GET http://127.0.0.1:8000/api/user/pantry
        like:( http://127.0.0.1:8000/api/recipes/search?ingredients=Salt,Tomato )
    </p>
    <p>
        //For post pantry items
        POST http://127.0.0.1:8000/api/user/pantry
    </p>
    <p>
        //For pantry items delete
        DELETE http://
        GET http://127.0.0.1:8000/api/user/pantry/{id}
    </p>
    </body>

</html>
