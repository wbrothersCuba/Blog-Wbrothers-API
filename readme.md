
# Blog Wbrothers

  

This RESTful API is generated with Laravel, for a blog. In the file database.sql (MYSQL) are the SQL commands to generate the BD, but may be needed to adapt the file.env and modify the line to your configurations:

  

*env:*

  

DB_CONNECTION=mysql

DB_HOST=127.0.0.1

DB_PORT=3306

DB_DATABASE=api_rest_laravel

DB_USERNAME=root

DB_PASSWORD=null

  

and there is a file named api_rest_laravel.sql with the commands required to restore it.

  

Create MYSQL db

  

The app is already configured to work with CORS requests, so you can make requests using Postman or testing directly in the frontend app. The API uses JWT for authentication, so you must generate a token and put it in the headers of the request.

  
  

## Endpoints
> User

 - POST /api/login: Sign in.
 - POST /api/register: Sign up.
 - PUT /api_url/user/update: User settings.
 - POST /api/user/upload: Upload an avatar image.
 - GET /api/user/avatar/{filename}: Show the user avatar image.
 - GET /api/user/detail/{id}: Show the user detail.

  
> Categories

 - GET /api/category/index: Return all categories.
 - GET /api/category/show(id): Return a category with the given id.
 - POST /api/category/store : Add a new category.
 - PUT /api/category/update/{id}: Change post data.

> Posts

- GET /api/post/index .
- GET /api/post/show(id).
- POST /api/post/store : Add a new post.
- PUT /api/post/update/{id}: Change post data.
- DELETE /api/post/destroy/{id}: Delete data of a post.
- POST /api/post/upload: upload a post image.
- GET /api/post/getImage/{filename}:: upload a post image.
- GET /api/post/getPostbyCategory/{category_id}:: upload a post image.
- GET /api/post/getPosytByUser/{user_id}:: upload a post image.



## TO DO

 -  Update laravel version to 11.
