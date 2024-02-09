<?php

namespace App\Http\Controllers\MobileAppAPI;

use App\Http\Controllers\Controller;
use App\Models\MobileApp\Tasks;
use Carbon\Carbon;
use Illuminate\Http\Request;

class YourInsightsController extends Controller
{
    public function your_insights(Request $request){
        // week task calculations
        $total_tasks_for_weeks = Tasks::whereBetween('date', [Carbon::parse($request->date_time)->startOfWeek()->format('Y-m-d'), Carbon::parse($request->date_time)->endOfWeek()->format('Y-m-d')])->orderBy('date','asc')->where('user_id',$request->user_id)->count();
        $not_completed_tasks_for_weeks = Tasks::whereBetween('date', [Carbon::parse($request->date_time)->startOfWeek()->format('Y-m-d'), Carbon::parse($request->date_time)->endOfWeek()->format('Y-m-d')])->orderBy('date','asc')->where(['user_id'=>$request->user_id,'task_status'=>0])->count();
        $completed_tasks_for_weeks = Tasks::whereBetween('date', [Carbon::parse($request->date_time)->startOfWeek()->format('Y-m-d'), Carbon::parse($request->date_time)->endOfWeek()->format('Y-m-d')])->orderBy('date','asc')->where(['user_id'=>$request->user_id,'task_status'=>1])->count();
        $cancelled_tasks_for_weeks = Tasks::whereBetween('date', [Carbon::parse($request->date_time)->startOfWeek()->format('Y-m-d'), Carbon::parse($request->date_time)->endOfWeek()->format('Y-m-d')])->orderBy('date','asc')->where(['user_id'=>$request->user_id,'task_status'=>2])->count();
        $rescheduled_tasks_for_weeks = Tasks::whereBetween('date', [Carbon::parse($request->date_time)->startOfWeek()->format('Y-m-d'), Carbon::parse($request->date_time)->endOfWeek()->format('Y-m-d')])->orderBy('date','asc')->where(['user_id'=>$request->user_id,'task_status'=>3])->count();

        // month task calculations
        $total_tasks_for_month = Tasks::whereMonth('date', date('m',strtotime($request->date)))->where('user_id',$request->user_id)->count();
        $not_completed_tasks_for_month = Tasks::whereMonth('date', date('m',strtotime($request->date)))->where(['user_id'=>$request->user_id,'task_status'=>0])->count();
        $completed_tasks_for_month = Tasks::whereMonth('date', date('m',strtotime($request->date)))->where(['user_id'=>$request->user_id,'task_status'=>1])->count();
        $cancelled_tasks_for_month = Tasks::whereMonth('date', date('m',strtotime($request->date)))->where(['user_id'=>$request->user_id,'task_status'=>2])->count();
        $rescheduled_tasks_for_month = Tasks::whereMonth('date', date('m',strtotime($request->date)))->where(['user_id'=>$request->user_id,'task_status'=>3])->count();

        //quarterly task calculations
        $total_tasks_for_quarterly = Tasks::whereBetween('date', [Carbon::parse($request->date_time)->startOfQuarter()->format('Y-m-d'),Carbon::parse($request->date_time)->endOfQuarter()->format('Y-m-d')])->where('user_id',$request->user_id)->orderBy('date','asc')->count();
        $not_completed_tasks_for_quarterly = Tasks::whereBetween('date', [Carbon::parse($request->date_time)->startOfQuarter()->format('Y-m-d'),Carbon::parse($request->date_time)->endOfQuarter()->format('Y-m-d')])->orderBy('date','asc')->where(['user_id'=>$request->user_id,'task_status'=>0])->count();
        $completed_tasks_for_quarterly = Tasks::whereBetween('date', [Carbon::parse($request->date_time)->startOfQuarter()->format('Y-m-d'),Carbon::parse($request->date_time)->endOfQuarter()->format('Y-m-d')])->orderBy('date','asc')->where(['user_id'=>$request->user_id,'task_status'=>1])->count();
        $cancelled_tasks_for_quarterly = Tasks::whereBetween('date', [Carbon::parse($request->date_time)->startOfQuarter()->format('Y-m-d'),Carbon::parse($request->date_time)->endOfQuarter()->format('Y-m-d')])->orderBy('date','asc')->where(['user_id'=>$request->user_id,'task_status'=>2])->count();
        $rescheduled_tasks_for_quarterly = Tasks::whereBetween('date', [Carbon::parse($request->date_time)->startOfQuarter()->format('Y-m-d'),Carbon::parse($request->date_time)->endOfQuarter()->format('Y-m-d')])->orderBy('date','asc')->where(['user_id'=>$request->user_id,'task_status'=>3])->count();

        // year task calculations
        $total_tasks_for_year = Tasks::whereYear('date', date('Y',strtotime($request->date)))->where('user_id',$request->user_id)->count();
        $not_completed_tasks_for_year = Tasks::whereYear('date', date('Y',strtotime($request->date)))->where(['user_id'=>$request->user_id,'task_status'=>0])->count();
        $completed_tasks_for_year = Tasks::whereYear('date', date('Y',strtotime($request->date)))->where(['user_id'=>$request->user_id,'task_status'=>1])->count();
        $cancelled_tasks_for_year = Tasks::whereYear('date', date('Y',strtotime($request->date)))->where(['user_id'=>$request->user_id,'task_status'=>2])->count();
        $rescheduled_tasks_for_year = Tasks::whereYear('date', date('Y',strtotime($request->date)))->where(['user_id'=>$request->user_id,'task_status'=>3])->count();

        return response()->json([
            'status' => 200,
            'data' => [
                'your_insights' => [
                    'week' => [
                        'not_completed_tasks' => $not_completed_tasks_for_weeks ? round(($not_completed_tasks_for_weeks/$total_tasks_for_weeks)*100) : 0,
                        'completed_tasks' => $completed_tasks_for_weeks ? round(($completed_tasks_for_weeks/$total_tasks_for_weeks)*100) : 0,
                        'cancelled_tasks' => $cancelled_tasks_for_weeks ? round(($cancelled_tasks_for_weeks/$total_tasks_for_weeks)*100) : 0,
                        'rescheduled_tasks' => $rescheduled_tasks_for_weeks ? round(($rescheduled_tasks_for_weeks/$total_tasks_for_weeks)*100) : 0,
                    ],
                    'month' => [
                        'not_completed_tasks' => $not_completed_tasks_for_month ? round(($not_completed_tasks_for_month/$total_tasks_for_month)*100) : 0,
                        'completed_tasks' => $completed_tasks_for_month ? round(($completed_tasks_for_month/$total_tasks_for_month)*100) : 0,
                        'cancelled_tasks' => $cancelled_tasks_for_month ? round(($cancelled_tasks_for_month/$total_tasks_for_month)*100) : 0,
                        'rescheduled_tasks' => $rescheduled_tasks_for_month ? round(($rescheduled_tasks_for_month/$total_tasks_for_month)*100) : 0,
                    ],
                    'quartely' => [
                        'not_completed_tasks' => $not_completed_tasks_for_quarterly ? round(($not_completed_tasks_for_quarterly/$total_tasks_for_quarterly)*100) : 0,
                        'completed_tasks' => $completed_tasks_for_quarterly ? round(($completed_tasks_for_quarterly/$total_tasks_for_quarterly)*100) : 0,
                        'cancelled_tasks' => $cancelled_tasks_for_quarterly ? round(($cancelled_tasks_for_quarterly/$total_tasks_for_quarterly)*100) : 0,
                        'rescheduled_tasks' => $rescheduled_tasks_for_quarterly ? round(($rescheduled_tasks_for_quarterly/$total_tasks_for_quarterly)*100) : 0,
                    ],
                    'yearly' => [
                        'not_completed_tasks' => $not_completed_tasks_for_year ? round(($not_completed_tasks_for_year/$total_tasks_for_year)*100) : 0,
                        'completed_tasks' => $completed_tasks_for_year ? round(($completed_tasks_for_year/$total_tasks_for_year)*100) : 0,
                        'cancelled_tasks' => $cancelled_tasks_for_year ? round(($cancelled_tasks_for_year/$total_tasks_for_year)*100) : 0,
                        'rescheduled_tasks' => $rescheduled_tasks_for_year ? round(($rescheduled_tasks_for_year/$total_tasks_for_year)*100) : 0,
                    ]
                ]
            ],
            'message' => 'Data Fetched Successfully'
        ]);
    }

    public function your_insights_for_week(Request $request){
        $request->validate(['week_start'=>'required','week_end'=>'required']);
        $total_tasks_for_weeks = Tasks::whereBetween('date', [$request->week_start, $request->week_end])->where('user_id',$request->user_id)->orderBy('date','asc')->count();
        $not_completed_tasks_for_weeks = Tasks::whereBetween('date', [$request->week_start, $request->week_end])->orderBy('date','asc')->where(['user_id'=>$request->user_id,'task_status'=>0])->count();
        $completed_tasks_for_weeks = Tasks::whereBetween('date', [$request->week_start, $request->week_end])->orderBy('date','asc')->where(['user_id'=>$request->user_id,'task_status'=>1])->count();
        $cancelled_tasks_for_weeks = Tasks::whereBetween('date', [$request->week_start, $request->week_end])->orderBy('date','asc')->where(['user_id'=>$request->user_id,'task_status'=>2])->count();
        $rescheduled_tasks_for_weeks = Tasks::whereBetween('date', [$request->week_start, $request->week_end])->orderBy('date','asc')->where(['user_id'=>$request->user_id,'task_status'=>3])->count();

        return response()->json([
            'status' => 200,
            'data' => [
                'your_insights_week' => [
                    'not_completed_tasks' => $not_completed_tasks_for_weeks ? round(($not_completed_tasks_for_weeks/$total_tasks_for_weeks)*100) : 0,
                    'completed_tasks' => $completed_tasks_for_weeks ? round(($completed_tasks_for_weeks/$total_tasks_for_weeks)*100) : 0,
                    'cancelled_tasks' => $cancelled_tasks_for_weeks ? round(($cancelled_tasks_for_weeks/$total_tasks_for_weeks)*100) : 0,
                    'rescheduled_tasks' => $rescheduled_tasks_for_weeks ? round(($rescheduled_tasks_for_weeks/$total_tasks_for_weeks)*100) : 0,
                ]
            ],
            'message' => 'Data Fetched Successfully'
        ]);
    }

    public function your_insights_for_month(Request $request){
        $request->validate(['month'=>'required']);
        $total_tasks_for_month = Tasks::whereMonth('date', $request->month)->where('user_id',$request->user_id)->count();
        $not_completed_tasks_for_month = Tasks::whereMonth('date', $request->month)->where(['user_id'=>$request->user_id,'task_status'=>0])->count();
        $completed_tasks_for_month = Tasks::whereMonth('date', $request->month)->where(['user_id'=>$request->user_id,'task_status'=>1])->count();
        $cancelled_tasks_for_month = Tasks::whereMonth('date', $request->month)->where(['user_id'=>$request->user_id,'task_status'=>2])->count();
        $rescheduled_tasks_for_month = Tasks::whereMonth('date', $request->month)->where(['user_id'=>$request->user_id,'task_status'=>3])->count();

        return response()->json([
            'status' => 200,
            'data' => [
                'your_insights_month' => [
                    'not_completed_tasks' => $not_completed_tasks_for_month ? round(($not_completed_tasks_for_month/$total_tasks_for_month)*100) : 0,
                    'completed_tasks' => $completed_tasks_for_month ? round(($completed_tasks_for_month/$total_tasks_for_month)*100) : 0,
                    'cancelled_tasks' => $cancelled_tasks_for_month ? round(($cancelled_tasks_for_month/$total_tasks_for_month)*100) : 0,
                    'rescheduled_tasks' => $rescheduled_tasks_for_month ? round(($rescheduled_tasks_for_month/$total_tasks_for_month)*100) : 0,
                ]
            ],
            'message' => 'Data Fetched Successfully'
        ]);
    }

    public function your_insights_for_quarterly(Request $request){
        $request->validate(['start_quarter'=>'required','end_quarter'=>'required']);
        $total_tasks_for_quarterly = Tasks::whereBetween('date', [$request->start_quarter,$request->end_quarter])->where('user_id',$request->user_id)->orderBy('date','asc')->count();
        $not_completed_tasks_for_quarterly = Tasks::whereBetween('date', [$request->start_quarter,$request->end_quarter])->orderBy('date','asc')->where(['user_id'=>$request->user_id,'task_status'=>0])->count();
        $completed_tasks_for_quarterly = Tasks::whereBetween('date', [$request->start_quarter,$request->end_quarter])->orderBy('date','asc')->where(['user_id'=>$request->user_id,'task_status'=>1])->count();
        $cancelled_tasks_for_quarterly = Tasks::whereBetween('date', [$request->start_quarter,$request->end_quarter])->orderBy('date','asc')->where(['user_id'=>$request->user_id,'task_status'=>2])->count();
        $rescheduled_tasks_for_quarterly = Tasks::whereBetween('date', [$request->start_quarter,$request->end_quarter])->orderBy('date','asc')->where(['user_id'=>$request->user_id,'task_status'=>3])->count();

        return response()->json([
            'status' => 200,
            'data' => [
                'your_insights_month' => [
                    'not_completed_tasks' => $not_completed_tasks_for_quarterly ? round(($not_completed_tasks_for_quarterly/$total_tasks_for_quarterly)*100) : 0,
                    'completed_tasks' => $completed_tasks_for_quarterly ? round(($completed_tasks_for_quarterly/$total_tasks_for_quarterly)*100) : 0,
                    'cancelled_tasks' => $cancelled_tasks_for_quarterly ? round(($cancelled_tasks_for_quarterly/$total_tasks_for_quarterly)*100) : 0,
                    'rescheduled_tasks' => $rescheduled_tasks_for_quarterly ? round(($rescheduled_tasks_for_quarterly/$total_tasks_for_quarterly)*100) : 0,
                ]
            ],
            'message' => 'Data Fetched Successfully'
        ]);
    }

    public function your_insights_for_yearly(Request $request){
        $request->validate(['year'=>'required']);
        $total_tasks_for_year = Tasks::whereYear('date', $request->year)->where('user_id',$request->user_id)->count();
        $not_completed_tasks_for_year = Tasks::whereYear('date', $request->year)->where(['user_id'=>$request->user_id,'task_status'=>0])->count();
        $completed_tasks_for_year = Tasks::whereYear('date', $request->year)->where(['user_id'=>$request->user_id,'task_status'=>1])->count();
        $cancelled_tasks_for_year = Tasks::whereYear('date', $request->year)->where(['user_id'=>$request->user_id,'task_status'=>2])->count();
        $rescheduled_tasks_for_year = Tasks::whereYear('date', $request->year)->where(['user_id'=>$request->user_id,'task_status'=>3])->count();
      
      	/*echo "Total Per : ".$total_tasks_for_year."\n";
        echo "Not Completed Per : ".$not_completed_tasks_for_year."\n";
        echo "Completed Per : ".$completed_tasks_for_year."\n";
        echo "Cancelled Per : ".$cancelled_tasks_for_year."\n";
        echo "Rescheduled Per : ".$rescheduled_tasks_for_year."\n";*/


        return response()->json([
            'status' => 200,
            'data' => [
                'your_insights_month' => [
                    'not_completed_tasks' => $not_completed_tasks_for_year ? round(($not_completed_tasks_for_year/$total_tasks_for_year)*100) : 0,
                    'completed_tasks' => $completed_tasks_for_year ? round(($completed_tasks_for_year/$total_tasks_for_year)*100) : 0,
                    'cancelled_tasks' => $cancelled_tasks_for_year ? round(($cancelled_tasks_for_year/$total_tasks_for_year)*100) : 0,
                    'rescheduled_tasks' => $rescheduled_tasks_for_year ? round(($rescheduled_tasks_for_year/$total_tasks_for_year)*100) : 0,
                ]
            ],
            'message' => 'Data Fetched Successfully'
        ]);
    }
}
