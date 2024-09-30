# Blog Wbrothers API

This app is a backend API generated with Laravel, for a blog. The DB is generated in MYSQL, in the file database.sql are the sql commands to generate the BD, but may be needed to adapt the file.env and modify the line to your own configurations:

*env:*

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=api_rest_laravel
    DB_USERNAME=root
    DB_PASSWORD=null

and there is a file named api_rest_laravel.sql with the commands required for restore it. However it can be created from the app itself with the followings commands:

Create MYSQL db

The app is configured to work with CORS requests already, so you can make request using postman or testing directly in the frontend app. The API use JWT for authentication so you must generate a token and put it in the headers of the request.

 
Endpoints

