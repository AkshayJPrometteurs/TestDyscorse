<?php

namespace App\Http\Controllers\MobileAppAPI;

use App\Http\Controllers\Controller;
use App\Models\MobileApp\Journal;
use Illuminate\Http\Request;

class JournalController extends Controller
{
    public function journal_store(Request $request){
        //$request->validate(['journal_title' => 'required']);
        /*$data = Journal::where(['user_id' => $request->user_id,'journal_title' => $request->journal_title])->first();
        if($data){return response()->json(['status' => 200,'data' => [],'message' => 'Journal Already Exists.']);
        }else{
            $journalFile = null;
          	$journalAttachment = null;
            if($request->hasFile('journal_file')){
                $journalFile = 'Journal_'.rand(1111111111,99999999999).".".$request->file('journal_file')->getClientOriginalExtension();
                $request->file('journal_file')->move('assets/journals', $journalFile);
            }
          	if($request->hasFile('journal_attachment')){
                $fileAttach = $request->file('journal_attachment');
                $journalAttachment = 'Journal_Attachment_'.rand(1111111111,9999999999).".".$fileAttach->getClientOriginalExtension();
                $fileAttach->move('assets/journals',$journalAttachment);
            }
            Journal::create([
                'user_id' => $request->user_id,
              	'journal_date' => $request->journal_date,
                'journal_title' => $request->journal_title,
                'journal_desc' => $request->journal_desc,
                'journal_file' => $journalFile,
              	'journal_attachment' => $journalAttachment,
            ]);
            return response()->json(['status' => 200,'data' => [],'message' => 'Journal Created Successfully']);
        }*/
      	$journalFile = null;
        $journalAttachment = null;
        if($request->hasFile('journal_file')){
            $journalFile = 'Journal_'.rand(1111111111,99999999999).".".$request->file('journal_file')->getClientOriginalExtension();
            $request->file('journal_file')->move('assets/journals', $journalFile);
        }
        if($request->hasFile('journal_attachment')){
            $fileAttach = $request->file('journal_attachment');
            $journalAttachment = 'Journal_Attachment_'.rand(1111111111,9999999999).".".$fileAttach->getClientOriginalExtension();
            $fileAttach->move('assets/journals',$journalAttachment);
        }
        Journal::create([
            'user_id' => $request->user_id,
            'journal_date' => $request->journal_date,
            'journal_title' => $request->journal_title,
            'journal_desc' => $request->journal_desc,
            'journal_file' => $journalFile,
            'journal_attachment' => $journalAttachment,
        ]);
        return response()->json(['status' => 200,'data' => [],'message' => 'Journal Created Successfully']);
    }

    public function journal_list(Request $request){
        $journals = Journal::where('user_id',$request->user_id)->orderBy('id','desc')->get();
        if($journals->isNotEmpty()){
            foreach($journals as $data){
              $journals_data[] = [
                  'journal_id' => $data->id,
                  'journal_title' => $data->journal_title,
                  'journal_desc' => $data->journal_desc,
                  'journal_file' => $data->journal_file ? url('/assets/journals/')."/".$data->journal_file : '',
                  'journal_attachment' => $data->journal_attachment ? url('/assets/journals/')."/".$data->journal_attachment : '',
                  'journal_time' => date('d-M-Y', strtotime($data->journal_date)),
              ];
          }
          return response()->json(['status' => 200,'data' => ['journals' => $journals_data],'message' => 'Data Fetched Successfully']);
        }else{
          return response()->json(['status' => 400,'data' => [],'message' => 'No Data Found']);
        }
    }

    public function journal_search(Request $request){
        $request->validate(['keyword'=>'required']);
        $journals = Journal::where('journal_title', 'LIKE', '%' . $request->keyword . '%')->where('user_id',$request->user_id)->get();
        if($journals->isNotEmpty()){
            foreach($journals as $data){
                $journals_data[] = [
                    'journal_id' => $data->id,
                    'journal_title' => $data->journal_title,
                    'journal_desc' => $data->journal_desc,
                    'journal_file' => $data->journal_file ? url('/assets/journals/')."/".$data->journal_file : '',
                    'journal_attachment' => $data->journal_attachment ? url('/assets/journals/')."/".$data->journal_attachment : '',
                    'journal_time' => date('d-M-Y', strtotime($data->journal_date)),
                ];
            }
            return response()->json(['status' => 200,'data' => ['journals' => $journals_data],'message' => 'Data Fetched Successfully']);
        }else{
            return response()->json(['status' => 200,'data' => [],'message' => 'Data Not Found']);
        }
    }
}
