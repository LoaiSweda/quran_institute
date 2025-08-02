<?php

namespace App\Http\Controllers;

use App\Models\Library;
use App\Models\Category;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder; // Import the Builder class

class LibraryController extends Controller
{
    /**
     * Display a listing of the library resources (for initial page load).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Only fetch categories for the dropdown on initial page load
        $categories = Category::all();
        return view('libraries.index', compact('categories'));
    }

    /**
     * API endpoint for filtered library resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiIndex(Request $request)
    {
        $libraries = $this->filterLibraries($request)->paginate(10);

        return response()->json([
            'html' => view('libraries.partials.library_items', ['libraries' => $libraries])->render(),
            'pagination' => $libraries->links()->toHtml()
        ]);
    }

    /**
     * Common filtering logic for both web and API.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function filterLibraries(Request $request): Builder
    {
        $query = Library::query()->with(['category', 'file', 'uploader', 'institute']);

        $this->applyRoleFilters($query);

        $this->applySearchFilters($query, $request);

        return $query->latest();
    }

    /**
     * Apply role-based filters to the query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    protected function applyRoleFilters(Builder $query): void
    {
        if (Auth::user()->hasRole('institute manager')) {
            $instituteId = Auth::user()->institute_id;
            $query->where(function ($q) use ($instituteId) {
                $q->where('institute_id', $instituteId)
                    ->orWhereNull('institute_id'); // Global resources
            });
        }

        if (Auth::user()->hasRole('teacher')) {
            $instituteId = Auth::user()->institute_id;
            $userId = Auth::id();
            $query->where(function ($q) use ($instituteId, $userId) {
                $q->where('institute_id', $instituteId)
                    ->orWhereNull('institute_id') // Global resources
                    ->orWhere('user_id', $userId);
            });
        }
    }

    /**
     * Apply search and category filters to the query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function applySearchFilters(Builder $query, Request $request): void
    {
        if ($request->has('search') && $request->input('search') != '') {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhereHas('category', function($subQuery) use ($search) {
                        $subQuery->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($request->has('category_id') && $request->input('category_id') != '') {
            $query->where('category_id', $request->input('category_id'));
        }
    }

    /**
     * Show the form for creating a new library resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $categories = Category::all();
        return view('libraries.create', compact('categories'));
    }

    /**
     * Store a newly created library resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'author' => 'nullable|string|max:255',
            'isbn' => 'nullable|string|max:255',
            'file' => 'required|file|mimes:pdf,mp3,mp4,doc,docx,ppt,pptx,xls,xlsx|max:102400',
        ]);

        $file = $request->file('file');
        $path = $file->store('public/library_files');

        $uploadedFile = File::create([
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        Library::create([
            'name' => $request->name,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'author' => $request->author,
            'isbn' => $request->isbn,
            'file_id' => $uploadedFile->id,
            'is_visible' => $request->has('is_visible'),
            'user_id' => Auth::id(),
            'institute_id' => Auth::user()->institute_id ?? null,
        ]);

        return redirect()->route(Auth::user()->hasRole('super admin') ? 'super-admin.library.index' : (Auth::user()->hasRole('institute manager') ? 'manager.library.index' : 'teacher.library.index'))->with('success', 'Library item created successfully.');
    }

    /**
     * Show the form for editing the specified library resource.
     *
     * @param  \App\Models\Library  $library
     * @return \Illuminate\View\View
     */
    public function edit(Library $library)
    {
        if (Auth::user()->hasRole('super admin')) {
        } elseif (Auth::user()->hasRole('institute manager')) {
            if ($library->institute_id !== Auth::user()->institute_id && $library->institute_id !== null) {
                abort(403, 'Unauthorized action.');
            }
        } elseif (Auth::user()->hasRole('teacher')) {
            if ($library->user_id !== Auth::id()) {
                abort(403, 'Unauthorized action.');
            }
        } else{
            abort(403, 'Unauthorized action.');
        }

        $categories = Category::all();
        return view('libraries.edit', compact('library', 'categories'));
    }

    /**
     * Update the specified library resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Library  $library
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Library $library)
    {
        if (Auth::user()->hasRole('super admin')) {
            // No additional check needed for super admin
        } elseif (Auth::user()->hasRole('institute manager')) {
            if ($library->institute_id !== Auth::user()->institute_id && $library->institute_id !== null) {
                abort(403, 'Unauthorized action.');
            }
        } elseif (Auth::user()->hasRole('teacher')) {
            if ($library->user_id !== Auth::id()) {
                abort(403, 'Unauthorized action.');
            }
        } else {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'author' => 'nullable|string|max:255',
            'isbn' => 'nullable|string|max:255',
            'file' => 'nullable|file|mimes:pdf,mp3,mp4,doc,docx,ppt,pptx,xls,xlsx|max:102400',
        ]);

        if ($request->hasFile('file')) {
            if ($library->file) {
                Storage::delete($library->file->path);
                $library->file->delete();
            }

            $file = $request->file('file');
            $path = $file->store('public/library_files');

            $uploadedFile = File::create([
                'original_name' => $file->getClientOriginalName(),
                'path' => $path,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);
            $library->file_id = $uploadedFile->id;
        }

        $library->update([
            'name' => $request->name,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'author' => $request->author,
            'isbn' => $request->isbn,
            'is_visible' => $request->has('is_visible'),
        ]);

        return redirect()->route(Auth::user()->hasRole('super admin') ? 'super-admin.library.index' : (Auth::user()->hasRole('institute manager') ? 'manager.library.index' : 'teacher.library.index'))->with('success', 'Library item updated successfully.');
    }

    /**
     * Remove the specified library resource from storage.
     *
     * @param  \App\Models\Library  $library
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Library $library)
    {
        if (Auth::user()->hasRole('super admin')) {
        } elseif (Auth::user()->hasRole('institute manager')) {
            if ($library->institute_id !== Auth::user()->institute_id && $library->institute_id !== null) {
                abort(403, 'Unauthorized action.');
            }
        } elseif (Auth::user()->hasRole('teacher')) {
            if ($library->user_id !== Auth::id()) {
                abort(403, 'Unauthorized action.');
            }
        } else {
            abort(403, 'Unauthorized action.');
        }

        if ($library->file) {
            Storage::delete($library->file->path);
            $library->file->delete();
        }
        $library->delete();

        return redirect()->route(Auth::user()->hasRole('super admin') ? 'super-admin.library.index' : (Auth::user()->hasRole('institute manager') ? 'manager.library.index' : 'teacher.library.index'))->with('success', 'Library item deleted successfully.');
    }

    /**
     * Toggle the visibility of the specified library resource.
     * This action is typically for Super Admin and Institute Manager.
     *
     * @param  \App\Models\Library  $library
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleVisibility(Library $library)
    {
        if (!Auth::user()->hasAnyRole(['super admin', 'institute manager'])) {
            abort(403, 'Unauthorized action.');
        }

        if (Auth::user()->hasRole('institute manager') && $library->institute_id !== Auth::user()->institute_id && $library->institute_id !== null) {
            abort(403, 'Unauthorized action.');
        }

        $library->is_visible = !$library->is_visible;
        $library->save();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'is_visible' => $library->is_visible]);
        }

        return back()->with('success', 'Visibility updated successfully.');
    }
}
