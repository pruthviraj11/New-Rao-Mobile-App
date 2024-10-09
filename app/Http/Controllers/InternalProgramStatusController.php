<?php

namespace App\Http\Controllers;
use App\Models\NewsCategory;
use App\Services\InternalProgramStatusService;
use Illuminate\Http\Request;
use App\Http\Requests\InternalProgramStatus\CreateInternalProgramStatusRequest;
use App\Http\Requests\InternalProgramStatus\UpdateInternalProgramStatusRequest;
use App\Models\ClientType;
use \Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

class InternalProgramStatusController extends Controller
{
    protected $InternalProgramStatusService;

    public function __construct(InternalProgramStatusService $internalProgramStatus)
    {
        $this->InternalProgramStatusService = $internalProgramStatus;
        $this->middleware('permission:internal-program-statuses-list|internal-program-statuses-create|internal-program-statuses-edit|internal-program-statuses-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:internal-program-statuses-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:internal-program-statuses-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:internal-program-statuses-delete', ['only' => ['destroy']]);

        // Permission::create(['name' => 'internal-program-statuses-list', 'guard_name' => 'web', 'module_name' => 'Internal Program Status']);
        // Permission::create(['name' => 'internal-program-statuses-create', 'guard_name' => 'web', 'module_name' => 'Internal Program Status']);
        // Permission::create(['name' => 'internal-program-statuses-edit', 'guard_name' => 'web', 'module_name' => 'Internal Program Status']);
        // Permission::create(['name' => 'internal-program-statuses-delete', 'guard_name' => 'web', 'module_name' => 'Internal Program Status']);

    }
    public function index()
    {
        return view('content/apps/InternalProgramStatus/list');
    }

    public function create()
    {
        $internalProgramStatus = "";
        $page_data['page_title'] = "internal Program Status Add";
        $page_data['form_title'] = "Add New internal Program Status";
        $ClientType = ClientType::where('status', '1')->get();
        return view('/content/apps/InternalProgramStatus/create-edit', compact('page_data', 'internalProgramStatus', 'ClientType'));
    }
    public function getAll()
    {
        $internalProgramStatus = $this->InternalProgramStatusService->getAllinternalProgramStatus();
        return DataTables::of($internalProgramStatus)
            ->addColumn('client_type', function ($row) {
                return $row->clientType ? $row->clientType->displayname : 'N/A';
            })
            ->addColumn('actions', function ($row) {
                $encryptedId = encrypt($row->id);
                // Update Button
                $updateButton = "<a class='btn btn-warning btn-sm ' href='" . route('app-internal-program-statuses-edit', $encryptedId) . "'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"feather feather-edit ficon\"><path d=\"M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7\"></path><path d=\"M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z\"></path></svg> </a>";

                // Delete Button
                $deleteButton = "<a class='btn btn-danger btn-sm mx-1  confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-internal-program-statuses-delete', $encryptedId) . "'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"feather feather-trash-2 ficon\"><polyline points=\"3 6 5 6 21 6\"></polyline><path d=\"M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2\"></path><line x1=\"10\" y1=\"11\" x2=\"10\" y2=\"17\"></line><line x1=\"14\" y1=\"11\" x2=\"14\" y2=\"17\"></line></svg> </a>";
                $buttons = $updateButton . " " . $deleteButton;
                return "<div class='d-flex justify-content-start'>" . $buttons . "</div>";
            })
            ->rawColumns(['actions'])->make(true);


    }
    /**
     * Search slider user data
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateInternalProgramStatusRequest $request)
    {
        try {
            $internalProgramStatusData['name'] = $request->get('name');
            $internalProgramStatusData['description'] = $request->get('description');
            $internalProgramStatusData['order'] = $request->get('order');
            $internalProgramStatusData['category_id'] = $request->get('category_id');
            $internalProgramStatusData['status'] = $request->get('status') === 'on' ? 1 : 0;

            $internalProgramStatus = $this->InternalProgramStatusService->create($internalProgramStatusData);
            if (!empty($internalProgramStatus)) {
                return redirect()->route('app-internal-program-statuses-list')->with('success', 'internal Program Status Added Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Adding internal Program Status');
            }
        } catch (\Exception $error) {
            dd($error->getMessage());
            return redirect()->route('app-internal-program-statuses-list')->with('error', 'Error while editing internal Program Status');
        }

    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $encrypted_id
     * @return \Illuminate\Http\Response
     */
    public function edit($encrypted_id)
    {

        try {
            $id = decrypt($encrypted_id);
            $internalProgramStatus = $this->InternalProgramStatusService->getinternalProgramStatus($id);
            $page_data['page_title'] = "internal Program Status Edit";
            $page_data['form_title'] = "Edit internal Program Status";
            $ClientType = ClientType::where('status', '1')->get();
            return view('content/apps/InternalProgramStatus/create-edit', compact('page_data', 'internalProgramStatus', 'ClientType'));
        } catch (\Exception $error) {
            return redirect()->route("app/InternalProgramStatus/list")->with('error', 'Error while editing internal Program Statuss');
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $encrypted_id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateInternalProgramStatusRequest $request, $encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);

            $internalProgramStatusData['name'] = $request->get('name');
            $internalProgramStatusData['description'] = $request->get('description');
            $internalProgramStatusData['order'] = $request->get('order');
            $internalProgramStatusData['status'] = $request->get('status') === 'on' ? 1 : 0;
            $internalProgramStatusData['category_id'] = $request->get('category_id');
            $updated = $this->InternalProgramStatusService->updateinternalProgramStatus($id, $internalProgramStatusData);
            if (!empty($updated)) {
                return redirect()->route("app-internal-program-statuses-list")->with('success', 'internal Program Statuss Updated Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Updating internal Program Statuss');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-internal-program-statuses-list")->with('error', 'Error while editing internal Program Statuss');
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $encrypted_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($encrypted_id = '')
    {

        try {
            $id = decrypt($encrypted_id);
            $deleted = $this->InternalProgramStatusService->deleteinternalProgramStatus($id);
            if (!empty($deleted)) {
                return redirect()->route("app-internal-program-statuses-list")->with('success', 'internal Program Statuss Deleted Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Deleting internal Program Statuss');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-internal-program-statuses-list")->with('error', 'Error while editing internal Program Statuss');
        }
    }


}
