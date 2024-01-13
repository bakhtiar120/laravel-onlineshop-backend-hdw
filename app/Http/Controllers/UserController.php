<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //
    public function index(Request $request) {
        $users = DB::table('users')
        ->when($request->search,function ($query) use ($request) {
            return $query->where('name','like',"%{$request->search}%");
        })
        ->paginate(5);
        return view('pages.user.index',compact('users'));
    }

    public function create() {
        return view('pages.user.create');
    }

    public function store(Request $request) {
        $data = $request->all();
        $data['password']= Hash::make($request->input('password'));
        User::create($data);
        return redirect()->route('user.index');
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
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('user.index');

    }
}
