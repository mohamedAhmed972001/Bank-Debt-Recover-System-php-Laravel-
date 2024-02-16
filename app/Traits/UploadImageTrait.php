<?php
namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

trait UploadImageTrait
{
    public function uploadImage(Request $request,$folder_name)
    {
        $image = $request->file('file_path');
        $file_name = $image->getClientOriginalName();
        $path=$image->storeAs($folder_name,$file_name);
        return $path;
    }
    public function downloadImage($invoice_attachment)
    {
        $filePath = Storage::path($invoice_attachment->file_path);
        if (file_exists($filePath)) {
            $response = response()->download($filePath);

            // Add custom headers or data here
//            $response->headers->set('status', true);
//            $response->headers->set('message', true);
//            $response->headers->set('invoice_attachments', json_encode($invoice_attachment));

            return $response;
        }
        else {
            return response()->json([
                'status' => false,
                'message' => 'File is not found',
            ], 404);
        }
    }





}
