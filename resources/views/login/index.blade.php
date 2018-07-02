@extends('layouts.app')

@section('content')
<div class="row auth-services-container">

	@foreach (config('auth.services') as $name => $provider)
		<div class="col-md-6 mb-4 mb-md-0">
			<div class="auth-services drop-shadow card mx-auto">
				<div class="card-body">
					<b class="d-block mb-2">{{ $provider['name'] }}</b>

					{{ $provider['description'] }}
				</div>

				<div class="card-footer bg-transparent p-0">
					<div class="row m-0">
						@if ($provider['registrable'])
							<div class="col-6 p-0">
								<a class="btn btn-primary w-100 left" href="{{ route('register.show', ['provider' => $name]) }}">
									S'enregistrer
								</a>
							</div>
							<div class="col-6 p-0">
								<a class="btn btn-primary w-100 right" href="{{ route('login.show', ['provider' => $name]) }}">
									Se connecter
								</a>
							</div>
						@else
							<div class="col-12 p-0">
								<a class="btn btn-primary w-100" href="{{ route('login.show', ['provider' => $name]) }}">
									Se connecter
								</a>
							</div>
						@endif
					</div>
				</div>
			</div>
		</div>
	@endforeach

</div>
@endsection
