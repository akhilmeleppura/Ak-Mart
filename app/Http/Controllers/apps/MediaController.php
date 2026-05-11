<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    /**
     * Handle Dropzone file upload.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            
            // Store in public/assets/img/ecommerce-images
            $file->move(public_path('assets/img/ecommerce-images'), $filename);

            return response()->json([
                'success' => true,
                'filename' => $filename,
                'path' => asset('assets/img/ecommerce-images/' . $filename)
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Upload failed.'], 400);
    }

    /**
     * Handle file deletion.
     */
    public function delete(Request $request)
    {
        $filename = $request->filename;
        $path = public_path('assets/img/ecommerce-images/' . $filename);

        if (file_exists($path)) {
            unlink($path);
            return response()->json(['success' => true, 'message' => 'File deleted.']);
        }

        return response()->json(['success' => false, 'message' => 'File not found.'], 404);
    }
}
