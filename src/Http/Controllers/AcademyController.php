<?php

namespace Idoneo\HumanoAcademy\Http\Controllers;

use App\Http\Controllers\Controller;
use Idoneo\HumanoAcademy\Models\Course;

class AcademyController extends Controller
{
    public function index()
    {
        // Obtener cursos del equipo actual con relaciones
        $courses = Course::published()
            ->forTeam(auth()->user()->currentTeam->id)
            ->with(['chapters.lessons', 'category', 'language'])
            ->orderBy('order')
            ->get();

        return view('humano-academy::academy.index', compact('courses'));
    }

    public function courseDetails($id = null)
    {
        // Intentar cargar desde la base de datos con todas las relaciones
        $dbCourse = Course::with(['chapters.lessons', 'category', 'language'])->find($id);

        if ($dbCourse)
        {
            // Si existe en BD, usar esos datos
            return $this->showCourseFromDatabase($dbCourse);
        }

        // Si no existe en BD, mostrar datos estáticos de ejemplo
        return $this->showStaticCourse($id);
    }

    /**
     * Mostrar curso desde la base de datos.
     */
    private function showCourseFromDatabase(Course $course)
    {
        $courseData = [
            'id' => $course->id,
            'title' => $course->title,
            'instructor' => $course->instructor_name,
            'instructor_title' => $course->instructor_title,
            'category' => $course->category?->name ?? 'Sin categoría',
            'skill_level' => $course->skill_level,
            'students' => $course->students_count,
            'languages' => $course->language?->name ?? 'Español',
            'captions' => $course->has_captions ? 'Yes' : 'No',
            'lectures' => $course->lectures_count,
            'video_duration' => $course->total_duration.' minutes',
            'description' => $course->description,
            'video_url' => $course->chapters->first()?->lessons->first()?->full_video_url ?? 'https://cdn.plyr.io/static/demo/View_From_A_Blue_Moon_Trailer-576p.mp4',
            'video_poster' => $course->chapters->first()?->lessons->first()?->video_poster ?? 'https://cdn.plyr.io/static/demo/View_From_A_Blue_Moon_Trailer-HD.jpg',
        ];

        $chapters = $course->chapters->map(function ($chapter)
        {
            $completedLessons = $chapter->lessons->filter(function ($lesson)
            {
                return $lesson->isCompletedByUser(auth()->id());
            })->count();

            return [
                'title' => $chapter->title,
                'progress' => $completedLessons.' / '.$chapter->lessons_count,
                'duration' => $chapter->total_duration.' min',
                'lessons' => $chapter->lessons->map(function ($lesson)
                {
                    return [
                        'id' => $lesson->id,
                        'title' => $lesson->title,
                        'duration' => $lesson->duration_minutes.' min',
                        'completed' => $lesson->isCompletedByUser(auth()->id()),
                        'video_url' => $lesson->full_video_url ?? '',
                        'video_poster' => $lesson->video_poster ?? '',
                    ];
                })->toArray(),
            ];
        })->toArray();

        return view('humano-academy::academy.course-details', [
            'course' => $courseData,
            'chapters' => $chapters,
        ]);
    }

    /**
     * Mostrar curso estático de ejemplo (para demostración).
     */
    private function showStaticCourse($id)
    {
        $course = [
            'id' => $id ?? 1,
            'title' => 'UI/UX Basic Fundamentals',
            'instructor' => 'Devonne Wallbridge',
            'instructor_title' => 'Web Developer, Designer, and Teacher',
            'category' => 'UI/UX',
            'skill_level' => 'All Levels',
            'students' => 38815,
            'languages' => 'English',
            'captions' => 'Yes',
            'lectures' => 19,
            'video_duration' => '1.5 total hours',
            'description' => 'Learn web design in 1 hour with 25+ simple-to-use rules and guidelines — tons of amazing web design resources included!',
            'video_url' => 'https://cdn.plyr.io/static/demo/View_From_A_Blue_Moon_Trailer-576p.mp4',
            'video_poster' => 'https://cdn.plyr.io/static/demo/View_From_A_Blue_Moon_Trailer-HD.jpg',
        ];

        $chapters = [
            [
                'title' => 'Course Content',
                'progress' => '2 / 5',
                'duration' => '4.4 min',
                'lessons' => [
                    ['title' => '1. Welcome to this course', 'duration' => '2.4 min', 'completed' => true],
                    ['title' => '2. Watch before you start', 'duration' => '4.8 min', 'completed' => true],
                    ['title' => '3. Basic design theory', 'duration' => '5.9 min', 'completed' => false],
                    ['title' => '4. Basic fundamentals', 'duration' => '3.6 min', 'completed' => false],
                    ['title' => '5. What is ui/ux', 'duration' => '10.6 min', 'completed' => false],
                ],
            ],
            [
                'title' => 'Web Design for Web Developers',
                'progress' => '1 / 4',
                'duration' => '4.4 min',
                'lessons' => [
                    ['title' => '1. How to use Pages in Figma', 'duration' => '8:31 min', 'completed' => true],
                    ['title' => '2. What is Lo Fi Wireframe', 'duration' => '2 min', 'completed' => false],
                    ['title' => '3. How to use color in Figma', 'duration' => '5.9 min', 'completed' => false],
                    ['title' => '4. Frames vs Groups in Figma', 'duration' => '3.6 min', 'completed' => false],
                ],
            ],
            [
                'title' => 'Build Beautiful Websites!',
                'progress' => '0 / 6',
                'duration' => '4.4 min',
                'lessons' => [
                    ['title' => '1. Section & Div Block', 'duration' => '8:31 min', 'completed' => false],
                    ['title' => '2. Read-Only Version of Chat App', 'duration' => '8 min', 'completed' => false],
                    ['title' => '3. Webflow Autosave', 'duration' => '2.9 min', 'completed' => false],
                    ['title' => '4. Canvas Settings', 'duration' => '7.6 min', 'completed' => false],
                    ['title' => '5. HTML Tags', 'duration' => '10 min', 'completed' => false],
                    ['title' => '6. Footer (Chat App)', 'duration' => '9.10 min', 'completed' => false],
                ],
            ],
            [
                'title' => 'Final Project',
                'progress' => '2 / 3',
                'duration' => '4.4 min',
                'lessons' => [
                    ['title' => '1. Responsive Blog Site', 'duration' => '10:0 min', 'completed' => true],
                    ['title' => '2. Responsive Portfolio', 'duration' => '13:00 min', 'completed' => true],
                    ['title' => '3. Responsive eCommerce Website', 'duration' => '15 min', 'completed' => false],
                ],
            ],
        ];

        return view('humano-academy::academy.course-details', [
            'course' => $course,
            'chapters' => $chapters,
        ]);
    }
}
