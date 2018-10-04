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

	{{-- Custom Styles --}}
	<link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
	{{-- React Root & Scripts --}}
	<div id="root"></div>
	<script src="{{ asset('js/index.js') }}"></script>
</body>
</html>
