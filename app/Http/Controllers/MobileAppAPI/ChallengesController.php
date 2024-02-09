<?php

namespace App\Http\Controllers\MobileAppAPI;

use App\Http\Controllers\Controller;
use App\Models\Admin\Slogan;
use App\Models\MobileApp\ChallengePostComment;
use App\Models\MobileApp\ChallengePostLike;
use App\Models\MobileApp\ChallengePosts;
use App\Models\MobileApp\Challenges;
use App\Models\MobileApp\CompleteChallenge;
use App\Models\MobileApp\FriendsFollowers;
use App\Models\MobileApp\FriendsFollowing;
use App\Models\MobileApp\UserJoinChallenge;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ChallengesController extends Controller
{
    public function create_challenge(Request $request){
        $request->validate(['challenges_title' => 'required','challenge_date'=>'required','challenges_desc' => 'required']);
        $challengeCount = Challenges::where(['user_id' => $request->user_id])
        ->whereDate('challenge_date', '=', date('Y-m-d', strtotime($request->challenge_date)))
        ->count();
        if($challengeCount < 2){
            $imageName = null;
            if($request->hasFile('challenges_image')){
                $imageName = 'Challenges_Image_'.rand(1111111111,9999999999).".".$request->file('challenges_image')->getClientOriginalExtension();
                $request->file('challenges_image')->move('assets/images/challenges',$imageName);
            }
            $newChallengeData = $request->except('challenges_image');
            $newChallengeData['challenges_image'] = $imageName;
            $newChallengeData['consistency_count'] = 0;
            $challenge = Challenges::create($newChallengeData);
            UserJoinChallenge::create([
                'user_challenge_joinned_date' => $request->challenge_date,
                'user_id' => $request->user_id,
                'challenge_id' => $challenge->id,
            ]);
            return response()->json(['status'=> 200,'data'=>[],'message'=>'Challenge Created Successfully']);
        }else{
            return response()->json(['status'=> 200,'data'=>[],'message'=>'You Have Reached Maximum Limit For Create Challenge']);
        }
    }

    public function challenges_list(){
        $challenges = Challenges::select('id', 'challenge_date' , 'challenges_title', 'challenges_desc', 'challenges_image')->orderBy('id','desc')->get();
        return response()->json(['status'=> 200,'data'=>['challenges'=>$challenges],'message'=>'Challenge List Fetched Successfully']);
    }

    public function challenges_suggestion_list(Request $request){
        $challenges = Challenges::orderBy('id','desc')->get();
        if($challenges->isNotEmpty()){
            foreach($challenges as $data){
                $userJoined = UserJoinChallenge::where('user_id', $request->user_id)
                ->where('challenge_id', $data->id)
                ->first();
                $challengesData[] = [
                    'id' => $data->id,
                    'title' => $data->challenges_title,
                    'description' => $data->challenges_desc,
                    'image' => $data->challenges_image,
                    'joined' => $userJoined ? true : false,
                    'joined_at' => $userJoined ? Carbon::parse($userJoined->user_challenge_joinned_date)->diffForHumans() : '',
                    'delete_challenge_flag' => $request->user_id == $data->user_id ? true : false
                ];
            }
        }else{ $challengesData = []; }
        return response()->json(['status'=> 200,'data'=>['challenges'=>$challengesData],'message'=>'Challenge Suggestion List Fetched Successfully']);
    }

    public function user_join_challenge(Request $request){
        $request->validate(['challenge_id'=>'required']);
        $challenge = Challenges::find($request->challenge_id);
        if($challenge){
            $JoinedChallenge = UserJoinChallenge::where(['user_id' => $request->user_id, 'challenge_id' => $request->challenge_id])
            ->whereDate('user_challenge_joinned_date', '=', date('Y-m-d', strtotime($request->user_challenge_joinned_date)))
            ->first();
            if($JoinedChallenge){
                return response()->json(['status'=> 200,'data'=>[],'message'=>'You Are Already Joined This Challenge']);
            }else{
                $ChallengeJoinedData = UserJoinChallenge::where('user_id',$request->user_id)
                ->whereDate('user_challenge_joinned_date', '=', date('Y-m-d', strtotime($request->user_challenge_joinned_date)))
                ->count();
                if($ChallengeJoinedData < 2){
                    UserJoinChallenge::create($request->all());
                    return response()->json(['status'=> 200,'data'=>[],'message'=>'Challenge Joined Successfully']);
                }else{
                    return response()->json(['status'=> 200,'data'=>[],'message'=>'You Have Reached Maximum Limit To Join Challenge']);
                }
            }
        }else{
            return response()->json(['status'=> 200,'data'=>[],'message'=>'Challenge Data Not Found']);
        }
    }

    public function user_unjoin_challenge(Request $request){
        $request->validate(['challenge_id'=>'required']);
        $challenge = Challenges::find($request->challenge_id);
        if($challenge){
            $JoinedChallenge = UserJoinChallenge::where(['user_id' => $request->user_id, 'challenge_id' => $request->challenge_id])->first();
            if($JoinedChallenge){
                $JoinedChallenge->delete();
                return response()->json(['status'=> 200,'data'=>[],'message'=>'Challenge Unjoined Successfully']);
            }
        }else{
            return response()->json(['status'=> 200,'data'=>[],'message'=>'Challenge Data Not Found']);
        }
    }

    public function reset_reminder_time(Request $request){
        $request->validate(['challenge_id'=>'required']);
        $challenge = Challenges::find($request->challenge_id);
        if($challenge){
            $JoinedChallenge = UserJoinChallenge::where(['user_id' => $request->user_id, 'challenge_id' => $request->challenge_id])->first();
            if($JoinedChallenge){
                $JoinedChallenge->update(['challenge_time'=>$request->challenge_time]);
                return response()->json(['status'=> 200,'data'=>[],'message'=>'Challenge Reminder Time Reset Successfully']);
            }
        }else{
            return response()->json(['status'=> 200,'data'=>[],'message'=>'Challenge Data Not Found']);
        }
    }

    public function challenges_screen(Request $request){
        $challenges = Challenges::select('id', 'challenges_title', 'challenges_desc', 'challenges_image')->orderBy('id','desc')->limit(5)->get();
        $randomSentences = [
            "Ready for a wild ride?",
            "Dive into the challenges – your rules, your way!",
            "Imagine turning your daily routine into the ultimate adventure, and the best part?",
            "You get to bring your friends along for the ride!",
            "Together, you'll create habits, nail consistency, and high-five your way to victory.",
            "Get ready to cheer each other on, hold tight, and enjoy the rollercoaster of epic challenges with your squad!",
        ];
        $text = $randomSentences[array_rand($randomSentences)];
        //$slogan = Slogan::inRandomOrder()->first(['slogan_name']);
        if($challenges->isNotEmpty()){
            foreach($challenges as $data){
                $userJoined = UserJoinChallenge::where('user_id', $request->user_id)
                ->where('challenge_id', $data->id)
                ->first();
                $challengesData[] = [
                    'id' => $data->id,
                    'title' => $data->challenges_title,
                    'description' => $data->challenges_desc,
                    'image' => $data->challenges_image,
                    'joined' => $userJoined ? true : false,
                    'joined_at' => $userJoined ? Carbon::parse($userJoined->user_challenge_joinned_date)->diffForHumans() : '',
                    'delete_challenge_flag' => $request->user_id == $data->user_id ? true : false
                ];
            }
        }else{ $challengesData = []; }
        return response()->json([
            'status'=> 200,
            'data'=>[
                'slogan'=>$text,
                'challenges'=>$challenges,
                'Challenges_suggestions'=>$challengesData
            ],
            'message'=>'Challenges Data Fetched Successfully'
        ]);
    }

    public function delete_challenge(Request $request){
        $request->validate(['challenge_id'=>'required']);
        $challenge = Challenges::where(['id'=>$request->challenge_id,'user_id'=>$request->user_id])->first();
        if($challenge){
            if(File::exists(public_path()."/assets/images/challenges/".$challenge->challenges_image)){
                File::delete(public_path()."/assets/images/challenges/".$challenge->challenges_image);
            }
            $challenge->delete();
            UserJoinChallenge::where('challenge_id',$request->challenge_id)->delete();
            return response()->json(['status'=> 200,'data'=>[],'message'=>'Challenge Deleted Successfully']);
        }else{
            return response()->json(['status'=> 200,'data'=>[],'message'=>'Challenge Not Found']);
        }
    }

    public function create_challenge_post(Request $request){
        $request->validate(['challenge_id' => 'required','post_caption'=>'required']);
        $challenge = Challenges::find($request->challenge_id);
        if($challenge){
            $imageName = null;
            if($request->hasFile('post_image')){
                $imageName = 'Challenge_Post_Image_'.rand(1111111111,9999999999).".".$request->file('post_image')->getClientOriginalExtension();
                $request->file('post_image')->move('assets/images/challenges/posts',$imageName);
            }
            $newChallengePostData = $request->except('post_image');
            $newChallengePostData['post_image'] = $imageName;
            ChallengePosts::create($newChallengePostData);
            return response()->json(['status'=> 200,'data'=>[],'message'=>'Challenge Post Created Successfully']);
        }else{
            return response()->json(['status'=> 200,'data'=>[],'message'=>'Challenge Data Not Found']);
        }
    }

    public function challenge_detail(Request $request){
        $request->validate(['challenge_id' => 'required','date'=>'required']);
        $challenge = Challenges::find($request->challenge_id);
        if($challenge){
            // $complete_challenges = CompleteChallenge::where(['user_id' => $request->user_id,'challenge_id' => $request->challenge_id,'challenge_status' => 'completed'])->get();
            // $completedDates = $complete_challenges->pluck('challenge_completed_date')->map(function ($date) { return Carbon::parse($date)->format('Y-m-d'); })->toArray();
            // $todatDate = Carbon::parse($request->date);
            // $startWeek = $todatDate->copy()->startOfWeek();
            // $endWeek = $todatDate->copy()->endOfWeek();
            // $dateRange = [];
            // $consistencyCount = 0;
            // $maxConsistencyCount = 0;
            // foreach (CarbonPeriod::between($startWeek, $endWeek) as $date) {
            //     if (in_array($date->format('Y-m-d'), $completedDates)) {
            //         $consistencyCount++;
            //         if ($consistencyCount > $maxConsistencyCount) {
            //             $maxConsistencyCount = $consistencyCount;
            //         }
            //         $challenge_status = 'completed';
            //     } else {
            //         $maxConsistencyCount = max($consistencyCount, $maxConsistencyCount);
            //         $consistencyCount = 0;
            //         $challenge_status = 'incompleted';
            //     }
            //     $dateRange[] = [
            //         'date' => $date->copy()->format('Y-m-d'),
            //         'day' => $date->copy()->format('l'),
            //         'challenge_status' => $challenge_status
            //     ];
            // }

            $complete_challenges = CompleteChallenge::where(['user_id' => $request->user_id,'challenge_id' => $request->challenge_id,'challenge_status' => 'completed'])->get();

            $completedDates = $complete_challenges->pluck('challenge_completed_date')->map(function ($date) { return Carbon::parse($date)->format('Y-m-d'); })->toArray();

            $todatDate = Carbon::parse($request->date);
            $startWeek = $todatDate->copy()->startOfWeek(Carbon::SUNDAY);
            $endWeek = $todatDate->copy()->endOfWeek(Carbon::SATURDAY);

            $dateRange = [];
            $consistencyCount = 0;
            $maxConsistencyCount = 0;

            foreach (CarbonPeriod::between($startWeek, $endWeek) as $date) {
                if (in_array($date->format('Y-m-d'), $completedDates)) {
                    $consistencyCount++;
                    if ($consistencyCount > $maxConsistencyCount) { $maxConsistencyCount = $consistencyCount; }
                    $challenge_status = 'completed';
                } else {
                    $maxConsistencyCount = max($consistencyCount, $maxConsistencyCount);
                    $consistencyCount = 0;
                    $challenge_status = 'incompleted';
                }
                $dateRange[] = [
                    'date' => $date->copy()->format('Y-m-d'),
                    'day' => $date->copy()->format('l'),
                    'challenge_status' => $challenge_status
                ];
            }

            $joinedMembers = UserJoinChallenge::select('users.profile_image')->join('users','users.id','=','user_id')->where('challenge_id',$request->challenge_id)->get();
            $posts = ChallengePosts::select('challenge_posts.id as post_id','challenge_posts.post_caption','challenge_posts.post_image','users.id as user_id','users.first_name','users.last_name','users.profile_image')
            ->join('users','users.id','=','user_id')->where('challenge_id',$request->challenge_id)->get();
            if($posts->isNotEmpty()){
                foreach($posts as $data){
                    if($data->user_id == $request->user_id){ $userName = 'You';
                    }else{ $userName = $data->first_name." ".$data->last_name; }
                    $likedData = ChallengePostLike::where(['user_id' => $request->user_id,'challenge_id' => $request->challenge_id,'post_id' => $data->post_id,'like_status' => 'yes'])->exists();

                    $likeCount = ChallengePostLike::where(['challenge_id'=>$request->challenge_id,'post_id'=>$data->post_id])->count();
                    $commentCount = ChallengePostComment::where(['challenge_id'=>$request->challenge_id,'post_id'=>$data->post_id])->count();

                    $postData[] = [
                        'user_id' => $data->user_id,
                        'name' => $userName,
                        'profile_image' => $data->profile_image,
                        'post_id' => $data->post_id,
                        'post_caption' => $data->post_caption,
                        'post_image' => $data->post_image,
                        'like_count' => $likeCount,
                        'comment_count' => $commentCount,
                        'like_status' => $likedData
                    ];
                }
                $currentUserID = $request->user_id;
                usort($postData, function($a, $b) use ($currentUserID) {
                    if ($a['user_id'] == $currentUserID) { return -1;
                    } elseif ($b['user_id'] == $currentUserID) { return 1;
                    } else { return 0; }
                });
            }
            return response()->json([
                'status'=> 200,
                'data'=>[
                    'dates' => [
                        'data' => $dateRange,
                        'consistancy_data' => ($maxConsistencyCount > 0) ? $maxConsistencyCount : 0
                    ],
                    'joined_members'=>['list' => $joinedMembers,'count' => $joinedMembers->isNotEmpty() ? count($joinedMembers) : 0],
                    'posts'=>$postData ?? ''
                ],
                'message'=>'Challenges Data Fetched Successfully'
            ]);
        }else{
            return response()->json(['status'=> 200,'data'=>[],'message'=>'Challenge Not Found']);
        }
    }

    public function challenge_post_like(Request $request){
        $request->validate(['challenge_id' => 'required','post_id' => 'required']);
        $challenge = Challenges::find($request->challenge_id);
        if($challenge){
            $post = ChallengePosts::find($request->post_id);
            if($post){
                $challenge_post_like_data = ChallengePostLike::where(['user_id' => $request->user_id,'challenge_id' => $request->challenge_id,'post_id' => $request->post_id,'like_status' => 'yes'])->first();
                if($challenge_post_like_data){
                    $challenge_post_like_data->delete();
                    return response()->json(['status'=> 200,'data'=>[],'message'=>'Challenge Post Unliked']);
                }else{
                    $request->merge(['like_status'=>'yes']);
                    ChallengePostLike::create($request->all());
                    return response()->json(['status'=> 200,'data'=>[],'message'=>'Challenge Post Liked']);
                }
            }else{ return response()->json(['status'=> 200,'data'=>[],'message'=>'Challenge Post Data Not Found']); }
        }else{ return response()->json(['status'=> 200,'data'=>[],'message'=>'Challenge Not Found']); }
    }

    public function challenge_post_comment(Request $request){
        $request->validate(['challenge_id' => 'required','post_id' => 'required','challenge_comment_date'=>'required','comment'=>'required']);
        $challenge = Challenges::find($request->challenge_id);
        if($challenge){
            $post = ChallengePosts::find($request->post_id);
            if($post){
                ChallengePostComment::create($request->all());
                return response()->json(['status'=> 200,'data'=>[],'message'=>'Challenge Post Comment Saved']);
            }else{ return response()->json(['status'=> 200,'data'=>[],'message'=>'Challenge Post Data Not Found']); }
        }else{ return response()->json(['status'=> 200,'data'=>[],'message'=>'Challenge Not Found']); }
    }

    public function challenge_post_details(Request $request){
        $request->validate(['challenge_id' => 'required', 'post_id'=> 'required']);
        $challenge = Challenges::find($request->challenge_id);
        if($challenge){
            $posts = ChallengePosts::select('challenge_posts.id as post_id','challenge_posts.post_caption','challenge_posts.post_image','users.id as user_id','users.first_name','users.last_name','users.profile_image')
            ->join('users','users.id','=','user_id')->where('challenge_id',$request->challenge_id)->first();
            if($posts){
                $likedData = ChallengePostLike::where(['user_id' => $request->user_id,'challenge_id' => $request->challenge_id,'post_id' => $request->post_id,'like_status' => 'yes'])->exists();
                $likeData = ChallengePostLike::join('users','users.id','=','user_id')
                ->select('challenge_post_likes.id as like_id','challenge_post_likes.challenge_id','challenge_post_likes.post_id','users.id as user_id','users.first_name','users.last_name','users.profile_image')
                ->where(['challenge_id'=>$request->challenge_id,'post_id'=>$posts->post_id])->get();
                if($likeData->isNotEmpty()){
                    foreach($likeData as $data){
                        if($data->user_id == $request->user_id){ $userName = 'You';
                        }else{ $userName = $data->first_name." ".$data->last_name; }
                        $likeWithData[] = [
                            'like_id' => $data->like_id,
                            'challenge_id' => $data->challenge_id,
                            'post_id' => $data->post_id,
                            'user_id' => $data->user_id,
                            'name' => $userName,
                            'profile_image' => $data->profile_image
                        ];
                    }
                    $currentUserID = $request->user_id;
                    usort($likeWithData, function($a, $b) use ($currentUserID) {
                        if ($a['user_id'] == $currentUserID) { return -1;
                        } elseif ($b['user_id'] == $currentUserID) { return 1;
                        } else { return 0; }
                    });
                }else{ $likeWithData = []; }
                $commentData = ChallengePostComment::join('users','users.id','=','user_id')
                ->select('challenge_post_comments.id as comment_id','challenge_post_comments.challenge_comment_date','challenge_post_comments.challenge_id','challenge_post_comments.post_id','challenge_post_comments.comment','users.id as user_id','users.first_name','users.last_name','users.profile_image')
                ->where(['challenge_id'=>$request->challenge_id,'post_id'=>$posts->post_id])->get();
                if($commentData->isNotEmpty()){
                    foreach($commentData as $data){
                        if($data->user_id == $request->user_id){ $userName = 'You';
                        }else{ $userName = $data->first_name." ".$data->last_name; }
                        $commentWithData[] = [
                            'comment_id' => $data->comment_id,
                            'challenge_comment_date' => Carbon::parse($data->challenge_comment_date)->diffForHumans(),
                            'challenge_id' => $data->challenge_id,
                            'post_id' => $data->post_id,
                            'user_id' => $data->user_id,
                            'comment' => $data->comment,
                            'name' => $userName,
                            'profile_image' => $data->profile_image
                        ];
                    }
                    $currentUserID = $request->user_id;
                    usort($commentWithData, function($a, $b) use ($currentUserID) {
                        if ($a['user_id'] == $currentUserID) { return -1;
                        } elseif ($b['user_id'] == $currentUserID) { return 1;
                        } else { return 0; }
                    });
                }else{ $commentWithData = []; }
                $postData = [
                    'user_id' => $posts->user_id,
                    'first_name' => $posts->first_name,
                    'last_name' => $posts->last_name,
                    'profile_image' => $posts->profile_image,
                    'post_id' => $posts->post_id,
                    'post_caption' => $posts->post_caption,
                    'post_image' => $posts->post_image,
                    'like_count' => $likeData->isNotEmpty() ? count($likeData) : 0,
                    'comment_count' => $commentData->isNotEmpty() ? count($commentData) : 0,
                    'like_list' => $likeWithData,
                    'comment_list' => $commentWithData,
                    'like_status' => $likedData
                ];
                return response()->json(['status'=> 200,'data'=>['posts'=>$postData],'message'=>'Challenges Data Fetched Successfully']);
            }else{
                return response()->json(['status'=> 200,'data'=>[],'message'=>'Challenge Post Data Not Found']);
            }
        }else{
            return response()->json(['status'=> 200,'data'=>[],'message'=>'Challenge Not Found']);
        }
    }

    public function joined_members_list(Request $request){
        $request->validate(['challenge_id' => 'required']);
        $joinedMembers = UserJoinChallenge::join('users','users.id','=','user_id')->where('challenge_id',$request->challenge_id)->get();
        if($joinedMembers->isNotEmpty()){
            foreach($joinedMembers as $data){
                $following = FriendsFollowing::where(['user_id'=>$request->user_id,'friend_user_id'=>$data->user_id])->first();
                if($following){
                    if($following->following_status == 'requested'){$follow_status = 1;}else{$follow_status = 2;}
                }else{
                    $follower = FriendsFollowers::where(['user_id'=>$request->user_id,'friend_user_id'=>$data->user_id])->first();
                    if($follower){
                        if($follower->following_status == 'pending'){$follow_status = 1;}else{$follow_status = 2;}
                    }else{
                        $follow_status = 0;
                    }
                }
                $joinedMembersList[] = [
                    'user_id' => $request->user_id,
                    'joined_member_id' => $data->user_id,
                    'first_name' => $data->first_name,
                    'last_name' => $data->last_name,
                    'profile_image' => $data->profile_image,
                    'follow_status' => $follow_status,
                ];
            }
            return response()->json(['status'=> 200,'data'=>['list'=>$joinedMembersList],'message'=>'Challenges Joined Members List Fetched Successfully']);
        }else{
            return response()->json(['status'=> 200,'data'=>[],'message'=>'Data Not Found']);
        }
    }

    public function delete_member(Request $request){
        $request->validate(['member_id'=>'required','challenge_id'=>'required']);
        $challenge = Challenges::find($request->challenge_id);
        if($challenge){
            UserJoinChallenge::where(['user_id'=>$request->member_id,'challenge_id'=>$request->challenge_id])->delete();
            return response()->json(['status'=> 200,'data'=>[],'message'=>'Members Deleted Successfully']);
        }else{
            return response()->json(['status'=> 200,'data'=>[],'message'=>'Challenge Not Found']);
        }
    }

    public function complete_challenge(Request $request){
        $request->validate(['challenge_id'=>'required']);
        $challenge = Challenges::find($request->challenge_id);
        if($challenge){
            if($challenge->consistency_count == 0){
                $challenge->update(['consistency_count' => 1]);
            }else{
                $challenge->update(['consistency_count' => $challenge->consistency_count+1]);
            }
            $request->merge(['challenge_status' => 'completed']);
            CompleteChallenge::create($request->all());
            return response()->json(['status'=> 200,'data'=>[],'message'=>'Challenge Completed Successfully']);
        }else{
            return response()->json(['status'=> 200,'data'=>[],'message'=>'Challenge Data Not Found']);
        }
    }

    public function reminder_push_notification(){
        $time = Carbon::now()->format('H:i A');
        // $JoinedUsers = UserJoinChallenge::where('challenge_time',$time)->get();
        $JoinedUsers = UserJoinChallenge::all();
        echo $time."\n";
        if($JoinedUsers->isNotEmpty()){
            foreach($JoinedUsers as $data){
                $UserData = User::find($data->user_id);
                $ChallengesData = Challenges::find($data->challenge_id);
                if(!empty($UserData->fcm_token)){
                    $url = 'https://fcm.googleapis.com/fcm/send';
                    $data_new = [
                        "registration_ids" => [$UserData->fcm_token],
                        "notification" => [
                            "title" => "Challenge Reminder Notification",
                            "body" => "Your ".$ChallengesData->challenges_title." started! Get ready to dive in and conquer your goals."
                        ]
                    ];
                    $encodedData = json_encode($data_new);
                    $headers = ['Authorization:key=' . env('FCM_SERVER_KEY'),'Content-Type: application/json'];
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
                    $result = curl_exec($ch);
                    echo "====================="."\n";
                    if ($result === FALSE) {die('Curl failed: ' . curl_error($ch));}
                    curl_close($ch);
                }
            }
            return response()->json(['status'=> 200,'data'=>[],'message'=>'Reminder Send Successfully']);
        }else{
            return response()->json(['status'=> 200,'data'=>[],'message'=>'Data Not Found']);
        }
    }
}
