@extends('layouts/layoutMaster')
@php
  $configData = Helper::appClasses();
@endphp

@section('title', 'Academy Course - Apps')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/plyr/plyr.css')}}" />
@endsection

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/app-academy.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/plyr/plyr.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/app-academy-course.js')}}"></script>
@endsection

@section('content')
<h4 class="py-3 mb-4"><span class="text-muted fw-light">Academy/</span> My Courses</h4>

<div class="app-academy">
  <div class="card p-0 mb-4">
    <div class="card-body d-flex flex-column flex-md-row justify-content-between p-0 pt-4">
      <div class="app-academy-md-25 card-body py-0">
        <img src="{{asset('assets/img/illustrations/bulb-'.$configData['style'].'.png') }}" class="img-fluid app-academy-img-height scaleX-n1-rtl" alt="Bulb in hand" data-app-light-img="illustrations/bulb-light.png" data-app-dark-img="illustrations/bulb-dark.png" height="90" />
      </div>
      <div class="app-academy-md-50 card-body d-flex align-items-md-center flex-column text-md-center">
        <h3 class="card-title mb-4 lh-sm px-md-5 lh-lg">
          Education, talents, and career opportunities.
          <span class="text-primary fw-medium text-nowrap">All in one place</span>.
        </h3>
        <p class="mb-3">
          Grow your skill with the most reliable online courses and certifications in marketing, information technology,
          programming, and data science.
        </p>
        <div class="d-flex align-items-center justify-content-between app-academy-md-80">
          <input type="search" id="courseSearchInput" placeholder="Find your course" class="form-control me-2" />
          <button type="submit" class="btn btn-primary btn-icon"><i class="ti ti-search"></i></button>
        </div>
      </div>
      <div class="app-academy-md-25 d-flex align-items-end justify-content-end">
        <img src="{{asset('assets/img/illustrations/pencil-rocket.png') }}" alt="pencil rocket" height="188" class="scaleX-n1-rtl" />
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header d-flex flex-wrap justify-content-between gap-3">
      <div class="card-title mb-0 me-1">
        <h5 class="mb-1">My Courses</h5>
        <p class="text-muted mb-0">Total {{ $courses->count() }} course{{ $courses->count() != 1 ? 's' : '' }} available</p>
      </div>
      <div class="d-flex justify-content-md-end align-items-center gap-4 flex-wrap">
        <select id="select2_course_select" class="select2 form-select" data-placeholder="All Courses">
          <option value="">All Courses</option>
          @foreach($courses->unique('category_id')->filter(fn($c) => $c->category) as $course)
            <option value="{{ $course->category->name }}">{{ $course->category->name }}</option>
          @endforeach
        </select>

        <label class="switch">
          <input type="checkbox" class="switch-input" id="hideCompletedToggle">
          <span class="switch-toggle-slider">
            <span class="switch-on"></span>
            <span class="switch-off"></span>
          </span>
          <span class="switch-label text-nowrap mb-0">Hide completed</span>
        </label>
      </div>
    </div>
    <div class="card-body">
      @if($courses->isEmpty())
        <div class="text-center py-5">
          <div class="mb-3">
            <i class="ti ti-school" style="font-size: 4rem; color: #ddd;"></i>
          </div>
          <h5 class="mb-2">No courses available</h5>
          <p class="text-muted">There are no courses published yet. Check back soon!</p>
        </div>
      @else
        <div class="row gy-4 mb-4" id="coursesContainer">
          @foreach($courses as $course)
          <div class="col-sm-6 col-lg-4 course-item" data-category="{{ $course->category?->name ?? '' }}">
            <div class="card p-2 h-100 shadow-none border">
              <div class="rounded-2 text-center mb-3">
                <a href="{{ route('academy.course.details', $course->id) }}">
                  @if($course->thumbnail)
                    <img class="img-fluid" src="{{ $course->thumbnail }}" alt="{{ $course->title }}" />
                  @else
                    <div class="bg-label-primary d-flex align-items-center justify-content-center" style="height: 200px; border-radius: 0.375rem;">
                      <i class="ti ti-school" style="font-size: 4rem;"></i>
                    </div>
                  @endif
                </a>
              </div>
              <div class="card-body p-3 pt-2">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  @if($course->category)
                    <span class="badge bg-label-primary">{{ $course->category->name }}</span>
                  @else
                    <span class="badge bg-label-secondary">Uncategorized</span>
                  @endif
                  <h6 class="d-flex align-items-center justify-content-center gap-1 mb-0">
                    <span class="text-muted">({{ $course->students_count ?? 0 }})</span>
                  </h6>
                </div>
                <a href="{{ route('academy.course.details', $course->id) }}" class="h5">{{ $course->title }}</a>
                <p class="mt-2">{{ Str::limit($course->description, 80) }}</p>
                <p class="d-flex align-items-center"><i class="ti ti-clock me-2 mt-n1"></i>{{ $course->total_duration }} minutes</p>
                
                <!-- Progress bar (to be implemented with user progress) -->
                <div class="progress mb-4" style="height: 8px">
                  <div class="progress-bar w-0" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                
                <div class="d-flex flex-column flex-md-row gap-2 text-nowrap">
                  <a class="app-academy-md-50 btn btn-label-secondary me-md-2 d-flex align-items-center" href="{{ route('academy.course.details', $course->id) }}">
                    <i class="ti ti-rotate-clockwise-2 align-middle scaleX-n1-rtl me-2 mt-n1 ti-sm"></i><span>Start Over</span>
                  </a>
                  <a class="app-academy-md-50 btn btn-label-primary d-flex align-items-center" href="{{ route('academy.course.details', $course->id) }}">
                    <span class="me-2">Continue</span><i class="ti ti-chevron-right scaleX-n1-rtl ti-sm"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
          @endforeach
        </div>
      @endif
    </div>
  </div>

  <div class="row gy-4 mb-4">
    <div class="col-lg-6">
      <div class="card bg-label-primary h-100">
        <div class="card-body d-flex justify-content-between flex-wrap-reverse">
          <div class="mb-0 w-100 app-academy-sm-60 d-flex flex-column justify-content-between text-center text-sm-start">
            <div class="card-title">
              <h4 class="text-primary mb-2">Earn a Certificate</h4>
              <p class="text-body w-sm-80 app-academy-xl-100">
                Get the right professional certificate program for you.
              </p>
            </div>
            <div class="mb-0"><button class="btn btn-primary">View Programs</button></div>
          </div>
          <div class="w-100 app-academy-sm-40 d-flex justify-content-center justify-content-sm-end h-px-150 mb-3 mb-sm-0">
            <img class="img-fluid scaleX-n1-rtl" src="{{ asset('assets/img/illustrations/boy-app-academy.png')}}" alt="boy illustration" />
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card bg-label-danger h-100">
        <div class="card-body d-flex justify-content-between flex-wrap-reverse">
          <div class="mb-0 w-100 app-academy-sm-60 d-flex flex-column justify-content-between text-center text-sm-start">
            <div class="card-title">
              <h4 class="text-danger mb-2">Best Rated Courses</h4>
              <p class="text-body app-academy-sm-60 app-academy-xl-100">
                Enroll now in the most popular and best rated courses.
              </p>
            </div>
            <div class="mb-0"><button class="btn btn-danger">View Courses</button></div>
          </div>
          <div class="w-100 app-academy-sm-40 d-flex justify-content-center justify-content-sm-end h-px-150 mb-3 mb-sm-0">
            <img class="img-fluid scaleX-n1-rtl" src="{{asset('assets/img/illustrations/girl-app-academy.png')}}" alt="girl illustration" />
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('courseSearchInput');
    const categorySelect = document.getElementById('select2_course_select');
    const courseItems = document.querySelectorAll('.course-item');
    
    function filterCourses() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedCategory = categorySelect.value;
        
        courseItems.forEach(item => {
            const title = item.querySelector('.h5').textContent.toLowerCase();
            const description = item.querySelector('p.mt-2').textContent.toLowerCase();
            const category = item.dataset.category;
            
            const matchesSearch = title.includes(searchTerm) || description.includes(searchTerm);
            const matchesCategory = !selectedCategory || category === selectedCategory;
            
            if (matchesSearch && matchesCategory) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }
    
    searchInput.addEventListener('input', filterCourses);
    if (categorySelect) {
        categorySelect.addEventListener('change', filterCourses);
    }
});
</script>
@endsection
