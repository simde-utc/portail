<!DOCTYPE html> 
<html lang="{{ app()->getLocale() }}"> 
<head> 
	<meta charset="utf-8"> 
	<meta http-equiv="X-UA-Compatible" content="IE=edge"> 
	<meta name="viewport" content="width=device-width, initial-scale=1"> 

	{{-- CSRF Token --}} 
	<meta name="csrf-token" content="{{ csrf_token() }}"> 
	<title>BDE-UTC - Portail des Associations</title> 

	{{-- Custom Bootstrap 4 --}} 
	<link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}"> 

	{{-- Font Awesome 5 --}} 
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">

	{{-- TODO: Replace with material icons ?
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">  --}}

	{{-- Custom Styles --}} 
	<link rel="stylesheet" href="{{ asset('css/app.css') }}"> 
</head> 
<body> 
	{{-- React Root & Scripts --}} 
	<div id="root"></div> 
	<script src="{{ asset('js/index.js') }}"></script> 
</body> 
</html> 