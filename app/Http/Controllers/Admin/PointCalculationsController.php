<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\PointCalculations;
use App\Models\MobileApp\MilestonePointsCalculations;
use Illuminate\Http\Request;

class PointCalculationsController extends Controller
{
    public function point_calculation_view(){
        $points = PointCalculations::first();
        $milestone = MilestonePointsCalculations::all();
        return view('Admin.point-calculations.point-calculations',compact('points','milestone'));
    }

    public function point_calculation_store(Request $request){
        $request->validate([
            'task_completion' => 'required',
            'max_streak' => 'required',
            'se_follow' => 'required',
            'se_assigning_task_to_family_member' => 'required',
            'feedback' => 'required',
            'app_sharing' => 'required',
            'reflection_and_review' => 'required',
        ]);
        $points_data = PointCalculations::first();
        if($points_data){
            MilestonePointsCalculations::query()->truncate();
            array_map(function ($item1, $item2, $item3) {
                MilestonePointsCalculations::create(['milestone_start' => $item1,'milestone_end' => $item2,'milestone_points' => $item3,]);
            }, $request->milestone_start, $request->milestone_end, $request->milestone_points);
            PointCalculations::find($points_data->id)->update($request->all());
        }else{
            PointCalculations::create($request->all());
        }
      	flash()->addSuccess('Data Updated Successfully');
        return redirect()->back();
    }

    public function delete_milestone(Request $request){
        MilestonePointsCalculations::find($request->id)->delete();
      	flash()->addSuccess('Data Deleted Successfully');
        return redirect()->back();
    }
}
