<?php

namespace App\Http\Controllers;
use App\Models\Slide;
use App\Services\ClientTypeService;
use Illuminate\Http\Request;
use App\Http\Requests\ClientType\CreateClientTypeRequest;
use App\Http\Requests\ClientType\UpdateClientTypeRequest;
use \Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

class ClientTypeController extends Controller
{
    protected $clientTypeService;

    public function __construct(ClientTypeService $clientType)
    {
        $this->clientTypeService = $clientType;
        $this->middleware('permission:client-types-list|client-types-create|client-types-edit|client-types-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:client-types-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:client-types-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:client-types-delete', ['only' => ['destroy']]);

        // Permission::create(['name' => 'client-types-list', 'guard_name' => 'web', 'module_name' => 'Client Type']);
        // Permission::create(['name' => 'client-types-create', 'guard_name' => 'web', 'module_name' => 'Client Type']);
        // Permission::create(['name' => 'client-types-edit', 'guard_name' => 'web', 'module_name' => 'Client Type']);
        // Permission::create(['name' => 'client-types-delete', 'guard_name' => 'web', 'module_name' => 'Client Type']);

    }
    public function index()
    {
        return view('content/apps/client-types/list');
    }

    public function create()
    {
        $clientType = "";
        $page_data['page_title'] = "Client Type Add";
        $page_data['form_title'] = "Add New Client Type";
        return view('/content/apps/client-types/create-edit', compact('page_data', 'clientType'));
    }
    public function getAll()
    {
        $clientType = $this->clientTypeService->getAllClientType();
        return DataTables::of($clientType)

            ->addColumn('actions', function ($row) {
                $encryptedId = encrypt($row->id);
                // Update Button
                $updateButton = "<a class='btn btn-warning btn-sm ' href='" . route('app-client-types-edit', $encryptedId) . "'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"feather feather-edit ficon\"><path d=\"M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7\"></path><path d=\"M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z\"></path></svg> </a>";

                // Delete Button
                $deleteButton = "<a class='btn btn-danger btn-sm  confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-client-types-delete', $encryptedId) . "'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"feather feather-trash-2 ficon\"><polyline points=\"3 6 5 6 21 6\"></polyline><path d=\"M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2\"></path><line x1=\"10\" y1=\"11\" x2=\"10\" y2=\"17\"></line><line x1=\"14\" y1=\"11\" x2=\"14\" y2=\"17\"></line></svg> </a>";
                return $updateButton . " " . $deleteButton;
            })
            ->rawColumns(['actions'])->make(true);


    }
    /**
     * Search slider user data
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateClientTypeRequest $request)
    {
        try {
            $clientTypeData['name'] = $request->get('name');
            $clientTypeData['displayname'] = $request->get('displayname');
            $clientTypeData['status'] = $request->get('status') === 'on' ? 1 : 0;
            $clientTypeData['created_by'] = auth()->id();

            $clientType = $this->clientTypeService->create($clientTypeData);
            if (!empty($clientType)) {
                return redirect()->route('app-client-types-list')->with('success', 'ClientType Added Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Adding ClientType');
            }
        } catch (\Exception $error) {
            dd($error->getMessage());
            return redirect()->route('app-client-types-list')->with('error', 'Error while editing ClientType');
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
            $clientType = $this->clientTypeService->getClientType($id);
            $page_data['page_title'] = "Client Type Edit";
            $page_data['form_title'] = "Edit Client Type";
            return view('content/apps/client-types/create-edit', compact('page_data', 'clientType'));
        } catch (\Exception $error) {
            return redirect()->route("app/client-types/list")->with('error', 'Error while editing ClientType');
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $encrypted_id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateClientTypeRequest $request, $encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);

            $clientTypeData['name'] = $request->get('name');
            $clientTypeData['displayname'] = $request->get('displayname');
            $clientTypeData['status'] = $request->get('status') === 'on' ? 1 : 0;

            $updated = $this->clientTypeService->updateClientType($id, $clientTypeData);
            if (!empty($updated)) {
                return redirect()->route("app-client-types-list")->with('success', 'ClientType Updated Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Updating ClientType');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-client-types-list")->with('error', 'Error while editing ClientType');
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
            $deleted = $this->clientTypeService->deleteClientType($id);
            if (!empty($deleted)) {
                return redirect()->route("app-client-types-list")->with('success', 'ClientType Deleted Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Deleting ClientType');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-client-types-list")->with('error', 'Error while editing ClientType');
        }
    }


}