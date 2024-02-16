<?php

namespace App\Http\Controllers\Api;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use App\Models\InvoiceAttachment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\UploadImageTrait;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class InvoiceAttachmentController extends Controller
{


        function __construct()
        {

            $this->middleware('permission:View_Attachment', ['only' => ['show']]);//for Admin
            $this->middleware('permission:Add_Attachment', ['only' => ['create','store']]);// for user
            $this->middleware('permission:Download_Attachment', ['only' => ['download']]);// for user
            $this->middleware('permission:Delete_Attachment', ['only' => ['destroy']]);//for Admin


        }



    use UploadImageTrait;
    /**
     * Display a listing of the resource.
     */
    public function index($page,$limit)
     {   $this->$page=$page;
         $this->$limit=$limit;
         $skip=($page-1)*$limit;
         $invoice_attachments = DB::table('invoice_attachments')->skip($skip)->limit($limit)->get();
         return response()->json([
             'status'=>true,
             'num of products '=>$invoice_attachments->count(),
             'invoice'=>$invoice_attachments

         ],200);
     }


    /**
     * Get Destroy Invoice Attachment.
     */

    public function GetDestroyInvoiceAttachment()
    {
        $invoice_attachment = InvoiceAttachment::onlyTrashed()->get();
        return response()->json([
            'status'=>true,
            'num of products '=>$invoice_attachment->count(),
            'invoice'=>$invoice_attachment

        ],200);
    }

    /**
     * Restore Destroy Invoice Attachment.
     */
    public function RestoreDestroyInvoiceAttachment($id)
    {
        $invoice_attachment = InvoiceAttachment::withTrashed()->where('id', $id)->restore();
        return response()->json([
            'status'=>true,
            'invoice'=>$invoice_attachment

        ],200);
    }



    /**
     * Get Invocies Of specified InvoiceAttachment.
     */
     public function GetInvoice($id)
    {
         $invoice_attachment= InvoiceAttachment::findorfail($id);
        if($invoice_attachment->count()==1){
            $invoice=$invoice_attachment->invoice;
            return response()->json([
                'status' => true,
                'invoices' => $invoice

            ], 200);
        }
        else{
            return response()->json([
                'status'=>false,
                'message'=>'This category is not exists',
            ],404);

        }
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $input = $request->all();
        $rules = array(
            'invoice_id' => 'required|unique:invoice_attachments',
            'file_path'=> 'required|unique:invoice_attachments|extensions:jpg,png,pdf,jpeg',
        );
        $validator = Validator::make($input,$rules);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $invoice=Invoice::findorfail($input['invoice_id']);
                $path= $this->uploadImage($request,'Attachments/'.$invoice->invoice_number);
                // Create the invoice_attachment record
                $invoice_attachment = InvoiceAttachment::create([
                    'file_path' => $path,
                    'invoice_id' => $input['invoice_id'],

                    ]);
                    return response()->json([
                    'status'=>true,
                    'message'=>true,
                    'category'=>$invoice_attachment
                ],201);

        // Handle attachments

    }



    /**
     * Display the specified resource.
     */
    public function show( $id)
    {

        $invoice_attachment = InvoiceAttachment::where('id', $id)->first();
        if($invoice_attachment){
            return response()->json([
                'status'=>true,
                'invoice'=>$invoice_attachment
            ],200);
        }
        else{
            return response()->json([
                'status'=>false,
                'invoice_attachment'=>'this invoice_attachment not exists'

            ],404);
        }

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $input = $request->all();
        $rules = array(
            'invoice_id' => 'required|unique:invoices|string',
            'file_path'=> 'required|unique:invoice_attachments|extensions:jpg,png,pdf,jpeg',
        );
        $validator = Validator::make($input,$rules);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $invoice_attachment=InvoiceAttachment::findorfail($id);

        if($invoice_attachment->count()==1){
            $invoice=Invoice::findorfail($input['invoice_id']);
            $path= $this->uploadImage($request,'Attachments/'.$invoice->invoice_number);
            $invoice_attachment->update([
                'file_path' => $path,
                'invoice_id' => $input['invoice_id'],
                ]);
                return response()->json([
                'status'=>true,
                'message'=>true,
                'invoice_attachment'=>$invoice_attachment
            ],201);
        }
        else{
            return response()->json([
                'status'=>false,
                'message'=>'This invoice_attachment is not exists',
            ],404);
        }
    }


    /**
     * Remove the specified Invoice To Archive  .
     */
    public function destroy(string $id)
    {
        $invoice_attachment=InvoiceAttachment::findorfail($id);
        if($invoice_attachment->count()==1){
            $invoice_attachment=$invoice_attachment->delete();
            return response()->json([
                'status'=>true,
                'message'=>true,
                'invoice'=>$invoice_attachment
            ],201);
        }
        else{
            return response()->json([
                'status'=>false,
                'message'=>'This invoice is not exists',
            ],404);

        }
    }

    /**
     * Delete All Invoices To Archive .
     */
    public function destroyAll()
    {

        $invoice_attachment = InvoiceAttachment::query()->delete();;
        return response()->json([
            'status'=>true,
            'message'=>true,
            'invoices'=>$invoice_attachment
        ],201);
    }

    /**
     * Delete specified Invoice Attachment From Archive .
     */
    public function delete(string $id)
    {
        $invoice_attachment=InvoiceAttachment::findorfail($id);
        if($invoice_attachment){
            $invoice_attachment=$invoice_attachment->delete();
            return response()->json([
                'status'=>true,
                'message'=>true,
                'invoice_attachment'=>$invoice_attachment
            ],201);
        }
        else{
            return response()->json([
                'status'=>false,
                'message'=>'This invoice_attachment is not exists',
            ],201);

        }
    }

    /**
     * Delete All Invoice Attachments From Archive .
     */
    public function deleteAll()
    {
        //$category=Category::delete();
        $invoice_attachments = DB::table('invoice_attachments')->delete();
        return response()->json([
            'status'=>true,
            'message'=>true,
            'invoice_attachments'=>$invoice_attachments
        ],201);
    }

    /**
     * truncate All products .
     */
    public function truncate()
    {
        $invoice_attachments=DB::table('invoice_attachments')->truncate();
        return response()->json([
            'status'=>true,
            'message'=>true,
            'invoice_attachments'=>$invoice_attachments
        ],201);
    }

    /**
     * Download file  .
     */
    public function download($id)
    {
        $invoice_attachment = InvoiceAttachment::findOrFail($id);

        // Check if the file exists

            $response = $this->downloadImage($invoice_attachment);

            // Add custom headers or data here
//            $response->headers->set('status', true);
//            $response->headers->set('message', true);
//            $response->headers->set('invoice_attachments', json_encode($invoice_attachment));

            return $response;

    }



}

