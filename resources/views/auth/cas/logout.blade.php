@extends('layouts.app')

@section('content')
	<iframe src="{{ config('portail.cas.url').'logout' }}" style="position: absolute; height:100%; width:100%; margin-top:-20px" frameborder="0" height="100%" width="100%"></iframe>
@endsection
