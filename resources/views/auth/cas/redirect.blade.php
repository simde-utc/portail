<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body>
	<div style="position: absolute; width: 100%; top: 50%; transform: translateY(-50%); display: flex; justify-content: center;">
		<div>
			<span style="color: green" class="glyphicon glyphicon-ok"></span><br />
			Vous allez être redirigé, vous avez été connecté avec succès !
		</div>
	</div>
</body>
</html>
