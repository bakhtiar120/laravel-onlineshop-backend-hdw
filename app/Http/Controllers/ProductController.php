<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    public function index(Request $request) {
        $products = Product::paginate(5);
        return view('pages.product.index',compact('products'));
    }

    public function create() {
        $categories = Category::get();
        return view('pages.product.create',compact('categories'));
    }

    public function store(Request $request) {
        $data = $request->all();
        $file = $request->file('image');
        $filename = time() . '_' . $file->getClientOriginalName();

        // File extension
        $extension = $file->getClientOriginalExtension();

        // File upload location
        $location = 'upload';

        // Upload file
        $file->move($location, $filename);

        // File path
        $filepath = url('upload/' . $filename);
        $data['image'] = $filename;
        Product::create($data);
        return redirect()->route('product.index');
    }

    public function show($id) {

    }

    public function edit($id) {
        $product=Product::findOrFail($id);
        $categories=Category::get();
        return view('pages.product.edit',compact('product','categories'));
    }

    public function update(Request $request,$id) {
        $data = $request->all();
        $product = Product::findOrFail($id);
        $file = $request->file('image');
        if($file) {
            $filename = time() . '_' . $file->getClientOriginalName();

        // File extension
        $extension = $file->getClientOriginalExtension();

        // File upload location
        $location = 'upload';

        // Upload file
        $file->move($location, $filename);

        // File path
        $filepath = url('upload/' . $filename);
        $path_file = public_path()."/upload/".$product->image;
        $delete_file=File::delete($path_file);
        $data['image'] = $filename;
        } else {
            $data['image'] = $product->image;
        }

        $product->update($data);
        return redirect()->route('product.index');
    }

    public function destroy($id) {
        $product = Product::findOrFail($id);
        $path_file = public_path()."/upload/".$product->image;
        $delete_file=File::delete($path_file);
        $product->delete();
        return redirect()->route('product.index');

    }
}
