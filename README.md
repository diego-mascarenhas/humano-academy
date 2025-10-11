# Humano Academy

[![Latest Version on Packagist](https://img.shields.io/packagist/v/idoneo/humano-academy.svg?style=flat-square)](https://packagist.org/packages/idoneo/humano-academy)
[![Total Downloads](https://img.shields.io/packagist/dt/idoneo/humano-academy.svg?style=flat-square)](https://packagist.org/packages/idoneo/humano-academy)
[![License](https://img.shields.io/packagist/l/idoneo/humano-academy.svg?style=flat-square)](https://packagist.org/packages/idoneo/humano-academy)

A comprehensive Laravel package for creating and managing online courses with video lessons, chapters, progress tracking, and team-based content management.

## Features

✨ **Core Features:**
- 📚 Multi-course management with chapters and lessons
- 🎥 Video-based learning content
- 📊 Student progress tracking
- 👥 Team-based content isolation
- 🎯 Course categories and skill levels
- 🌍 Multi-language support
- 📱 Responsive UI based on Vuexy template
- 🔐 Role-based permissions (admin/student)

## Requirements

- PHP 8.1 or higher
- Laravel 10.x, 11.x, or 12.x
- MySQL/MariaDB database

## Installation

You can install the package via Composer:

```bash
composer require idoneo/humano-academy
```

### Publish Migrations

The package uses a custom installation command to set up all required assets:

```bash
php artisan academy:install
```

This command will:
- Publish database migrations
- Run migrations
- Seed default permissions
- Configure the module

### Manual Installation

If you prefer manual installation:

```bash
# Publish and run migrations
php artisan vendor:publish --tag="humano-academy-migrations"
php artisan migrate

# Seed permissions
php artisan db:seed --class="Idoneo\HumanoAcademy\Database\Seeders\AcademyPermissionsSeeder"
```

## Database Structure

The package creates four main tables:

### `academy_courses`
Main course table with team isolation, instructor info, categories, and metadata.

### `academy_chapters`
Organizes lessons into logical chapters within each course.

### `academy_lessons`
Individual video lessons with duration, ordering, and status.

### `academy_user_progress`
Tracks student progress including watch time, completion status, and last position.

## Configuration

### Team Setup

Courses are isolated by team. To create a course for a specific team:

```php
use Idoneo\HumanoAcademy\Models\Course;

$course = Course::create([
    'team_id' => 1,
    'title' => 'Introduction to Laravel',
    'description' => 'Learn the basics of Laravel framework',
    'instructor_name' => 'John Doe',
    'category_id' => 1, // References categories table
    'language' => 'en', // References languages.code
    'status' => 'published',
]);
```

### Creating Chapters and Lessons

```php
use Idoneo\HumanoAcademy\Models\Chapter;
use Idoneo\HumanoAcademy\Models\Lesson;

$chapter = Chapter::create([
    'course_id' => $course->id,
    'title' => 'Getting Started',
    'order' => 1,
]);

$lesson = Lesson::create([
    'chapter_id' => $chapter->id,
    'title' => 'Installation',
    'description' => 'Learn how to install Laravel',
    'video_path' => 'lesson-1-installation.mp4', // Filename only
    'duration_minutes' => 15,
    'order' => 1,
    'status' => 'published',
]);
```

## Video Management

### Storage Structure

Videos are stored using team-specific hashing for security and isolation:

```
storage/app/public/academy/{team_hash}/videos/
  ├── lesson-1-installation.mp4
  ├── lesson-2-routing.mp4
  └── lesson-3-controllers.mp4
```

### Generate Team Hash

```bash
php artisan tinker
>>> \App\Models\Team::generateTeamHash(1)
=> "a1b2c3d4e5f6"
```

### Create Storage Symlink

```bash
php artisan storage:link
```

### Video URL Generation

The `Lesson` model automatically generates full URLs:

```php
$lesson->full_video_url 
// Returns: http://your-app.test/storage/academy/{team_hash}/videos/lesson-1.mp4
```

## Routes

The package registers the following routes (protected by `web` and `auth` middleware):

```php
GET  /academy                    # List all courses
GET  /academy/list               # Alternative route for menu
GET  /academy/course/{id}        # Course details with lessons
```

## Models and Relationships

### Course Model

```php
// Relationships
$course->team()          // BelongsTo Team
$course->category()      // BelongsTo Category
$course->language()      // BelongsTo Language
$course->chapters()      // HasMany Chapter
$course->lessons()       // HasManyThrough Lesson

// Attributes
$course->lectures_count  // Total number of lessons
$course->total_duration  // Sum of all lesson durations

// Scopes
Course::published()      // Only published courses
Course::forTeam($teamId) // Filter by team
```

### Chapter Model

```php
$chapter->course()       // BelongsTo Course
$chapter->lessons()      // HasMany Lesson
```

### Lesson Model

```php
$lesson->chapter()       // BelongsTo Chapter
$lesson->full_video_url  // Computed full video URL
```

### UserProgress Model

```php
$progress->user()        // BelongsTo User
$progress->lesson()      // BelongsTo Lesson
$progress->course()      // BelongsTo Course (through lesson)
```

## Permissions

The package includes the following permissions:

- `academy.list` - View course list
- `academy.show` - View course details
- `academy.course.details` - Access course content

Permissions are automatically assigned to the `admin` role. For students:

```php
use Spatie\Permission\Models\Role;

$studentRole = Role::firstOrCreate(['name' => 'student']);
$studentRole->givePermissionTo(['academy.list', 'academy.show', 'academy.course.details']);
```

## Usage Example

### Controller Example

```php
use Idoneo\HumanoAcademy\Models\Course;

public function index()
{
    $courses = Course::published()
        ->forTeam(auth()->user()->currentTeam->id)
        ->with(['chapters.lessons', 'category', 'language'])
        ->orderBy('order')
        ->get();
        
    return view('your-view', compact('courses'));
}
```

### Blade Template Example

```blade
@foreach($courses as $course)
    <div class="course-card">
        <h3>{{ $course->title }}</h3>
        <p>{{ $course->description }}</p>
        <p>Instructor: {{ $course->instructor_name }}</p>
        <p>Duration: {{ $course->total_duration }} minutes</p>
        <p>Lessons: {{ $course->lectures_count }}</p>
        <a href="{{ route('academy.course.details', $course->id) }}">
            View Course
        </a>
    </div>
@endforeach
```

## Seeders

### Create Custom Course Seeder

Example seeder structure for populating courses:

```php
use Idoneo\HumanoAcademy\Models\Course;
use Idoneo\HumanoAcademy\Models\Chapter;
use Idoneo\HumanoAcademy\Models\Lesson;

public function run()
{
    $course = Course::create([
        'team_id' => 1,
        'title' => 'Laravel Fundamentals',
        'description' => 'Complete Laravel course',
        'instructor_name' => 'Jane Smith',
        'category_id' => 1,
        'language' => 'en',
        'status' => 'published',
    ]);

    $chapter = Chapter::create([
        'course_id' => $course->id,
        'title' => 'Introduction',
        'order' => 1,
    ]);

    Lesson::create([
        'chapter_id' => $chapter->id,
        'title' => 'Welcome',
        'video_path' => 'welcome.mp4',
        'duration_minutes' => 10,
        'order' => 1,
        'status' => 'published',
    ]);
}
```

## Frontend Assets

The package uses the Vuexy admin template. Make sure you have the following assets available:

- Select2 for dropdowns
- Plyr for video playback
- Custom Academy CSS

Include in your layout:

```blade
@section('vendor-style')
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/plyr/plyr.css')}}" />
@endsection

@section('page-style')
    <link rel="stylesheet" href="{{asset('assets/vendor/css/pages/app-academy.css')}}" />
@endsection
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [IDONEO](https://idoneo.cl)
- [All Contributors](../../contributors)

## License

The GNU Affero General Public License v3.0 or later (AGPL-3.0-or-later). Please see [License File](LICENSE.md) for more information.

## Support

- [GitHub Issues](https://github.com/idoneo/humano-academy/issues)
- [Documentation](https://github.com/idoneo/humano-academy)
- [IDONEO Website](https://idoneo.cl)
