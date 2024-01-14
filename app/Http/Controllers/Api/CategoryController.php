<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\File;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        return response()->json([
            'message' => 'Success',
            'data' => $categories
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:100',
            'description' => 'required',
            'image' => 'required|mimes:jpeg,png,jpg,gif'
        ]);
        $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
    
            // File extension
            $extension = $file->getClientOriginalExtension();
    
            // File upload location
            $location = 'upload/categories';
    
            // Upload file
            $file->move($location, $filename);
    
            // File path
            $filepath = url('upload/' . $filename);
            $validated['image'] = $filename;
        $category = Category::create($validated);


        return response()->json([
            'data' => $category,
            'message' => 'Data Category Berhasil ditambahkan',
        ],200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $category = Category::findOrFail($id);
        return  response()->json([
            'data' => $category,
            'message' => 'Data Detail Category Berhasil diambil',
        ],200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|max:100',
            'description' => 'required',
        ]);
        $category = Category::findOrFail($id);
        
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
        $category->update($validated);


    return response()->json([
        'data' => $category,
        'message' => 'Data Category Berhasil diupdate',
    ],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);
        $path_file = public_path()."/upload/categories".$category->image;
        $delete_file=File::delete($path_file);
        $category->delete();
        return response()->json([
            'message' => 'Data Category Berhasil dihapus',
        ],200);
    }
}