<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseRequest;
use App\Models\Course;
use App\Services\AdminActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));

        $courses = Course::query()
            ->withCount('certificates')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name_en', 'like', "%{$search}%")
                        ->orWhere('name_ja', 'like', "%{$search}%")
                        ->orWhere('level_code', 'like', "%{$search}%");
                });
            })
            ->orderBy('level_code')
            ->paginate(15)
            ->withQueryString();

        return view('admin.courses.index', [
            'courses' => $courses,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.courses.form', [
            'course' => new Course([
                'default_hours_per_day' => 3,
                'default_days_per_week' => 3,
                'learning_context' => [['ja' => '', 'en' => '']],
            ]),
        ]);
    }

    public function store(CourseRequest $request, AdminActivityLogger $logger): RedirectResponse
    {
        $course = Course::create($this->payload($request));

        app(AdminActivityLogger::class)->logCreated('App\Models\Course', $course->id, $course->toArray(), $request);

        return redirect()
            ->route('admin.courses.show', $course)
            ->with('status', "Course {$course->name_en} was created.");
    }

    public function show(Course $course): View
    {
        $course->loadCount('certificates');

        return view('admin.courses.show', ['course' => $course]);
    }

    public function edit(Course $course): View
    {
        return view('admin.courses.form', ['course' => $course]);
    }

    public function update(CourseRequest $request, Course $course, AdminActivityLogger $logger): RedirectResponse
    {
        $oldValues = $course->toArray();
        $course->update($this->payload($request));

        app(AdminActivityLogger::class)->logUpdated('App\Models\Course', $course->id, $oldValues, $course->fresh()->toArray(), $request);

        return redirect()
            ->route('admin.courses.show', $course)
            ->with('status', 'Course details were updated.');
    }

    public function destroy(Course $course, AdminActivityLogger $logger): RedirectResponse
    {
        if ($course->certificates()->exists()) {
            return back()->with('error', 'This course is used by certificates and cannot be deleted.');
        }

        $oldValues = $course->toArray();
        $course->delete();

        $logger->logDeleted('App\Models\Course', $course->id, $oldValues, request());

        return redirect()
            ->route('admin.courses.index')
            ->with('status', 'Course was removed.');
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(CourseRequest $request): array
    {
        $data = $request->validated();

        $data['learning_context'] = array_values(array_map(
            static fn (array $item): array => [
                'ja' => (string) $item['ja'],
                'en' => (string) $item['en'],
            ],
            $data['learning_context'],
        ));

        return $data;
    }
}
