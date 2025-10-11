@extends('layouts/layoutMaster')

@section('title', __('Academy Course Details'))

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/plyr/plyr.css')}}" />
@endsection

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/app-academy-details.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/plyr/plyr.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/app-academy-course-details.js')}}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
	// Obtener el reproductor Plyr
	const player = document.querySelector('#plyr-video-player');
	
	// Agregar event listeners a cada lección
	document.querySelectorAll('.lesson-item').forEach(function(lessonElement) {
		lessonElement.addEventListener('click', function(e) {
			// Prevenir que el checkbox se marque automáticamente
			if (e.target.type !== 'checkbox') {
				e.preventDefault();
			}
			
			const videoUrl = this.getAttribute('data-video-url');
			const videoPoster = this.getAttribute('data-video-poster');
			const lessonId = this.getAttribute('data-lesson-id');
			
			if (videoUrl) {
				// Cambiar el video
				player.src = videoUrl;
				if (videoPoster) {
					player.poster = videoPoster;
				}
				
				// Reproducir automáticamente
				player.play();
				
				// Scroll hacia el video
				window.scrollTo({
					top: 0,
					behavior: 'smooth'
				});
			}
		});
	});
});
</script>
@endsection

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">
	<div class="d-flex flex-column justify-content-center">
		<h4 class="mb-1 mt-3"><span class="text-muted fw-light">{{ __('Academy') }}/</span> {{ __('Course Details') }}</h4>
		<p class="text-muted">{{ __('Course overview and content') }}</p>
	</div>
	<div class="d-flex align-content-center flex-wrap gap-3">
		<a href="{{ route('academy.index') }}" class="btn btn-label-secondary waves-effect waves-light">
			<i class="ti ti-arrow-left me-1"></i>{{ __('Back to Courses') }}
		</a>
	</div>
</div>

<div class="card g-3 mt-5">
	<div class="card-body row g-3">
		<div class="col-lg-8">
			<div class="d-flex justify-content-between align-items-center flex-wrap mb-2 gap-1">
				<div class="me-1">
					<h5 class="mb-1">{{ $course['title'] }}</h5>
					<p class="mb-1">Prof. <span class="fw-medium">{{ $course['instructor'] }}</span></p>
				</div>
				<div class="d-flex align-items-center">
					<span class="badge bg-label-danger">{{ $course['category'] }}</span>
					<i class='ti ti-share ti-sm mx-4'></i>
					<i class='ti ti-bookmarks ti-sm'></i>
				</div>
			</div>
			
			<div class="card academy-content shadow-none border">
				<div class="p-2">
					<div class="cursor-pointer">
						<video class="w-100" poster="{{ $course['video_poster'] }}" id="plyr-video-player" playsinline controls>
							<source src="{{ $course['video_url'] }}" type="video/mp4" />
						</video>
					</div>
				</div>
				
				<div class="card-body">
					<h5 class="mb-2">{{ __('About this course') }}</h5>
					<p class="mb-0 pt-1">{{ $course['description'] }}</p>
					
					<hr class="my-4">
					
					<h5>{{ __('By the numbers') }}</h5>
					<div class="d-flex flex-wrap">
						<div class="me-5">
							<p class="text-nowrap"><i class='ti ti-checks ti-sm me-2 mt-n2'></i>{{ __('Skill level') }}: {{ $course['skill_level'] }}</p>
							<p class="text-nowrap"><i class='ti ti-user ti-sm me-2 mt-n2'></i>{{ __('Students') }}: {{ number_format($course['students']) }}</p>
							<p class="text-nowrap"><i class='ti ti-flag ti-sm me-2 mt-n2'></i>{{ __('Languages') }}: {{ $course['languages'] }}</p>
							<p class="text-nowrap "><i class='ti ti-file ti-sm me-2 mt-n2'></i>{{ __('Captions') }}: {{ $course['captions'] }}</p>
						</div>
						<div>
							<p class="text-nowrap"><i class='ti ti-pencil ti-sm me-2 mt-n2'></i>{{ __('Lectures') }}: {{ $course['lectures'] }}</p>
							<p class="text-nowrap "><i class='ti ti-clock ti-sm me-2 mt-n2'></i>{{ __('Video') }}: {{ $course['video_duration'] }}</p>
						</div>
					</div>
					
					<hr class="mb-4 mt-2">
					
					<h5>{{ __('Description') }}</h5>
					<p class="mb-4">
						The material of this course is also covered in my other course about web design and development
						with HTML5 & CSS3. Scroll to the bottom of this page to check out that course, too!
						If you're already taking my other course, you already have all it takes to start designing beautiful
						websites today!
					</p>
					<p class="mb-4">
						"Best web design course: If you're interested in web design, but want more than
						just a "how to use WordPress" course,I highly recommend this one." — Florian Giusti
					</p>
					<p>
						"Very helpful to us left-brained people: I am familiar with HTML, CSS, JQuery,
						and Twitter Bootstrap, but I needed instruction in web design. This course gave me practical,
						impactful techniques for making websites more beautiful and engaging." — Susan Darlene Cain
					</p>
					
					<hr class="my-4">
					
					<h5>{{ __('Instructor') }}</h5>
					<div class="d-flex justify-content-start align-items-center user-name">
						<div class="avatar-wrapper">
							<div class="avatar me-2">
								<img src="{{asset('assets/img/avatars/11.png')}}" alt="Avatar" class="rounded-circle">
							</div>
						</div>
						<div class="d-flex flex-column">
							<span class="fw-medium">{{ $course['instructor'] }}</span>
							<small class="text-muted">{{ $course['instructor_title'] }}</small>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="col-lg-4">
			<div class="accordion stick-top accordion-bordered" id="courseContent">
				@foreach($chapters as $index => $chapter)
				<div class="accordion-item {{ $index === 0 ? 'active' : '' }} mb-0">
					<div class="accordion-header" id="heading{{ $index }}">
						<button type="button" class="accordion-button bg-lighter rounded-0 {{ $index !== 0 ? 'collapsed' : '' }}" 
							data-bs-toggle="collapse" 
							data-bs-target="#chapter{{ $index }}" 
							aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" 
							aria-controls="chapter{{ $index }}">
							<span class="d-flex flex-column">
								<span class="h5 mb-1">{{ $chapter['title'] }}</span>
								<span class="fw-normal text-body">{{ $chapter['progress'] }} | {{ $chapter['duration'] }}</span>
							</span>
						</button>
					</div>
					<div id="chapter{{ $index }}" 
						class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" 
						data-bs-parent="#courseContent">
					<div class="accordion-body py-3 border-top">
						@foreach($chapter['lessons'] as $lessonIndex => $lesson)
						<div class="form-check d-flex align-items-center {{ $loop->last ? '' : 'mb-3' }} lesson-item" 
							style="cursor: pointer;"
							data-video-url="{{ $lesson['video_url'] }}"
							data-video-poster="{{ $lesson['video_poster'] }}"
							data-lesson-id="{{ $lesson['id'] }}">
							<input class="form-check-input" 
								type="checkbox" 
								id="lesson{{ $index }}_{{ $lessonIndex }}" 
								{{ $lesson['completed'] ? 'checked' : '' }} />
							<label for="lesson{{ $index }}_{{ $lessonIndex }}" class="form-check-label ms-3" style="cursor: pointer;">
								<span class="mb-0 h6">{{ $lesson['title'] }}</span>
								<span class="text-muted d-block">{{ $lesson['duration'] }}</span>
							</label>
						</div>
						@endforeach
					</div>
					</div>
				</div>
				@endforeach
			</div>
		</div>
	</div>
</div>

@endsection

