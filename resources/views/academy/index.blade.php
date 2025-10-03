@extends('layouts/layoutMaster')

@section('title', __('Academy'))

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">
	<div class="d-flex flex-column justify-content-center">
		<h4 class="mb-1 mt-3">{{ __('Academy') }}</h4>
		<p class="text-muted">{{ __('Learning content and courses') }}</p>
	</div>
</div>

<div class="card p-4">{{ __('Humano Academy installed') }}</div>
@endsection
