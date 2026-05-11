<?php

namespace App\Http\Controllers\apps\SaaS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;

class LanguageController extends Controller
{
    /**
     * List all languages.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Language::query();

            // Date Filter
            if ($request->has('start_date') && $request->start_date && $request->has('end_date') && $request->end_date) {
                $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
            }

            $languages = $query->latest()->get();
            return response()->json(['data' => $languages]);
        }

        return view('content.apps.saas.languages');
    }

    /**
     * Store a new language.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'code' => 'required|string|max:5|unique:languages'
        ]);

        $language = Language::create($request->all());

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Language added successfully.', 'data' => $language]);
        }

        return redirect()->back()->with('success', 'Language added successfully.');
    }

    /**
     * Update language.
     */
    public function update(Request $request, Language $language)
    {
        $request->validate([
            'name' => 'required|string',
            'code' => 'required|string|max:5|unique:languages,code,' . $language->id
        ]);

        $language->update($request->all());

        return response()->json(['success' => true, 'message' => 'Language updated successfully.']);
    }

    /**
     * Delete language.
     */
    public function destroy(Language $language)
    {
        $language->delete();
        return response()->json(['success' => true, 'message' => 'Language deleted successfully.']);
    }

    /**
     * Toggle language status.
     */
    public function toggle(Language $language)
    {
        $language->update(['is_active' => !$language->is_active]);
        
        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Status updated.']);
        }

        return redirect()->back()->with('success', 'Language status updated.');
    }
}
