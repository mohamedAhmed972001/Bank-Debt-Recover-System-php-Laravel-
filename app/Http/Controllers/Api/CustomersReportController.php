<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Invoice;
use Illuminate\Http\Request;

class CustomersReportController extends Controller
{
    function __construct()
    {

        $this->middleware('permission:Invoice_List', ['only' => ['index']]);//for Admin
        $this->middleware('permission:Categories_List', ['only' => ['Search_customers']]);//for Admin



    }
    /**
     * Display a listing of the resource.
     */
    public function index(){

        $categories = Category::all();
        return response()->json([
            'status'=>true,
            'message'=>true,
            'categories'=>$categories
        ],201);


    }


    public function Search_customers(Request $request)
    {
        $input = $request->all();


// في حالة البحث بدون التاريخ

        if ($input['category_id'] && $input['product_id'] && $input['start_at'] =='' &&  $input['end_at']=='') {


            $invoices = Invoice::select('*')->where('category_id','=',$request->category_id)->where('product_id','=',$request->product_id)->get();
            $categories = Category::all();
            return response()->json([
                'status'=>true,
                'message'=>true,
                'invoices'=>$invoices,
                'categories'=>$categories

            ],201);


        }


        // في حالة البحث بتاريخ

        else {

            $start_at = date($request->start_at);
            $end_at = date($request->end_at);

            $invoices = Invoice::whereBetween('invoce_date',[$start_at,$end_at])->where('category_id','=',$input['category_id'])->where('product_id','=',$input['product_id'])->get();
            $categories = Category::all();
            return response()->json([
                'status'=>true,
                'message'=>true,
                'invoices'=>$invoices,
                'categories'=>$categories

            ],201);


        }



    }
}
