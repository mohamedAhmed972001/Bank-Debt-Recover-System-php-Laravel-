<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InvoiceDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceDetailsController extends Controller
{
    function __construct()
    {

        $this->middleware('permission:Invoices', ['only' => ['index','show']]);//for Admin



    }
    /**
     * Display a listing of the resource.
     */
    public function index($page,$limit)
    {
        $this->$page=$page;
        $this->$limit=$limit;
        $skip=($page-1)*$limit;
        $invoice_details = DB::table('invoice_details')->where('invoice_id',1)
            ->orderBy('payment_date')->skip($skip)->limit($limit)->get();
        return response()->json([
            'status'=>true,
            'num of products '=>$invoice_details->count(),
            'invoice'=>$invoice_details

        ],200);
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $invoice_details = InvoiceDetails::where()->first();
        if($invoice_details){
            return response()->json([
                'status'=>true,
                'invoice'=>$invoice_details
            ],200);
        }
        else{
            return response()->json([
                'status'=>false,
                'invoice'=>'this invoice not exists'

            ],200);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
