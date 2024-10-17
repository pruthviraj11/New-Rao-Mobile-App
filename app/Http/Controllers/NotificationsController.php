<?php

namespace App\Http\Controllers;
use App\Models\NewsCategory;
use App\Models\User;
use App\Services\NotificationsService;
use Illuminate\Http\Request;
use App\Http\Requests\Notifications\CreateNotificationsRequest;
// use App\Http\Requests\Notifications\UpdateDrawsRequest;
use App\Models\ClientType;
use \Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use App\Models\PushNotificationUser;

class NotificationsController extends Controller
{

    protected $notificationsService;

    public function __construct(NotificationsService $notificationsService)
    {
        $this->notificationsService = $notificationsService;
        $this->middleware('permission:notifications-list|notifications-create|notifications-edit|notifications-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:notifications-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:notifications-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:notifications-delete', ['only' => ['destroy']]);

        // Permission::create(['name' => 'notifications-list', 'guard_name' => 'web', 'module_name' => 'Notifications']);
        // Permission::create(['name' => 'notifications-create', 'guard_name' => 'web', 'module_name' => 'Notifications']);
        // Permission::create(['name' => 'notifications-edit', 'guard_name' => 'web', 'module_name' => 'Notifications']);
        // Permission::create(['name' => 'notifications-delete', 'guard_name' => 'web', 'module_name' => 'Notifications']);

    }

    public function index()
    {
        return view('content/apps/Notifications/list');
    }

    public function create()
    {
        $notifications = "";
        $page_data['page_title'] = "Notifications Add";
        $page_data['form_title'] = "Add New Notifications";
        $ClientType = ClientType::where('status', '1')->get();
        $users = User::get();
        return view('/content/apps/Notifications/create-edit', compact('page_data', 'notifications', 'ClientType', 'users'));
    }

    public function getAll()
    {
        $notifications = $this->notificationsService->getAllNotifications();
        return DataTables::of($notifications)
            ->addColumn('client_type', function ($row) {
                return $row->clientType ? $row->clientType->displayname : 'N/A';
            })
            ->addColumn('actions', function ($row) {
                $encryptedId = encrypt($row->id);
                // Update Button
                $updateButton = "<a class='btn btn-warning btn-sm ' href='" . route('app-notifications-edit', $encryptedId) . "'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"feather feather-edit ficon\"><path d=\"M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7\"></path><path d=\"M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z\"></path></svg> </a>";

                // Delete Button
                $deleteButton = "<a class='btn btn-danger btn-sm mx-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-notifications-delete', $encryptedId) . "'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"feather feather-trash-2 ficon\"><polyline points=\"3 6 5 6 21 6\"></polyline><path d=\"M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2\"></path><line x1=\"10\" y1=\"11\" x2=\"10\" y2=\"17\"></line><line x1=\"14\" y1=\"11\" x2=\"14\" y2=\"17\"></line></svg> </a>";
                $buttons = $updateButton . " " . $deleteButton;
                return "<div class='d-flex justify-content-start'>" . $buttons . "</div>";
            })
            ->rawColumns(['actions'])->make(true);
    }
    // public function store(CreateNotificationsRequest $request)
    // {
    //     try {
    //         $notificationsData['title'] = $request->get('title');
    //         $notificationsData['message'] = $request->get('message');
    //         $userIds = $request->get('user_id'); // Check if this is an array
    //         $notificationsData['type'] = $request->get('client_type');

    //         if (!is_array($userIds)) {
    //             $userIds = [$userIds];
    //         }

    //         foreach ($userIds as $userId) {
    //             $notificationsData['user_id'] = $userId;
    //             $draws = $this->notificationsService->create($notificationsData);
    //             if (!$draws) {
    //                 throw new \Exception('Error while creating notification for user ID: ' . $userId);
    //             }
    //         }


    //         $data = [
    //             'notification_title' => $notificationsData['title'],
    //             'notification_message_body' => $notificationsData['message'],
    //             'notification_type' => $notificationsData['type'],
    //         ];

    //         if ($request->has('user_id')) {
    //             $users = User::whereIn('id', $request->get('user_id'))->get();
    //         }

    //         foreach ($users as $user) {
    //             $userData = $data;
    //             $userData['user_id'] = $user->id;

    //             $this->sendPushNotification($userData, $user->device_token);
    //         }

    //         if (!empty($draws)) {
    //             return redirect()->route('app-notifications-list')->with('success', 'Notifications Added Successfully');
    //         } else {
    //             return redirect()->back()->with('error', 'Error while Adding Notifications');
    //         }
    //     } catch (\Exception $error) {
    //         dd($error->getMessage());
    //         return redirect()->route('app-notifications-list')->with('error', 'Error while editing Notifications');
    //     }
    // }



    public function store(CreateNotificationsRequest $request)
    {
        try {
            // Validate input data
            $validatedData = $request->validate([
                'title' => 'required|string',
                'message' => 'required|string',
                'user_id' => 'array|required',
                'client_type' => 'required|string',
            ]);

            // Create a single notification entry
            $notificationsData = [
                'title' => $validatedData['title'],
                'message' => $validatedData['message'],
                'type' => $validatedData['client_type'],
            ];

            $notification = $this->notificationsService->create($notificationsData);

            // Get selected users from the request
            $userIds = $validatedData['user_id'];

            if (in_array('all', $userIds)) {
                // Fetch all users based on the client type with pagination
                $users = User::where('user_category', $validatedData['client_type'])->paginate(100); // You can adjust the batch size

                foreach ($users as $user) {
                    // Insert entries into push_notification_user table for each user
                    PushNotificationUser::create([
                        'push_notification_id' => $notification->id,
                        'user_id' => $user->id,
                    ]);

                    // Prepare data to send push notification
                    $notificationData = [
                        'notification_title' => $notificationsData['title'],
                        'notification_message_body' => $notificationsData['message'],
                        'notification_type' => $notificationsData['type'],
                        'user_id' => $user->id,
                    ];

                    if ($user->device_token) {
                        $this->sendPushNotification($notificationData, $user->device_token);
                    }
                }
            } else {
                // Handle normal user selection
                $users = User::whereIn('id', $userIds)->get();

                foreach ($users as $user) {
                    // Insert entries into push_notification_user table for each user
                    PushNotificationUser::create([
                        'push_notification_id' => $notification->id,
                        'user_id' => $user->id,
                    ]);

                    // Prepare data to send push notification
                    $notificationData = [
                        'notification_title' => $notificationsData['title'],
                        'notification_message_body' => $notificationsData['message'],
                        'notification_type' => $notificationsData['type'],
                        'user_id' => $user->id,
                    ];

                    if ($user->device_token) {
                        $this->sendPushNotification($notificationData, $user->device_token);
                    }
                }
            }

            return redirect()->route('app-notifications-list')->with('success', 'Notifications Added Successfully');
        } catch (\Exception $error) {
            // Handle error
            return redirect()->route('app-notifications-list')->with('error', 'Error while adding Notifications: ' . $error->getMessage());
        }
    }

    public function getUsersByClientType($clientTypeId)
    {
        $users = User::where('user_category', $clientTypeId)->get(['id', 'name']);
        return response()->json($users);
    }

    public function sendPushNotification($data, $deviceToken)
    {
        try {

            $messaging = app('firebase.messaging');
            $message = CloudMessage::withTarget('token', $deviceToken)
                ->withNotification(Notification::create($data['notification_title'], $data['notification_message_body']))
                ->withData(['custom_key' => 'custom_value']);

            $response = $messaging->send($message);

            Log::info('Push notification sent successfully to device with token: ' . $deviceToken);
            Log::info('Firebase response: ' . json_encode($response));

        } catch (\Exception $e) {
            Log::error('Error sending push notification: ' . $e->getMessage());
        }
    }
    
}
