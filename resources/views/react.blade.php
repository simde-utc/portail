<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	{{-- CSRF Token --}}
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>BDE-UTC - Portail des Associations</title>
</head>
<body>
	<div id="root"></div>

	{{-- React Script --}}
	<script src="{{ asset('js/index.js') }}"></script>
</body>
</html>
