<?php

namespace App\Http\Controllers;
use App\Models\NewsCategory;
use App\Models\User;
use App\Services\OtherStakeholdersService;
use Illuminate\Http\Request;
use App\Http\Requests\OtherStakeholders\CreateOtherStakeholdersRequest;
use App\Http\Requests\OtherStakeholders\UpdateOtherStakeholdersRequest;
use App\Models\ClientType;
use \Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

class OtherStakeholdersController extends Controller
{
    protected $otherStakeholdersService;

    public function __construct(OtherStakeholdersService $otherStakeholdersService)
    {
        $this->otherStakeholdersService = $otherStakeholdersService;
        $this->middleware('permission:other-stakeholders-list|other-stakeholders-create|other-stakeholders-edit|other-stakeholders-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:other-stakeholders-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:other-stakeholders-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:other-stakeholders-delete', ['only' => ['destroy']]);

        // Permission::create(['name' => 'other-stakeholders-list', 'guard_name' => 'web', 'module_name' => 'Other Stakeholders']);
        // Permission::create(['name' => 'other-stakeholders-create', 'guard_name' => 'web', 'module_name' => 'Other Stakeholders']);
        // Permission::create(['name' => 'other-stakeholders-edit', 'guard_name' => 'web', 'module_name' => 'Other Stakeholders']);
        // Permission::create(['name' => 'other-stakeholders-delete', 'guard_name' => 'web', 'module_name' => 'Other Stakeholders']);

    }



    public function index()
    {
        return view('content/apps/OtherStakeholders/list');
    }


    public function create()
    {
        $otherStakeholders = "";
        $page_data['page_title'] = "Other Stakeholders Add";
        $page_data['form_title'] = "Add New Other Stakeholders";
        $ClientType = ClientType::where('status', '1')->get();
        $users = User::get();
        $advisors = User::where('role_id', '=', 23)->get();
        return view('/content/apps/OtherStakeholders/create-edit', compact('page_data', 'otherStakeholders', 'ClientType', 'users', 'advisors'));
    }

    public function getAll()
    {
        $otherStakeholders = $this->otherStakeholdersService->getAllOtherStakeholders();
        return DataTables::of($otherStakeholders)
            ->addColumn('client_type', function ($row) {
                return $row->clientType ? $row->clientType->displayname : 'N/A';
            })
            ->addColumn('actions', function ($row) {
                $encryptedId = encrypt($row->id);
                // Update Button
                $updateButton = "<a class='btn btn-warning btn-sm ' href='" . route('app-other-stakeholders-edit', $encryptedId) . "'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"feather feather-edit ficon\"><path d=\"M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7\"></path><path d=\"M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z\"></path></svg> </a>";

                // Delete Button
                $deleteButton = "<a class='btn btn-danger btn-sm mx-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-other-stakeholders-delete', $encryptedId) . "'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"feather feather-trash-2 ficon\"><polyline points=\"3 6 5 6 21 6\"></polyline><path d=\"M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2\"></path><line x1=\"10\" y1=\"11\" x2=\"10\" y2=\"17\"></line><line x1=\"14\" y1=\"11\" x2=\"14\" y2=\"17\"></line></svg> </a>";
                $buttons = $updateButton . " " . $deleteButton;
                return "<div class='d-flex justify-content-start'>" . $buttons . "</div>";
            })
            ->rawColumns(['actions'])->make(true);
    }

    public function store(CreateOtherStakeholdersRequest $request)
    {
        try {
            $otherStakeholdersData['user_id'] = $request->get('user_id');
            $otherStakeholdersData['attached_user_id'] = $request->get('attached_user_id');
            $otherStakeholdersData['role_name'] = $request->get('role_name');

            $otherStakeholders = $this->otherStakeholdersService->create($otherStakeholdersData);
            if (!empty($otherStakeholders)) {
                return redirect()->route('app-other-stakeholders-list')->with('success', 'Other Stakeholders Added Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Adding Other Stakeholders');
            }
        } catch (\Exception $error) {
            dd($error->getMessage());
            return redirect()->route('app-other-stakeholders-list')->with('error', 'Error while editing Other Stakeholders');
        }

    }

    public function edit($encrypted_id)
    {
        try {

            $id = decrypt($encrypted_id);
            $otherStakeholders = $this->otherStakeholdersService->getOtherStakeholders($id);
            $page_data['page_title'] = "Other Stakeholders Edit";
            $page_data['form_title'] = "Edit Other Stakeholders";
            $ClientType = ClientType::where('status', '1')->get();
            $users = User::get();
            $advisors = User::where('role_id', '=', 23)->get();
            return view('content/apps/OtherStakeholders/create-edit', compact('page_data', 'otherStakeholders', 'ClientType', 'users', 'advisors'));
        } catch (\Exception $error) {
            return redirect()->route("app/OtherStakeholders/list")->with('error', 'Error while editing Our Services');
        }
    }


    public function update(UpdateOtherStakeholdersRequest $request, $encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);

            $otherStakeholders = $this->otherStakeholdersService->getOtherStakeholders($id);
            $otherStakeholdersData['user_id'] = $request->get('user_id');
            $otherStakeholdersData['attached_user_id'] = $request->get('attached_user_id');
            $otherStakeholdersData['role_name'] = $request->get('role_name');


            $updated = $this->otherStakeholdersService->updateOtherStakeholders($id, $otherStakeholdersData);
            if (!empty($updated)) {
                return redirect()->route("app-other-stakeholders-list")->with('success', 'Other Stakeholders Updated Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Updating Other Stakeholders');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-other-stakeholders-list")->with('error', 'Error while editing Other Stakeholders');
        }
    }

    public function destroy($encrypted_id = '')
    {

        try {
            $id = decrypt($encrypted_id);
            $deleted = $this->otherStakeholdersService->deleteOtherStakeholders($id);
            if (!empty($deleted)) {
                return redirect()->route("app-other-stakeholders-list")->with('success', 'Other Stakeholders Deleted Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Deleting Other Stakeholders');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-other-stakeholders-list")->with('error', 'Error while editing Other Stakeholders');
        }
    }

    public function getUsers(Request $request)
    {
        $search = $request->input('search');
        $page = $request->input('page', 1);
        $pageSize = 10; // Number of users to show per page

        $query = User::query();

        // Filter by search term if provided
        if (!empty($search)) {
            $query->where('name', 'LIKE', "%{$search}%");
        }

        $totalUsers = $query->count();

        $users = $query->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get(['id', 'name']);

        // Format results for Select2
        $results = $users->map(function ($user) {
            return ['id' => $user->id, 'name' => $user->name];
        });

        return response()->json([
            'data' => $results,
            'pagination' => [
                'more' => ($page * $pageSize) < $totalUsers // Indicate if more results are available
            ]
        ]);
    }

}
