<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\MobileApp\BulkEmailSend;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

class EmailSendsController extends Controller
{
    public function user_list_for_emails(){
        $users = User::all();
        return view('Admin.emailsends.userlistsforemails',compact('users'));
    }

    public function sent_now_emails(){
        return view('Admin.emailsends.emailFormat');
    }

    public function send_email_with_data(Request $request){
        $email_to = explode(',',$request->email_to);
        foreach($email_to as $emailAddress){
            $user = User::where('email',$emailAddress)->first();
            $data = [
                'name' => ucfirst($user->first_name)." ".ucfirst($user->last_name),
                'subject' => $request->email_subject,
                'body' => $request->email_body,
            ];
            Mail::to($emailAddress)->send(new BulkEmailSend($data));
        }

        flash()->addSuccess('Emails Sends Successfully');
        return redirect()->route('user_list_for_emails');

        //Artisan::call('queue:work', ['--tries' => 3]);
        // $apiKey = '736335223801c16c63dd2a8dd6b57603-us21';
        // $listId = '837d23441b';
        // // $email = 'akshayjadhav9669@gmail.com';
        // // $url = "https://us21.api.mailchimp.com/3.0/lists/$listId/members";
        // // $data = ['email_address' => $email,'status' => 'subscribed'];
        // // $jsonData = json_encode($data);
        // // $ch = curl_init($url);
        // // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // // curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json','Authorization: Basic ' . $apiKey]);
        // // curl_setopt($ch, CURLOPT_POST, 1);
        // // curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        // // $response = curl_exec($ch);
        // // $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // // curl_close($ch);
        // // print_r($response);
        // // if ($statusCode === 200 || $statusCode === 204) {
        // //     return response()->json(['message' => 'Subscribed successfully']);
        // // } else {
        // //     return response()->json(['message' => 'Subscription failed'], $statusCode);
        // // }

        // $subject = "Your Email Subject";
        // $htmlContent = "<p>Your email content goes here.</p>";

        // $members = ['akshayjadhav9669@gmail.com'];

        // foreach ($members as $email) {
        //     $data = [
        //         'message' => [
        //             'subject' => $subject,
        //             'html' => $htmlContent,
        //         ],
        //         'recipients' => [
        //             'list_id' => $listId,
        //             'to_email' => $email,
        //         ],
        //     ];

        //     $json_data = json_encode($data);

        //     $ch = curl_init();
        //     curl_setopt($ch, CURLOPT_URL, "https://us21.api.mailchimp.com/3.0/campaigns");
        //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //     curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        //     curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json','Authorization: Basic ' . $apiKey]);

        //     $response = curl_exec($ch);
        //     $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //     print_r(json_encode($response));
        //     die;
        //     if ($http_code == 200 || $http_code == 201) {

        //     } else {
        //         // Handle error
        //         $error = curl_error($ch);
        //     }

        //     curl_close($ch);
        // }

        // // Return a response
        // return response()->json(['message' => 'Emails sent to subscribers']);
    }
}
