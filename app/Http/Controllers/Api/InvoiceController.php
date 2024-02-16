<?php

namespace App\Http\Controllers\Api;
use App\Exports\InvoicessExport;
use App\Models\Invoice;
use App\Models\InvoiceDetails;
use App\Models\User;
use App\Notifications\Invoice_Addition_Updating;
use App\Notifications\InvoicedatabaseAddition;
use App\Notifications\UpdatingInvoiceStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class InvoiceController extends Controller
{

    function __construct()
    {

        $this->middleware('permission:Invoice_List', ['only' => ['index','show','MarkAsRead_all','unreadNotifications_count','unreadNotifications']]);
        $this->middleware('permission:Paid_Invoices', ['only' => ['Invoice_Paid']]);
        $this->middleware('permission:Partially_Paid_Invoices', ['only' => ['Invoice_Partial']]);
        $this->middleware('permission:Unpaid_Invoices', ['only' => ['Invoice_unPaid']]);
        $this->middleware('permission:Change_Payment_Status', ['only' => ['status_update']]);
        $this->middleware('permission:Export_to_Excel', ['only' => ['export']]);
        $this->middleware('permission:Add_Invoice', ['only' => ['create','store']]);
        $this->middleware('permission:Edit_Invoice', ['only' => ['edit','update']]);
        $this->middleware('permission:Delete_Invoice', ['only' => ['destroyAll','destroy']]);


    }
    /**
     * Display a listing of the resource.
     */

     public function index($page,$limit)
     {   $this->$page=$page;
         $this->$limit=$limit;
         $skip=($page-1)*$limit;
         $invoice = DB::table('invoices')->skip($skip)->limit($limit)->get();
         return response()->json([
             'status'=>true,
             'num of products '=>$invoice->count(),
             'invoice'=>$invoice

         ],200);
     }

    public function Invoice_Paid()
    {
        $invoices = Invoice::where('value_status', 1)->get();
        return response()->json([
            'status'=>true,
            'num of products '=>$invoices->count(),
            'invoice'=>$invoices

        ],200);
    }

    public function Invoice_unPaid()
    {
        $invoices = Invoice::where('value_status',2)->get();
        return response()->json([
            'status'=>true,
            'num of products '=>$invoices->count(),
            'invoice'=>$invoices

        ],200);
    }

    public function Invoice_Partial()
    {
        $invoices = Invoice::where('value_status',3)->get();
        return response()->json([
            'status'=>true,
            'num of products '=>$invoices->count(),
            'invoice'=>$invoices

        ],200);
    }

    /**
     * Get Destroy Invoice.
     */

    public function GetDestroyInvoice()
    {
        $invoice = Invoice::onlyTrashed()->get();
        return response()->json([
            'status'=>true,
            'num of products '=>$invoice->count(),
            'invoice'=>$invoice

        ],200);
    }

    /**
     * Restore Destroy Invoice.
     */
    public function RestoreDestroyInvoice($id)
    {
        $invoice = Invoice::withTrashed()->where('id', $id)->restore();
        return response()->json([
            'status'=>true,
            'invoice'=>$invoice

        ],200);
    }




     /**
     * Get Category Of Specified Invoice.
     */
     public function GetCategory($id)
     {
         $invoice = Invoice::find($id);
         if($invoice){
             return response()->json([
                 'status' => true,
                 'category' =>$invoice->category
             ],200);

         }
         else{
             return response()->json([
                 'status' => false,
                 'category' =>'this invoice not exists'
             ],200);
         }


     }

     /**
     * Get Product Of Specified Invoice.
     */
     public function GetProduct($id)
     {
         $invoice = Invoice::find($id);
         if($invoice){
             return response()->json([
                 'status' => true,
                 'product' =>$invoice->product
             ],200);

         }
         else{
             return response()->json([
                 'status' => false,
                 'product' =>'this product not exists'
             ],200);
         }


     }

     /**
     * Get User Of Specified Invoice.
     */
    public function GetUser($id)
    {
        $invoice = Invoice::find($id);
        if($invoice){
            return response()->json([
                'status' => true,
                'category' =>$invoice->user
            ],200);
        }
        else{
            return response()->json([
                'status' => false,
                'product' =>'this product not exists'
            ],200);
        }


    }

    /**
     * Get Attachments Of specified Invoice.
     */
    public function GetAttachments($id)
    {
        $invoice= Invoice::findorfail($id);
        if($invoice->count()==1){
            $invoices=$invoice->attachments;
            return response()->json([
                'status' => true,
                'num of prodects' => count($invoices),
                'invoices' => $invoices->map(function ($invoice) {
                    return $invoice;
                }),
            ], 200);
        }
        else{
            return response()->json([
                'status'=>false,
                'message'=>'This category is not exists',
            ],201);

        }
    }

    public function GetInvoice_details($id)
    {
        $invoice= Invoice::findorfail($id);
        if($invoice){
            $details=$invoice->details;
            return response()->json([
                'status' => true,
                'num of prodects' => count($details),
                'invoices' => $details->map(function ($detail) {
                    return $detail;
                }),
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
        // Validate the form data
        $input = $request->all();
        $rules = array(
            'invoice_number' => 'required|unique:invoices|string',
            'invoice_date'=> 'required|date',
            'due_date'=>'required|date',
            'discount'=>'required',
            'rate_vat'=>'required',
            'amount_collection'=>'required',
            'amout_commission'=>'required',
            'status'=>'required|string|max:15',
            'value_status'=>'required|integer',
            'note'=>'required|string',
            'product_id'=>'required',
            'category_id'=>'required',
        );
        $validator = Validator::make($input,$rules);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        // Create the invoice
        $invoice = Invoice::where('invoice_number', $input['invoice_number'])->first();
            if($invoice){
                return response()->json([
                    'status'=>false,
                    'message'=>'This invoice is  exists',
                ],201);
            }
            else{
                $invoice=new Invoice();
                $value = $invoice->calculateValue($input['amout_commission'],  $input['discount'],  $input['rate_vat']);
                $total = $invoice->calculateTotal($input['amout_commission'], $input['discount'], $input['rate_vat']);
                $invoice = Invoice::create(array_merge($input, [
                    'user_id' => Auth::user()->id,
                    'invoice_date' => Carbon::parse($input['invoice_date'])->format('Y-m-d H:i:s'),
                    'due_date' => Carbon::parse($input['due_date'])->format('Y-m-d H:i:s'),
                    'value_vat' =>$value,
                    'total' => $total,
                    ]));
                $user=User::find(Auth::user()->id);
                Notification::send($user, new Invoice_Addition_Updating($invoice->id,1));
                Notification::send($user, new InvoicedatabaseAddition($invoice->id,1));
                $invoice_details = InvoiceDetails::create([
                    'status' => $input['status'],
                    'value_status' => $input['value_status'],
                    'payment_date' => date('Y-m-d'),
                    'invoice_id' =>$invoice->id,
                    'user_id' =>  Auth::user()->id
                ]);
                    return response()->json([
                    'status'=>true,
                    'message'=>true,
                    'invoice'=>$invoice,
                        'invoice_details' => $invoice_details

                ],201);
            }
    }


     /* Display the specified Invoice.
     */
    public function show( $id)
    {
        $invoice = DB::table('invoices')->where('id', $id)->get();
        if($invoice->count()==1){
            return response()->json([
                'status'=>true,
                'invoice'=>$invoice
            ],200);
        }
        else{
            return response()->json([
                'status'=>false,
                'invoice'=>'this invoice not exists'

            ],200);
        }

    }


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
            'invoice_number' => 'required|unique:invoices|string',
            'invoice_date'=> 'required|date',
            'due_date'=>'required|date',
            'discount'=>'required',
            'rate_vat'=>'required',
            'amount_collection'=>'required',
            'amout_commission'=>'required',
            'status'=>'required|string|max:15',
            'value_status'=>'required|integer',
            'note'=>'required|string',
            'product_id'=>'required',
            'category_id'=>'required',
        );
        $validator = Validator::make($input,$rules);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $invoice=new Invoice();
        $value = $invoice->calculateValue($input['amout_commission'],  $input['discount'],  $input['rate_vat']);
        $total = $invoice->calculateTotal($input['amout_commission'], $input['discount'], $input['rate_vat']);
        $invoice = Invoice::where('id',$id)->first();

        if($invoice){


            $invoice->update(array_merge($input, [
                'user_id' => Auth::user()->id,
                'invoice_date' => Carbon::parse($input['invoice_date'])->format('Y-m-d H:i:s'),
                'due_date' => Carbon::parse($input['due_date'])->format('Y-m-d H:i:s'),
                'value_vat' =>$value,
                'total' => $total,
            ]));
            $user=User::find(Auth::user()->id);
            Notification::send($user, new Invoice_Addition_Updating($invoice->id,2));
            Notification::send($user, new InvoicedatabaseAddition($invoice->id,2));
            $invoice_details = InvoiceDetails::create([
                'status' => $request->status,
                'value_status' => $request->value_status,
                'payment_date' => date('Y-m-d'),
                'invoice_id' =>$invoice->id,
                'user_id' =>  Auth::user()->id
            ]);
                return response()->json([
                'status'=>true,
                'message'=>true,
                'invoice'=>$invoice,
                    'invoice details' =>$invoice_details
            ],201);
        }
        else{
            return response()->json([
                'status'=>false,
                'message'=>'This invoice is not exists',
            ],201);
        }
    }

    public function status_update(Request $request, string $id){
        $invoice =Invoice::findorfail($id);
        if($request->value_status==1){
            $invoice->update([
                'status' => 'Paid',
                'value_status' => 1,
            ]);
            $user=User::find(Auth::user()->id);
            Notification::send($user,new UpdatingInvoiceStatus($invoice->id,$invoice->status));
            Notification::send($user, new InvoicedatabaseAddition($invoice->id,3));

            $invoice_details = InvoiceDetails::create([
                'status' => 'Paid',
                'value_status' => 1,
                'payment_date' => date('Y-m-d'),
                'invoice_id' =>$id,
                'user_id' =>  Auth::user()->id
            ]);
            return response()->json([
                'status'=>true,
                'message'=>true,
                'invoice'=>$invoice,
                'invoice details' =>$invoice_details
            ],201);
        }
        elseif ($request->value_status==2){
            $invoice->update([
                'status' => 'Not Paid',
                'value_status' => 2
            ]);
            $user=User::first();
            Notification::send($user,new UpdatingInvoiceStatus($invoice->id,$invoice->status,$user->name));
            $invoice_details = InvoiceDetails::create([
                'status' => 'Not Paid',
                'value_status' => 2,
                'payment_date' => date('Y-m-d'),
                'invoice_id' =>$id,
                'user_id' =>  Auth::user()->id
            ]);
            return response()->json([
                'status'=>true,
                'message'=>true,
                'invoice'=>$invoice,
                'invoice details' =>$invoice_details
            ],201);

        }
        elseif ($request->value_status==3){
            $invoice->update([
                'status' => 'Semi-Paid',
                'value_status' => 3
            ]);
            $user=User::first();
            Notification::send($user,new UpdatingInvoiceStatus($invoice->id,$invoice->status,$user->name));            $invoice_details = InvoiceDetails::create([
                'status' => 'Semi-Paid',
                'value_status' => 3,
                'payment_date' => date('Y-m-d'),
                'invoice_id' =>$id,
                'user_id' =>  Auth::user()->id
            ]);
            return response()->json([
                'status'=>true,
                'message'=>true,
                'invoice'=>$invoice,
                'invoice details' =>$invoice_details
            ],201);
        }

    }



    /**
     * Remove the specified Invoice To Archive  .
     */
    public function destroy(string $id)
    {
        $invoice=Invoice::where('id', $id)->first();
        if($invoice){
            $invoice=$invoice->delete();
            return response()->json([
                'status'=>true,
                'message'=>true,
                'invoice'=>$invoice
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

        $invoices = Invoice::query()->delete();;
        return response()->json([
            'status'=>true,
            'message'=>true,
            'invoices'=>$invoices
        ],201);
    }

    /**
     * Delete specified Invoice From Archive .
     */
    public function delete(string $id)
    {
        $invoice=Invoice::withTrashed()->where('id', $id)->first();
        if($invoice){
            $invoice=$invoice->forceDelete();
            return response()->json([
                'status'=>true,
                'message'=>true,
                'invoice'=>$invoice
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
     * Delete All Invoices From Archive .
     */
    public function deleteAll()
    {
        $invoices = Invoice::withTrashed()->get();
        $invoices=$invoices->forceDelete();
        return response()->json([
            'status'=>true,
            'message'=>true,
            'invoices'=>$invoices
        ],201);
    }

     /**
     * truncate All products .
     */
    public function truncate()
    {
        $invoices=Invoice::truncate();
        return response()->json([
            'status'=>true,
            'message'=>true,
            'invoices'=>$invoices
        ],201);
    }

    public function export()
    {
        $exel= Excel::download(new InvoicessExport, 'Invoice.xlsx');

        return $exel;



    }





    public function MarkAsRead_all (Request $request)
    {

        $userUnreadNotification= auth()->user()->unreadNotifications;

        if($userUnreadNotification) {
            $userUnreadNotification->markAsRead();
            return back();
        }


    }


    public function unreadNotifications_count()

    {
        return auth()->user()->unreadNotifications->count();
    }

    public function unreadNotifications()

    {
        foreach (auth()->user()->unreadNotifications as $notification) {

            return $notification->data['title'];

        }
    }

}
