<?php

namespace App\Http\Controllers;

use DataTables;
use Carbon\Carbon;
use Google\Client;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class NotificationController extends Controller
{
    public function sendNotificationPage()
    {
        return view("backend.push_notification.send");
    }

    public function sendPushNotification(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'topic' => 'nullable|url|max:255',
        ]);

        $title = $request->title;
        $body = $request->description;
        $topic_url = $request->topic ?? url("/");

        $tokens = DB::table('fcm_tokens')->pluck('token');

        if ($tokens->isEmpty()) {
            Toastr::error('No FCM tokens found.', 'Error');
            return back();
        }

        $serviceAccountPath = storage_path('app/firebase/firebase-service-account.json');
        $client = new Client();
        $client->setAuthConfig($serviceAccountPath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $accessToken = $client->fetchAccessTokenWithAssertion()['access_token'];

        $projectId = json_decode(file_get_contents($serviceAccountPath), true)['project_id'];
        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        foreach ($tokens as $token) {
            $message = [
                "message" => [
                    "token" => $token,
                    "data" => [
                        "title" => $title,
                        "body" => $body,
                        "icon" => url('logo.jpg'),
                        "link" => $topic_url
                    ],

                    "webpush" => [
                        "fcm_options" => [
                            "link" => $topic_url,
                        ]
                    ]
                ]
            ];

              Notification::insert([
                'title' => $title,
                'description' => $body,
                'created_at' => Carbon::now()
        ]);


            $response = Http::withToken($accessToken)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, $message);
        }

        

        Toastr::success('Notifications sent Succesffully.', 'Successful');
        return back();
    }

    public function viewAllNotifications(Request $request)
    {
        if ($request->ajax()) {

            $data = Notification::orderBy('id', 'desc')->get();

            return Datatables::of($data)
                ->editColumn('created_at', function ($data) {
                    return date("Y-m-d h:i:s a", strtotime($data->created_at));
                })
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $data->id . '" data-original-title="Delete" class="btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('backend.push_notification.view');
    }

    public function deleteNotification($id)
    {
        Notification::where('id', $id)->delete();
        return response()->json(['success' => 'Notification Deleted Successfully.']);
    }

    public function deleteNotificationRangeWise()
    {

        $currentDate = date("Y-m-d H:i:s");
        $prevDate = date('Y-m-d', strtotime('-15 day', strtotime($currentDate)));
        Notification::where('created_at', '<=', $prevDate)->delete();
        Toastr::error('Notifications are Deleted', 'Successful');
        return back();
    }
}
