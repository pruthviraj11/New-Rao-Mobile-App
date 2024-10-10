<?php

namespace App\Http\Controllers;
use App\Models\NewsCategory;
use App\Services\DrawsService;
use Illuminate\Http\Request;
use App\Http\Requests\Draws\CreateDrawsRequest;
use App\Http\Requests\Draws\UpdateDrawsRequest;
use App\Models\ClientType;
use \Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

class DrawsController extends Controller
{

    protected $drawsService;

    public function __construct(DrawsService $draws)
    {
        $this->drawsService = $draws;
        $this->middleware('permission:draws-list|draws-create|draws-edit|draws-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:draws-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:draws-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:draws-delete', ['only' => ['destroy']]);

        // Permission::create(['name' => 'draws-list', 'guard_name' => 'web', 'module_name' => 'Draws']);
        // Permission::create(['name' => 'draws-create', 'guard_name' => 'web', 'module_name' => 'Draws']);
        // Permission::create(['name' => 'draws-edit', 'guard_name' => 'web', 'module_name' => 'Draws']);
        // Permission::create(['name' => 'draws-delete', 'guard_name' => 'web', 'module_name' => 'Draws']);

    }

    public function index()
    {
        return view('content/apps/Draws/list');
    }

    public function create()
    {
        $draws = "";
        $page_data['page_title'] = "Draws Add";
        $page_data['form_title'] = "Add New Draw";
        $ClientType = ClientType::where('status', '1')->get();
        return view('/content/apps/Draws/create-edit', compact('page_data', 'draws', 'ClientType'));
    }

    public function getAll()
    {
        $draws = $this->drawsService->getAllDraws();
        return DataTables::of($draws)
            ->addColumn('client_type', function ($row) {
                return $row->clientType ? $row->clientType->displayname : 'N/A';
            })
            ->addColumn('actions', function ($row) {
                $encryptedId = encrypt($row->id);
                // Update Button
                $updateButton = "<a class='btn btn-warning btn-sm ' href='" . route('app-draws-edit', $encryptedId) . "'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"feather feather-edit ficon\"><path d=\"M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7\"></path><path d=\"M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z\"></path></svg> </a>";

                // Delete Button
                $deleteButton = "<a class='btn btn-danger btn-sm mx-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-draws-delete', $encryptedId) . "'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"feather feather-trash-2 ficon\"><polyline points=\"3 6 5 6 21 6\"></polyline><path d=\"M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2\"></path><line x1=\"10\" y1=\"11\" x2=\"10\" y2=\"17\"></line><line x1=\"14\" y1=\"11\" x2=\"14\" y2=\"17\"></line></svg> </a>";
                $buttons = $updateButton . " " . $deleteButton;
                return "<div class='d-flex justify-content-start'>" . $buttons . "</div>";
            })
            ->rawColumns(['actions'])->make(true);
    }

    public function store(CreateDrawsRequest $request)
    {
        try {

            $drawsData['date'] = $request->get('date');
            $drawsData['crs_cutoff'] = $request->get('crs_cutoff');
            $drawsData['type'] = $request->get('type');
            $drawsData['ita_issue'] = $request->get('ita_issue');
            $drawsData['status'] = $request->get('status') === 'on' ? 1 : 0;

            $draws = $this->drawsService->create($drawsData);
            if (!empty($draws)) {
                return redirect()->route('app-draws-list')->with('success', 'Draws Added Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Adding Draws');
            }
        } catch (\Exception $error) {
            dd($error->getMessage());
            return redirect()->route('app-draws-list')->with('error', 'Error while editing Draws');
        }

    }

    public function edit($encrypted_id)
    {
        try {

            $id = decrypt($encrypted_id);
            $draws = $this->drawsService->getDraws($id);
            $page_data['page_title'] = "Draws Edit";
            $page_data['form_title'] = "Edit Draw";
            $ClientType = ClientType::where('status', '1')->get();
            return view('content/apps/Draws/create-edit', compact('page_data', 'draws', 'ClientType'));
        } catch (\Exception $error) {
            return redirect()->route("app/Draws/list")->with('error', 'Error while editing Draw');
        }
    }

    public function update(UpdateDrawsRequest $request, $encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);
            $draws = $this->drawsService->getDraws($id);
            $drawsData['date'] = $request->get('date');
            $drawsData['crs_cutoff'] = $request->get('crs_cutoff');
            $drawsData['type'] = $request->get('type');
            $drawsData['ita_issue'] = $request->get('ita_issue');
            $drawsData['status'] = $request->get('status') === 'on' ? 1 : 0;

            $updated = $this->drawsService->updateDraws($id, $drawsData);
            if (!empty($updated)) {
                return redirect()->route("app-draws-list")->with('success', 'Draws Updated Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Updating Draws');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-draws-list")->with('error', 'Error while editing Draws');
        }
    }

    public function destroy($encrypted_id = '')
    {

        try {
            $id = decrypt($encrypted_id);
            $deleted = $this->drawsService->deleteDraws($id);
            if (!empty($deleted)) {
                return redirect()->route("app-draws-list")->with('success', 'Draws Deleted Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Deleting Draws');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-draws-list")->with('error', 'Error while Deleting Draws');
        }
    }
}
