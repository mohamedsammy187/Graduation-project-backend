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
    <p>
        //simple chat route
        GET http://127.0.0.1:8000/api/chat


    </p>
    <p>
        //chat for model (Will be upgraded soon)
        GET http://127.0.0.1:8000/api/ask

    </p>
    <p>
        //favorite routes
        GET http://127.0.0.1:8000/api/favorites
    </p>
    <p>
    <p>
        //favorite routes
        POST http://127..0.0.1:8000/api/favorites
    </p>
    <p>
        //favorite routes
        DELETE http://127..0.0.1:8000/api/favorites/{recipe_id}
    </p>
    <p>
        //register routes
        POST http://127..0.0.1:8000/api/register
    </p>
    <p>
        //Logiin routes
        POST http://127..0.0.1:8000/api/login
    </p>
    <p>
        //logout routes
        POST http://127..0.0.1:8000/api/logout
    </p>
    <p>
        //http://127.0.0.1:8000/api/ask then it will create you a link.
        //first ask with { "prompt": "I want something with Grilled Chicken" } ->body in postman.
        //searching by slug "more ingredients".
        GET http://127..0.0.1:8000/api//recipes/slug/{slug}
    </p>
    <p>
        //seach types:
        POST http://127.0.0.1:8000/api/recipes/search?ingredients=tomato,cheese
        POST http://127.0.0.1:8000/api/recipes/search?ingredients=tomato,cheese
        POST http://127.0.0.1:8000/api/recipes/search?ingredients=tomato,cheese&temperature=hot
        POST http://127.0.0.1:8000/api/recipes/search?ingredients=tomato,cheese&temperature=hot&meal_type=lunch
        POSThttp://127.0.0.1:8000/api/recipes/search?q=Pancakes

    </p>
    </p>
</body>

</html>
