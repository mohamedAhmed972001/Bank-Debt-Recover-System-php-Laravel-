<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceReportController extends Controller
{
    function __construct()
    {

        $this->middleware('permission:Invoice_List', ['only' => ['Search_invoices']]);//for user



    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        //
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

    public function Search_invoices(Request $request){


        $input = $request->all();


        // في حالة البحث بنوع الفاتورة

        if ($input['rdio'] == 1) {


            // في حالة عدم تحديد تاريخ
            if ($input['value_status'] && $input['end_at'] =='' && $input['start_at'] =='') {

                $invoices = Invoice::where('value_status',$request->value_status)->get();
                $type = $input['value_status'];
                 return response()->json([
                    'status'=>true,
                    'message'=>true,
                    'invoices'=>$invoices
                ],201);
            }

            // في حالة تحديد تاريخ استحقاق
            else {

                $start_at = date($input['start_at']);
                $end_at = date($input['end_at']);
                $type = $input['value_status'];

                $invoices = Invoice::whereBetween('invoice_Date',[$start_at,$end_at])->where('value_status',$input['value_status'])->get();
                return response()->json([
                    'status'=>true,
                    'message'=>true,
                    'invoices'=>$invoices
                ],201);
            }



        }

//====================================================================

// في البحث برقم الفاتورة
        else {

            $invoices = Invoice::select('*')->where('invoice_number','=',$request->invoice_number)->get();
            return response()->json([
                'status'=>true,
                'message'=>true,
                'invoices'=>$invoices
            ],201);
        }



    }
}
