<?php

namespace App\Http\Controllers\Api;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class ProductController extends Controller
{

    function __construct()
    {

        $this->middleware('permission:Products_List', ['only' => ['index','show']]);
        $this->middleware('permission:Add_Product', ['only' => ['create','store']]);
        $this->middleware('permission:Edit_Product', ['only' => ['edit','update']]);
        $this->middleware('permission:Delete_Product', ['only' => ['delete','deleteAll','truncate']]);


    }
    /**
     * Display a listing of the resource.
     */

    public function index($page,$limit)
    {   $this->$page=$page;
        $this->$limit=$limit;
        $skip=($page-1)*$limit;
        $products = DB::table('products')->skip($skip)->limit($limit)->get();
        return response()->json([
            'status'=>true,
            'num of products '=>$products->count(),
            'products'=>$products

        ],200);
    }


    /**
     * Get Category Of Specified Product.
     */
    public function GetCategory($id)
    {
        $product = Product::find($id);
        return response()->json([
            'status' => true,
            'category' =>$product->category
        ],200);


    }


    /**
     * Get Invocies Of specified Product.
     */
    public function GetInvoices($id)
    {
         $product= Product::findorfail($id);
        if($product->count()==1){
            $invoices=$product->invoices;
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

    /**
     * Display the specified Product.
     */
    public function show( $id)
    {
        $product = DB::table('products')->where('id', $id)->get();
        if($product->count()==1){
            return response()->json([
                'status'=>true,
                'prodect'=>$product
            ],200);
        }
        else{
            return response()->json([
                'status'=>false,
                'product'=>'this product not exists'

            ],200);
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
            'name' => 'required|string|unique:products|between:2,100',
            'description' => 'required|string',
            'category_id'=>'required'
        );
        $validator = Validator::make($input,$rules);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }


        $product = Product::where('name', $input['name'])->first();

        if($product){
            return response()->json([
                'status'=>false,
                'message'=>'This product already exists',
            ],201);

        }
        else{
            $product = Product::create($input);
            return response()->json([
                'status'=>true,
                'message'=>true,
                'category'=>$product
            ],201);

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
            'name' => 'required|string|unique:products|between:2,100',
            'description' => 'required|string',
            'category_id'=>'required'
        );
        $validator = Validator::make($input,$rules);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }


        $product=Product::where('id', $id)->first();
        if($product){
            $product->update($input);
            return response()->json([
                'status'=>true,
                'product'=>$product
            ],200);
        }
        else{
            return response()->json([
                'status'=>false,
                'product'=>'this product not exists'

            ],404);
        }




    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(string $id)
    {
        $product=Product::where('id', $id)->first();
        if($product){
            $product=$product->delete();
            return response()->json([
                'status'=>true,
                'message'=>true,
                'category'=>$product

            ],201);
        }
        else{
            return response()->json([
                'status'=>false,
                'message'=>'This product is not exists',
            ],404);

        }
    }

    /**
     * Delete All products .
     */
    public function deleteAll()
    {
        //$category=Category::delete();
        $products = Product::query()->delete();
        return response()->json([
            'status'=>true,
            'message'=>true,
            'prodects'=>$products
        ],201);
    }

     /**
     * truncate All products .
     */
    public function truncate()
    {
        $products=Product::truncate();
        return response()->json([
            'status'=>true,
            'message'=>true,
            'prodects'=>$products
        ],201);
    }
}
