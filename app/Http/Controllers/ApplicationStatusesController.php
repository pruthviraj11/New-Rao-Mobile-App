<?php

namespace App\Http\Controllers;
use App\Models\NewsCategory;
use App\Services\ApplicationStatusesService;
use Illuminate\Http\Request;
use App\Http\Requests\ApplicationStatuses\CreateApplicationStatusesRequest;
use App\Http\Requests\ApplicationStatuses\UpdateApplicationStatusesRequest;
use App\Models\ClientType;
use \Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

class ApplicationStatusesController extends Controller
{
    protected $applicationStatusesService;

    public function __construct(ApplicationStatusesService $applicationStatuses)
    {
        $this->applicationStatusesService = $applicationStatuses;
        $this->middleware('permission:application-statuses-list|application-statuses-create|application-statuses-edit|application-statuses-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:application-statuses-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:application-statuses-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:application-statuses-delete', ['only' => ['destroy']]);

        // Permission::create(['name' => 'application-statuses-list', 'guard_name' => 'web', 'module_name' => 'Application Statuses']);
        // Permission::create(['name' => 'application-statuses-create', 'guard_name' => 'web', 'module_name' => 'Application Statuses']);
        // Permission::create(['name' => 'application-statuses-edit', 'guard_name' => 'web', 'module_name' => 'Application Statuses']);
        // Permission::create(['name' => 'application-statuses-delete', 'guard_name' => 'web', 'module_name' => 'Application Statuses']);

    }

    public function index()
    {
        return view('content/apps/ApplicationStatuses/list');
    }

    public function create()
    {
        $applicationStatuses = "";
        $page_data['page_title'] = "Application Statuses Add";
        $page_data['form_title'] = "Add New application statuses";
        $ClientType = ClientType::where('status', '1')->get();
        return view('/content/apps/ApplicationStatuses/create-edit', compact('page_data', 'applicationStatuses', 'ClientType'));
    }


    public function getAll()
    {
        $applicationStatuses = $this->applicationStatusesService->getAllApplicationStatuses();
        return DataTables::of($applicationStatuses)
            ->addColumn('client_type', function ($row) {
                return $row->clientType ? $row->clientType->displayname : 'N/A';
            })
            ->addColumn('actions', function ($row) {
                $encryptedId = encrypt($row->id);
                // Update Button
                $updateButton = "<a class='btn btn-warning btn-sm ' href='" . route('app-application-statuses-edit', $encryptedId) . "'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"feather feather-edit ficon\"><path d=\"M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7\"></path><path d=\"M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z\"></path></svg> </a>";

                // Delete Button
                $deleteButton = "<a class='btn btn-danger btn-sm  confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-application-statuses-delete', $encryptedId) . "'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"feather feather-trash-2 ficon\"><polyline points=\"3 6 5 6 21 6\"></polyline><path d=\"M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2\"></path><line x1=\"10\" y1=\"11\" x2=\"10\" y2=\"17\"></line><line x1=\"14\" y1=\"11\" x2=\"14\" y2=\"17\"></line></svg> </a>";
                return $updateButton . " " . $deleteButton;
            })
            ->rawColumns(['actions'])->make(true);


    }


    public function store(CreateApplicationStatusesRequest $request)
    {
        try {
            $applicationStatusesData['name'] = $request->get('name');
            $applicationStatusesData['description'] = $request->get('description');
            $applicationStatusesData['order'] = $request->get('order');
            $applicationStatusesData['category_id'] = $request->get('category');
            $applicationStatusesData['status'] = $request->get('status') === 'on' ? 1 : 0;

            $applicationStatuses = $this->applicationStatusesService->create($applicationStatusesData);
            if (!empty($applicationStatuses)) {
                return redirect()->route('app-application-statuses-list')->with('success', 'Application Statuses Added Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Adding Application Statuses');
            }
        } catch (\Exception $error) {
            dd($error->getMessage());
            return redirect()->route('app-application-statuses-list')->with('error', 'Error while editing Application Statuses');
        }

    }

    public function edit($encrypted_id)
    {

        try {
            $id = decrypt($encrypted_id);
            $applicationStatuses = $this->applicationStatusesService->getApplicationStatuses($id);
            $page_data['page_title'] = "Application Statuses Edit";
            $page_data['form_title'] = "Edit Application Statuses";
            $ClientType = ClientType::where('status', '1')->get();
            return view('content/apps/ApplicationStatuses/create-edit', compact('page_data', 'applicationStatuses', 'ClientType'));
        } catch (\Exception $error) {
            return redirect()->route("app/application-statuses/list")->with('error', 'Error while editing Applicaion Statuses');
        }
    }

    public function update(UpdateApplicationStatusesRequest $request, $encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);

            $applicationStatuses = $this->applicationStatusesService->getApplicationStatuses($id);
            $applicationStatusesData['name'] = $request->get('name');
            $applicationStatusesData['description'] = $request->get('description');
            $applicationStatusesData['order'] = $request->get('order');
            $applicationStatusesData['category_id'] = $request->get('category');
            $applicationStatusesData['status'] = $request->get('status') === 'on' ? 1 : 0;
            $updated = $this->applicationStatusesService->updateApplicationStatuses($id, $applicationStatusesData);
            if (!empty($updated)) {
                return redirect()->route("app-application-statuses-list")->with('success', 'Application Statuses Updated Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Updating Application Statuses');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-application-statuses-list")->with('error', 'Error while editing Application Statuses');
        }
    }

    public function destroy($encrypted_id = '')
    {

        try {
            $id = decrypt($encrypted_id);
            $deleted = $this->applicationStatusesService->deleteApplicationStatuses($id);
            if (!empty($deleted)) {
                return redirect()->route("app-application-statuses-list")->with('success', 'Application Statuses Deleted Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Deleting Application Statuses');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-application-statuses-list")->with('error', 'Error while editing Application Statuses');
        }
    }
}
