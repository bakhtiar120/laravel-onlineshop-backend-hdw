<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CategoryController extends Controller
{
    public function index(Request $request) {
        $categories = DB::table('categories')
        ->when($request->search,function ($query) use ($request) {
            return $query->where('name','like',"%{$request->search}%");
        })
        ->paginate(5);
        return view('pages.category.index',compact('categories'));
    }

    public function create() {
        return view('pages.category.create');
    }

    public function store(Request $request) {
        $data = $request->all();
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
        $data['image'] = $filename;
      }
        Category::create($data);
        return redirect()->route('category.index');
    }

    public function show($id) {

    }

    public function edit($id) {
        $user=User::findOrFail($id);
        return view('pages.user.edit',compact('user'));
    }

    public function update(Request $request,$id) {
        $data = $request->all();
        $user = User::findOrFail($id);
        $password_input = $request->input('password');
        if($password_input) {
            $data['password'] = Hash::make($password_input);
        } else {
            $data['password'] = $user->password;
        }
        $user->update($data);
        return redirect()->route('user.index');
    }

    public function destroy($id) {
        $category = Category::findOrFail($id);
        $path_file = public_path()."/upload/categories".$category->image;
        $delete_file=File::delete($path_file);
        $category->delete();
        return redirect()->route('category.index');

    }
}
