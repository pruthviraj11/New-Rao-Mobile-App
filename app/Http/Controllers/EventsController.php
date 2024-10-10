<?php

namespace App\Http\Controllers;
use App\Models\NewsCategory;
use App\Services\EventService;
use Illuminate\Http\Request;
use App\Http\Requests\Events\CreateEventsRequest;
use App\Http\Requests\Events\UpdateEventsRequest;
use App\Models\ClientType;
use \Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

class EventsController extends Controller
{

    protected $eventsService;


    public function __construct(EventService $events)
    {
        $this->eventsService = $events;
        $this->middleware('permission:events-list|events-create|events-edit|events-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:events-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:events-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:events-delete', ['only' => ['destroy']]);

        // Permission::create(['name' => 'events-list', 'guard_name' => 'web', 'module_name' => 'Events']);
        // Permission::create(['name' => 'events-create', 'guard_name' => 'web', 'module_name' => 'Events']);
        // Permission::create(['name' => 'events-edit', 'guard_name' => 'web', 'module_name' => 'Events']);
        // Permission::create(['name' => 'events-delete', 'guard_name' => 'web', 'module_name' => 'Events']);

    }

    public function index()
    {
        return view('content/apps/Events/list');
    }

    public function create()
    {
        $events = "";
        $page_data['page_title'] = "Events Add";
        $page_data['form_title'] = "Add New Event";
        $ClientType = ClientType::where('status', '1')->get();
        return view('/content/apps/Events/create-edit', compact('page_data', 'events', 'ClientType'));
    }

    public function getAll()
    {
        $events = $this->eventsService->getAllEvents();
        return DataTables::of($events)
            ->addColumn('client_type', function ($row) {
                return $row->clientType ? $row->clientType->displayname : 'N/A';
            })
            ->addColumn('actions', function ($row) {
                $encryptedId = encrypt($row->id);
                // Update Button
                $updateButton = "<a class='btn btn-warning btn-sm ' href='" . route('app-events-edit', $encryptedId) . "'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"feather feather-edit ficon\"><path d=\"M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7\"></path><path d=\"M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z\"></path></svg> </a>";

                // Delete Button
                $deleteButton = "<a class='btn btn-danger btn-sm mx-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-events-delete', $encryptedId) . "'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"feather feather-trash-2 ficon\"><polyline points=\"3 6 5 6 21 6\"></polyline><path d=\"M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2\"></path><line x1=\"10\" y1=\"11\" x2=\"10\" y2=\"17\"></line><line x1=\"14\" y1=\"11\" x2=\"14\" y2=\"17\"></line></svg> </a>";
                $buttons = $updateButton . " " . $deleteButton;
                return "<div class='d-flex justify-content-start'>" . $buttons . "</div>";
            })
            ->rawColumns(['actions'])->make(true);
    }


    public function store(CreateEventsRequest $request)
    {
        try {
            $eventsData['title'] = $request->get('title');

            $eventsData['date'] = $request->get('date');
            if ($request->hasFile('image')) {
                $originalName = $request->file('image')->getClientOriginalName();
                $filename = str_replace(' ', '_', $originalName);
                $imagePath = $request->file('image')->storeAs('Events', $filename, 'public');
                $eventsData['image'] = $imagePath;
            }
            $eventsData['status'] = $request->get('status') === 'on' ? 1 : 0;

            $events = $this->eventsService->create($eventsData);
            if (!empty($events)) {
                return redirect()->route('app-events-list')->with('success', 'Events Added Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Adding Events');
            }
        } catch (\Exception $error) {
            dd($error->getMessage());
            return redirect()->route('app-events-list')->with('error', 'Error while editing Events');
        }

    }

    public function edit($encrypted_id)
    {
        try {

            $id = decrypt($encrypted_id);
            $events = $this->eventsService->getEvents($id);
            $page_data['page_title'] = "Events Edit";
            $page_data['form_title'] = "Edit Event";
            $ClientType = ClientType::where('status', '1')->get();
            return view('content/apps/Events/create-edit', compact('page_data', 'events', 'ClientType'));
        } catch (\Exception $error) {
            return redirect()->route("app/Events/list")->with('error', 'Error while editing Event');
        }
    }

    public function update(UpdateEventsRequest $request, $encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);

            $events = $this->eventsService->getEvents($id);
            $eventsData['title'] = $request->get('title');

            $eventsData['date'] = $request->get('date');

            if ($request->hasFile('image')) {
                if ($events->image) {
                    Storage::disk('public')->delete($events->image);
                }

                $originalName = $request->file('image')->getClientOriginalName();
                $filename = str_replace(' ', '_', $originalName);
                $imagePath = $request->file('image')->storeAs('Events', $filename, 'public');
                $eventsData['image'] = $imagePath;
            }

            $eventsData['status'] = $request->get('status') === 'on' ? 1 : 0;


            $updated = $this->eventsService->updateEvents($id, $eventsData);
            if (!empty($updated)) {
                return redirect()->route("app-events-list")->with('success', 'Events Updated Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Updating Events');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-events-list")->with('error', 'Error while editing Events');
        }
    }

    public function destroy($encrypted_id = '')
    {

        try {
            $id = decrypt($encrypted_id);
            $deleted = $this->eventsService->deleteEvents($id);
            if (!empty($deleted)) {
                return redirect()->route("app-events-list")->with('success', 'Events Deleted Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Deleting Events');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-events-list")->with('error', 'Error while Deleting Events');
        }
    }
}
