<?php

namespace App\Http\Controllers\Api;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class CategoryController extends Controller
{

    function __construct()
    {

        $this->middleware('permission:Categories_List', ['only' => ['index','show']]);
        $this->middleware('permission:Add_Category', ['only' => ['create','store']]);
        $this->middleware('permission:Edit_Category', ['only' => ['edit','update']]);
        $this->middleware('permission:Delete_Category', ['only' => ['delete','deleteAll','truncate']]);


    }

    /*
     * Get All Categories.
     */
    public function index($page,$limit)
    {   $this->$page=$page;
        $this->$limit=$limit;
        $skip=($page-1)*$limit;
        $categories = DB::table('categories')->skip($skip)->limit($limit)->get();
            return response()->json([
                'status'=>true,
                'num of categories '=>$categories->count(),
                'categories'=>$categories

            ],200);
    }
     /**
     * Get Invocies Of specified Category.
     */
    public function GetInvoices($id)
    {
         $category= Category::findorfail($id);
        if($category->count()==1){
            $invoices=$category->invoices;
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
     * Get Products Of specified Category.
     */
    public function GetProducts($id)
    {
        $category= Category::findorfail($id);
        if($category->count()==1){
            $products=$category->products;
            return response()->json([
                'status' => true,
                'num of prodects' => count($products),
                'prodects' => $products->map(function ($product) {
                    return $product;
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
     * Get User Of Specified Invoice.
     */
    public function GetUser($id)
    {
        $category = Category::findorfail($id);
        if($category->count()==1){
            return response()->json([
                'status' => true,
                'User' =>$category->user
            ],200);
        }
        else{
            return response()->json([
                'status' => false,
                'category' =>'this category not exists'
            ],200);
        }


    }

     /**
     * Display the specified Invoice.
     */
    public function show( $id)
    {
        $category = DB::table('categories')->where('id', $id)->get();
        if($category->count()==1){
            return response()->json([
                'status'=>true,
                'invoice'=>$category
            ],200);
        }
        else{
            return response()->json([
                'status'=>false,
                'category'=>'this category not exists'

            ],200);
        }

    }

    /**
     * Create Category.
     */
    public function create()
    {
        //
    }

    /**
     * Store Category.
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'name' => 'required|string|unique:categories|between:2,100',
            'description' => 'required|string',
        );
        $validator = Validator::make($input,$rules);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }


            $category = Category::create(array_merge($input, ['user_id' => Auth::user()->id]));

            return response()->json([
                'status'=>true,
                'message'=>true,
                'category'=>$category
            ],201);
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
            'name' => 'required|string|unique:categories|between:2,100',
            'description' => 'required|string',
        );
        $validator = Validator::make($input,$rules);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $category=Category::where('id', $id)->first();
        if($category){
            $category->update(array_merge($input, ['user_id' => Auth::user()->id]));
            return response()->json([
                'status'=>true,
                'invoice'=>$category
            ],200);
        }
        else{
            return response()->json([
                'status'=>false,
                'category'=>'this category not exists'

            ],200);
        }







    }

    /**
     * Remove the specified category from storage.
     */
    public function delete(string $id)
    {
        $category=Category::where('id', $id)->first();
        if($category){
            $category->delete();
            return response()->json([
                'status'=>true,
                'message'=>true,
                'category'=>$category
            ],201);
        }
        else{
            return response()->json([
                'status'=>false,
                'message'=>'This category is not exists',
            ],201);
        }


    }

    /**
     * Remove all category from storage.
     */
    public function deleteAll()
    {
        $categories = Category::query()->delete();
        return response()->json([
            'status'=>true,
            'message'=>true,
            'categories'=>$categories
        ],201);
    }

     /**
     * Truncate all category with ID to zerofrom storage.
     */
    public function truncate()
    {
        $categories=Category::truncate();
        return response()->json([
            'status'=>true,
            'message'=>true,
            'categories'=>$categories
        ],201);
    }




}
