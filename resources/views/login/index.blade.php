@extends('layouts.app')

@section('content')
<div style="position: absolute; width: 100%; top: 50%; transform: translateY(-50%); display: flex; justify-content: center;">
	@foreach (config('auth.services') as $name => $provider)
		<div class="card border-info mb-3" style="max-width: 18rem; margin: 15px; flex-basis: 100%">
			<div class="card-header">{{ $provider['name'] }}</div>

			<div class="card-body text-info">
				<h5 class="card-title">{{ $provider['description'] }}</h5>
			</div>
			<div class="card-footer bg-transparent border-success" style="text-align: center">
				<button onClick="window.location.href = '{{ route('login.show', ['provider' => $name, 'redirect' => $redirect]) }}'">Se connecter</button>
			</div>
		</div>
	@endforeach
</div>
@endsection
