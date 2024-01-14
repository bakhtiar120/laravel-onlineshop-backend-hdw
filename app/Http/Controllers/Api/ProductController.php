<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $products = Product::with('category')
        ->when($request->category_id, function ($query) use ($request) {
            return $query->where('category_id', $request->category_id);
        })
            ->paginate(10);
        return response()->json([
            'message' => 'Success',
            'data' => $products
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:100',
            'category_id' => 'required',
            'description' => 'required',
            'image' => 'required|mimes:jpeg,png,jpg,gif',
            'price' => 'required|integer',
            'stock' => 'required|integer',
        ]);
        $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
    
            // File extension
            $extension = $file->getClientOriginalExtension();
    
            // File upload location
            $location = 'upload/products';
    
            // Upload file
            $file->move($location, $filename);
    
            // File path
            $validated['image'] = $filename;
            $validated['is_available'] = 1;
        $product = Product::create($validated);


        return response()->json([
            'data' => $product,
            'message' => 'Data Produk Berhasil ditambahkan',
        ],200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // $product = Product::join('categories','categories.id','=','products.category_id')
        // ->where('products.id','=',$id)
        // ->select('products.*','categories.name as category_name')
        $product = Product::with('category')
        ->get();
        return response()->json([
            'data' => $product,
            'message' => 'Data Detail Produk Berhasil diambil',
        ],200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|max:100',
            'category_id' => 'required',
            'description' => 'required',
            'price' => 'required|integer',
            'stock' => 'required|integer',
        ]);
        $product = Product::findOrFail($id);
        
        $file = $request->file('image');
        if($file) {
            $filename = time() . '_' . $file->getClientOriginalName();

            // File extension
            $extension = $file->getClientOriginalExtension();

            // File upload location
            $location = 'upload/categories';

            // Upload file
            $file->move($location, $filename);

            // File path
            $validated['image'] = $filename;
            //delete image lama
            $path_file = public_path()."/upload/categories".$category->image;
            $delete_file=File::delete($path_file);
        } else {
            $validated['image'] = $category->image; 
        }
        $product->update($validated);
        return response()->json([
            'data' => $product,
            'message' => 'Data Produk Berhasil diupdate',
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = product::findOrFail($id);
        $path_file = public_path()."/upload/products".$product->image;
        $delete_file=File::delete($path_file);
        $product->delete();
        return response()->json([
            'message' => 'Data Produk Berhasil dihapus',
        ],200);
    }
}