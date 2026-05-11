<?php

namespace Modules\General\App\Http\Controllers\DocumentTemplate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\General\App\Models\DocumentTemplate;
use Modules\General\App\Models\Template;
use App\Helpers\HS\Reply;
use Modules\General\App\Helpers\CompanyHelper;

class DocumentTemplateController extends Controller
{
    /**
     * Display a listing of the document templates.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $templates = DocumentTemplate::with('template')->get();
        $templateDesigns = Template::all();

        // Add formatted type labels to each template
        $templates->each(function ($template) {
            $template->formatted_type = $this->formatTypeLabel($template->type);
        });

        return view('general::document_templates.index', compact('templates', 'templateDesigns'));
    }

    /**
     * Format the document type into a human-readable label with first letter capitalized.
     *
     * @param string $type
     * @return string
     */
    private function formatTypeLabel(string $type): string
    {
        // Replace underscores with spaces and convert to lowercase
        $label = str_replace('_', ' ', strtolower($type));
        // Capitalize the first letter of the entire string
        return ucfirst($label);
    }

    /**
     * Store a newly created document template in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string|unique:general_document_templates,type',
            'template_id' => 'nullable|exists:templates,id',
            'header_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'footer_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'is_active' => 'nullable',
        ]);

        try {
            $companyData = CompanyHelper::getCompanyAndBranch();

            $template = new DocumentTemplate();
            $template->type = $request->type;  // Store machine-friendly value (e.g., 'credit_note')

            // Format the name properly
            $formattedName = $this->formatTypeLabel($request->type);
            $template->name = $formattedName;  // Store human-readable value (e.g., 'Credit note')

            $template->template_id = $request->template_id;
            $template->is_active = $request->has('is_active') ? 1 : 0;

            // Attach company & branch IDs
            $template->company_id = $companyData['company_id'];
            $template->branch_id  = $companyData['branch_id'];

            $template->save();
            $uuid = $template->uuid;

            // Handle header image
            if ($request->hasFile('header_image')) {
                $file = $request->file('header_image');
                $filename = $uuid . '_header.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('document_templates/headers', $filename, 'public');
                $template->header_image = str_replace('\\', '/', $path);

                // Get image height
                $imageSize = getimagesize($file->getRealPath());
                $template->header_height = $imageSize[1];
            }

            // Handle footer image
            if ($request->hasFile('footer_image')) {
                $file = $request->file('footer_image');
                $filename = $uuid . '_footer.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('document_templates/footers', $filename, 'public');
                $template->footer_image = str_replace('\\', '/', $path);

                // Get image height
                $imageSize = getimagesize($file->getRealPath());
                $template->footer_height = $imageSize[1];
            }

            // Ensure name is still properly formatted before final save
            $template->name = $formattedName;
            $template->save();

            return redirect()->route('general.templates.index')
                ->with('success', "Template '{$formattedName}' created successfully!");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to save document template.')
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified document template.
     *
     * @param  string  $uuid
     * @return \Illuminate\View\View
     */
    public function edit($uuid)
    {
        $template = DocumentTemplate::where('uuid', $uuid)->firstOrFail();
        $templateDesigns = Template::all();
        return view('general::document_templates.edit', compact('template', 'templateDesigns'));
    }

    /**
     * Update the specified document template in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $uuid
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $uuid)
    {
        $request->validate([
            'type' => 'required|string|unique:general_document_templates,type,' . $uuid . ',uuid',
            'template_id' => 'nullable|exists:templates,id',
            'header_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'footer_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'is_active' => 'nullable',
        ]);

        try {
            $template = DocumentTemplate::where('uuid', $uuid)->firstOrFail();

            $template->type = $request->type;  // Store machine-friendly value (e.g., 'credit_note')

            // Format the name properly
            $formattedName = $this->formatTypeLabel($request->type);
            $template->name = $formattedName;  // Store human-readable value (e.g., 'Credit note')

            $template->template_id = $request->template_id;
            $template->is_active = $request->has('is_active') ? 1 : 0;

            // Attach company & branch IDs
            $companyData = CompanyHelper::getCompanyAndBranch();
            $template->company_id = $companyData['company_id'];
            $template->branch_id  = $companyData['branch_id'];

            // Handle header image
            if ($request->hasFile('header_image')) {
                // Delete old header image if exists
                if ($template->header_image && Storage::disk('public')->exists($template->header_image)) {
                    Storage::disk('public')->delete($template->header_image);
                }

                $file = $request->file('header_image');
                $filename = $uuid . '_header.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('document_templates/headers', $filename, 'public');
                $template->header_image = str_replace('\\', '/', $path);

                // Get image height
                $imageSize = getimagesize($file->getRealPath());
                $template->header_height = $imageSize[1];
            }

            // Handle footer image
            if ($request->hasFile('footer_image')) {
                // Delete old footer image if exists
                if ($template->footer_image && Storage::disk('public')->exists($template->footer_image)) {
                    Storage::disk('public')->delete($template->footer_image);
                }

                $file = $request->file('footer_image');
                $filename = $uuid . '_footer.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('document_templates/footers', $filename, 'public');
                $template->footer_image = str_replace('\\', '/', $path);

                // Get image height
                $imageSize = getimagesize($file->getRealPath());
                $template->footer_height = $imageSize[1];
            }

            // Ensure name is still properly formatted before final save
            $template->name = $formattedName;
            $template->save();

            return redirect()->route('general.templates.index')
                ->with('success', 'Document template updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update document template.')
                ->withInput();
        }
    }

    /**
     * Remove the specified document template from storage.
     *
     * @param  string  $uuid
     * @return \App\Helpers\HS\Reply
     */
    public function destroy($uuid)
    {
        try {
            $template = DocumentTemplate::where('uuid', $uuid)->firstOrFail();

            // Delete header image from storage if exists
            if ($template->header_image && Storage::disk('public')->exists($template->header_image)) {
                Storage::disk('public')->delete($template->header_image);
            }

            // Delete footer image from storage if exists
            if ($template->footer_image && Storage::disk('public')->exists($template->footer_image)) {
                Storage::disk('public')->delete($template->footer_image);
            }

            // Delete the database record
            $template->delete();

            return Reply::success('Document template deleted successfully!');
        } catch (\Exception $e) {
            return Reply::error('Failed to delete document template.');
        }
    }

    /**
     * Toggle the status of the specified template.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleStatus(Request $request, $uuid)
    {
        try {
            $template = DocumentTemplate::where('uuid', $uuid)->firstOrFail();

            // Toggle the is_active field
            $template->is_active = !$template->is_active;
            $template->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully.',
                'is_active' => $template->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update template status.'
            ], 500);
        }
    }
}
