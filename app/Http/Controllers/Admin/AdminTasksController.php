<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\TaskCategory;
use App\Models\Admin\TaskSubCategory;
use App\Models\MobileApp\Tasks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AdminTasksController extends Controller
{
    public function task_category_list(){
        $category = TaskCategory::all();
        return view('Admin.tasks.category.task-category',compact('category'));
    }

    public function add_task_category(){
        return view('Admin.tasks.category.add-task-category');
    }

    public function save_task_category(Request $request){
        $request->validate(['task_category_name'=>'required|unique:task_categories,task_category_name']);
        TaskCategory::create([
            'task_category_name'=>$request->task_category_name,
            'task_category_slug'=>Str::slug($request->task_category_name),
            'task_category_status'=>'active'
        ]);
        flash()->addSuccess('Task Category Added');
        return redirect()->route('task_category_list');
    }

    public function edit_task_category($slug){
        $category = TaskCategory::where('task_category_slug',$slug)->first();
        return view('Admin.tasks.category.edit-task-category',compact('category'));
    }

    public function update_task_category(Request $request, $id){
        $request->validate(['task_category_name'=>'required|unique:task_categories,task_category_name,'.$id.'']);
        $category = TaskCategory::find($id);
        $category->update([
            'task_category_name'=>$request->task_category_name,
            'task_category_slug'=>Str::slug($request->task_category_name)
        ]);
        flash()->addSuccess('Task Category Updated Successfully');
        return redirect()->route('task_category_list');
    }

    public function delete_task_category(Request $request){
        TaskCategory::find($request->id)->delete();
        flash()->addSuccess('Task Category Deleted Successfully');
        return redirect()->route('task_category_list');
    }

    // Sub Categories Start

    public function task_subcategory_list(){
        $subcategory = TaskSubCategory::join('task_categories','task_categories.id','task_sub_categories.task_category_name')
        ->select('task_sub_categories.*','task_categories.task_category_name')->get();
        return view('Admin.tasks.subcategory.task-subcategory',compact('subcategory'));
    }

    public function add_task_subcategory(){
        $category = TaskCategory::all();
        return view('Admin.tasks.subcategory.add-task-subcategory',compact('category'));
    }

    public function save_task_subcategory(Request $request){
        $request->validate([
            'task_category_name'=>'required',
            'task_sub_category_name'=>'required|unique:task_categories,task_category_name',
            'task_sub_category_image'=>'required|mimes:png,jpg,webp,jpeg|dimensions:max_width=512,max_height=512',
        ],['task_sub_category_image.dimensions' => 'Image dimensions is height and width is less than 512px.']);
        $TaskImageName = null;
        if($request->hasFile('task_sub_category_image')){
            $TaskImageName = 'Task_Image_'.rand(1111111111,99999999999).".".$request->file('task_sub_category_image')->getClientOriginalExtension();
            $request->file('task_sub_category_image')->move('assets/images/tasks', $TaskImageName);
        }
        TaskSubCategory::create([
            'user_id'=>0,
            'task_category_name'=>$request->task_category_name,
            'task_sub_category_name'=>$request->task_sub_category_name,
            'task_sub_category_slug'=>Str::slug($request->task_sub_category_name),
            'task_sub_category_image'=>$TaskImageName,
            'task_sub_category_status'=>'active'
        ]);
        flash()->addSuccess('Task Sub-Category Added');
        return redirect()->route('task_subcategory_list');
    }

    public function edit_task_subcategory($slug){
        $category = TaskCategory::get();
        $subcategory = TaskSubCategory::join('task_categories','task_categories.id','task_sub_categories.task_category_name')
        ->select('task_sub_categories.*','task_categories.task_category_name','task_categories.task_category_slug')
        ->where('task_sub_category_slug',$slug)->first();
        return view('Admin.tasks.subcategory.edit-task-subcategory',compact('category','subcategory'));
    }

    public function update_task_subcategory(Request $request, $id){
        $request->validate([
            'task_category_name'=>'required',
            'task_sub_category_name'=>'required|unique:task_sub_categories,task_sub_category_name,'.$id.'',
            'task_sub_category_image'=>'mimes:png,jpg,webp,jpeg|dimensions:max_width=512,max_height=512',
        ],['task_sub_category_image.dimensions' => 'Image dimensions is height and width is less than 512px.']);
        $subcategory = TaskSubCategory::find($id);
        $TaskImageName = $subcategory->task_sub_category_image;
        if($request->hasFile('task_sub_category_image')){
            if(File::exists(public_path("assets/images/tasks/".$subcategory->task_sub_category_image))){
                File::delete(public_path("assets/images/tasks/".$subcategory->task_sub_category_image));
            }
            $TaskImageName = 'Task_Image_'.rand(1111111111,99999999999).".".$request->file('task_sub_category_image')->getClientOriginalExtension();
            $request->file('task_sub_category_image')->move('assets/images/tasks', $TaskImageName);
        }
        $subcategory->update([
            'task_category_name'=>$request->task_category_name,
            'task_sub_category_name'=>$request->task_sub_category_name,
            'task_sub_category_slug'=>Str::slug($request->task_sub_category_name),
            'task_sub_category_image'=>$TaskImageName,
        ]);
        flash()->addSuccess('Task Sub-Category Updated Successfully');
        return redirect()->route('task_subcategory_list');
    }

    public function delete_task_subcategory(Request $request){
        TaskSubCategory::find($request->id)->delete();
        flash()->addSuccess('Task Sub-Category Deleted Successfully');
        return redirect()->route('task_subcategory_list');
    }
  
  	public function main_tasks_list(){
        $tasks = Tasks::join('users','users.id','tasks.user_id')
        ->join('task_sub_categories','task_sub_categories.id','tasks.task_name')
        ->select('tasks.*','users.first_name','users.last_name','task_sub_categories.task_sub_category_name')
        ->get();
        return view('Admin.tasks-list',compact('tasks'));
    }
}
