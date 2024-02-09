<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Slogan;
use Illuminate\Http\Request;

class SloganController extends Controller
{
    public function slogan_list(){
        $slogan_lists = Slogan::all();
        return view('Admin.slogans.slogans',compact('slogan_lists'));
    }

    public function add_slogan(){
        return view('Admin.slogans.add-slogans');
    }

    public function save_slogan(Request $request){
        $request->validate(['slogan_name' => 'required']);
        Slogan::create($request->all());
        flash()->addSuccess('Slogan Added Successfully');
        return redirect()->route('slogan_list');
    }

    public function delete_slogan(Request $request){
        Slogan::find($request->id)->delete();
        flash()->addSuccess('Slogan List Deleted Successfully');
        return redirect()->route('slogan_list');
    }
}
