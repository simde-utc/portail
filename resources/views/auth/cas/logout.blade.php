@extends('layouts.app')

@section('content')
<div class="container">
	<iframe src="{{ config('portail.cas.url').'logout' }}"></iframe>
	<a href={{ route('home') }}>Retourner au portail</a>
	<a href={{ $service === null ? URL::previous() : $service }}>Retourner à la page précédente</a>
</div>
@endsection
