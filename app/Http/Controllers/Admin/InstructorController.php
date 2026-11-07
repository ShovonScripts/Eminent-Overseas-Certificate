<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\InstructorRequest;
use App\Models\Instructor;
use App\Services\AdminActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstructorController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));

        $instructors = Instructor::query()
            ->withCount('certificates')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.instructors.index', [
            'instructors' => $instructors,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.instructors.form', [
            'instructor' => new Instructor([
                'title' => '講師 / Instructor',
                'is_active' => true,
            ]),
        ]);
    }

    public function store(InstructorRequest $request, AdminActivityLogger $logger): RedirectResponse
    {
        $instructor = Instructor::create($this->payload($request));

        app(AdminActivityLogger::class)->logCreated('App\Models\Instructor', $instructor->id, $instructor->toArray(), $request);

        return redirect()
            ->route('admin.instructors.index')
            ->with('status', "Instructor {$instructor->name} was created.");
    }

    public function edit(Instructor $instructor): View
    {
        return view('admin.instructors.form', ['instructor' => $instructor]);
    }

    public function update(InstructorRequest $request, Instructor $instructor, AdminActivityLogger $logger): RedirectResponse
    {
        $oldValues = $instructor->toArray();
        $instructor->update($this->payload($request));

        app(AdminActivityLogger::class)->logUpdated('App\Models\Instructor', $instructor->id, $oldValues, $instructor->fresh()->toArray(), $request);

        return redirect()
            ->route('admin.instructors.index')
            ->with('status', 'Instructor details were updated.');
    }

    public function destroy(Instructor $instructor, AdminActivityLogger $logger): RedirectResponse
    {
        if ($instructor->certificates()->exists()) {
            return back()->with('error', 'This instructor is attached to certificates and cannot be deleted. Mark them inactive instead.');
        }

        $oldValues = $instructor->toArray();
        $instructor->delete();

        $logger->logDeleted('App\Models\Instructor', $instructor->id, $oldValues, request());

        return redirect()
            ->route('admin.instructors.index')
            ->with('status', 'Instructor was removed.');
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(InstructorRequest $request): array
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
