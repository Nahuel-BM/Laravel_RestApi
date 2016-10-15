# Laravel PHP Framework
/Users/mohammadselimmiah/Sites/RestApi
[![Build Status](https://travis-ci.org/laravel/framework.svg)](https://travis-ci.org/laravel/framework)
[![Total Downloads](https://poser.pugx.org/laravel/framework/d/total.svg)](https://packagist.org/packages/laravel/framework)
[![Latest Stable Version](https://poser.pugx.org/laravel/framework/v/stable.svg)](https://packagist.org/packages/laravel/framework)
[![Latest Unstable Version](https://poser.pugx.org/laravel/framework/v/unstable.svg)](https://packagist.org/packages/laravel/framework)
[![License](https://poser.pugx.org/laravel/framework/license.svg)](https://packagist.org/packages/laravel/framework)

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable, creative experience to be truly fulfilling. Laravel attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as authentication, routing, sessions, queueing, and caching.

Laravel is accessible, yet powerful, providing tools needed for large, robust applications. A superb inversion of control container, expressive migration system, and tightly integrated unit testing support give you the tools you need to build any application with which you are tasked.

## Official Documentation

Documentation for the framework can be found on the [Laravel website](http://laravel.com/docs).

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](http://laravel.com/docs/contributions).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell at taylor@laravel.com. All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
# Laravel_RestApi
/Users/mohammadselimmiah/Sites/RestApi
$ laravel new RestApi
$ cd RestApi/
Configure .env for database and email
$ composer update

$ php artisan help key:generate
$ php artisan make:auth //this will create everything necessary for user login and registration

modify routes.php accordingly

modify AuthController under App\Http\Controllers\Auth;
add 'api_token' => str_random(60), in the create function
return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'api_token' => str_random(60),
            'password' => bcrypt($data['password']),
        ]);

prepare migration table.php
Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('api_token',60)->unique();
            $table->rememberToken();
            $table->timestamps();
        });

CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `api_token` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_api_token_unique` (`api_token`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

change User model and add api_token
 protected $fillable = ['name', 'email', 'password','api_token',];
 protected $hidden = ['password', 'remember_token','api_token',];

$ php artisan migrate //database tables will be created

https://www.youtube.com/watch?v=j2B5EfMvExU
$ php artisan serve

if you change .env you have to run $ php artisan serve again
https://github.com/Selimcse98/Laravel_RestApi.git

Create new user
http://localhost:8000/register

$ php artisan make:migration create_notes_table --create=notes 
modify 2016_07_24_153447_create_notes_table migration
Schema::create('notes', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('text');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
        });

CREATE TABLE `notes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `text` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `notes_user_id_foreign` (`user_id`),
  CONSTRAINT `notes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

$ php artisan make:model Note
protected $fillable = ['text', 'user_id',];
protected $hidden = ['user_id',];

$ php artisan make:controller NoteController --resource

$ php artisan migrate
Migrated: 2016_07_24_153447_create_notes_table

modify NoteController:
use Auth;

    public function store(Request $request)
    {
        $note = $request->input('note');

        return Note::create([
            'text'=>$note,
            'user_id'=>Auth::guard('api')-id()
        ]);
    }

modify routes.php

tutorial for curl request: http://www.yilmazhuseyin.com/blog/dev/curl-tutorial-examples-usage/

$ curl --data "note=testnote&api_token=mkeLYqdS4eesN79Jo42k7o6emPpSGjCFVNQ6iQKoQuDCY2gmtTG1oguxb6XY" http://localhost:8000/api/note

{"text":"testnote","updated_at":"2016-07-24 16:56:45","created_at":"2016-07-24 16:56:45","id":1}

$ curl --data "note=Second Note&api_token=mkeLYqdS4eesN79Jo42k7o6emPpSGjCFVNQ6iQKoQuDCY2gmtTG1oguxb6XY" http://localhost:8000/api/note
{"text":"Second Note","updated_at":"2016-07-24 16:59:23","created_at":"2016-07-24 16:59:23","id":2}


modify show method:
 public function show($id)
    {
        return response()->json(
          Note::where('id',$id)
              ->where('user_id',Auth::guard('api')->id())->first()
        );
    }

browser:
http://localhost:8000/api/note/1?api_token=mkeLYqdS4eesN79Jo42k7o6emPpSGjCFVNQ6iQKoQuDCY2gmtTG1oguxb6XY
http://localhost:8000/api/note/2?api_token=mkeLYqdS4eesN79Jo42k7o6emPpSGjCFVNQ6iQKoQuDCY2gmtTG1oguxb6XY
http://localhost:8000/api/note?api_token=mkeLYqdS4eesN79Jo42k7o6emPpSGjCFVNQ6iQKoQuDCY2gmtTG1oguxb6XY

$ curl --request GET http://localhost:8000/api/note/1?api_token=mkeLYqdS4eesN79Jo42k7o6emPpSGjCFVNQ6iQKoQuDCY2gmtTG1oguxb6XY
{"id":1,"created_at":"2016-07-24 16:56:45","updated_at":"2016-07-24 16:56:45","text":"testnote"}


$ curl --request GET http://localhost:8000/api/note/2?api_token=mkeLYqdS4eesN79Jo42k7o6emPpSGjCFVNQ6iQKoQuDCY2gmtTG1oguxb6XY
{"id":2,"created_at":"2016-07-24 16:59:23","updated_at":"2016-07-24 16:59:23","text":"Second Note"}Mohammads-MacBook-Air:RestApi mohammadselimmiah$ 

$ curl --request GET http://localhost:8000/api/note?api_token=mkeLYqdS4eesN79Jo42k7o6emPpSGjCFVNQ6iQKoQuDCY2gmtTG1oguxb6XY
