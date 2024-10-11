<?php

namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\NewsCategory;
use App\Models\Role;
use App\Models\User;
use App\Services\AdvisorService;
use Illuminate\Http\Request;
use App\Http\Requests\Advisor\CreateAdvisorRequest;
use App\Http\Requests\Advisor\UpdateAdvisorRequest;
use App\Models\ClientType;
use \Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

class AdvisorController extends Controller
{
    protected $advisorService;

    public function __construct(AdvisorService $advisorService)
    {
        $this->advisorService = $advisorService;
        $this->middleware('permission:advisor-list|advisor-create|advisor-edit|advisor-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:advisor-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:advisor-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:advisor-delete', ['only' => ['destroy']]);

        // Permission::create(['name' => 'advisor-list', 'guard_name' => 'web', 'module_name' => 'Advisor']);
        // Permission::create(['name' => 'advisor-create', 'guard_name' => 'web', 'module_name' => 'Advisor']);
        // Permission::create(['name' => 'advisor-edit', 'guard_name' => 'web', 'module_name' => 'Advisor']);
        // Permission::create(['name' => 'advisor-delete', 'guard_name' => 'web', 'module_name' => 'Advisor']);

    }

    public function index()
    {
        return view('content/apps/Advisor/list');
    }

    public function create()
    {
        $advisors = "";
        $page_data['page_title'] = "Advisor Add";
        $page_data['form_title'] = "Add New Advisor";
        $ClientType = ClientType::where('status', '1')->get();
        $users = User::where('role_id', '!=', 2)->get();
        $roles = Role::get();
        $userCategories = Category::get();
        return view('/content/apps/Advisor/create-edit', compact('page_data', 'advisors', 'ClientType', 'roles', 'users', 'userCategories'));
    }

    public function getAll()
    {
        $advisors = $this->advisorService->getAllAdvisor();
        return DataTables::of($advisors)
            ->addColumn('client_type', function ($row) {
                return $row->clientType ? $row->clientType->displayname : 'N/A';
            })
            ->addColumn('actions', function ($row) {
                $encryptedId = encrypt($row->id);
                // Update Button
                $updateButton = "<a class='btn btn-warning btn-sm ' href='" . route('app-advisor-edit', $encryptedId) . "'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"feather feather-edit ficon\"><path d=\"M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7\"></path><path d=\"M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z\"></path></svg> </a>";

                // Delete Button
                $deleteButton = "<a class='btn btn-danger btn-sm mx-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-advisor-delete', $encryptedId) . "'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"feather feather-trash-2 ficon\"><polyline points=\"3 6 5 6 21 6\"></polyline><path d=\"M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2\"></path><line x1=\"10\" y1=\"11\" x2=\"10\" y2=\"17\"></line><line x1=\"14\" y1=\"11\" x2=\"14\" y2=\"17\"></line></svg> </a>";
                $buttons = $updateButton . " " . $deleteButton;
                return "<div class='d-flex justify-content-start'>" . $buttons . "</div>";
            })
            ->rawColumns(['actions'])->make(true);
    }

    public function store(CreateAdvisorRequest $request)
    {
        try {
            $advisorData['name'] = $request->get('name');
            $advisorData['email'] = $request->get('email');
            $advisorData['user_category'] = $request->get('user_category');
            $advisorData['password'] = bcrypt($request->get('password'));
            $advisorData['phone_number'] = $request->get('phone_number');
            $advisorData['reporting_to'] = $request->get('reporting_to');
            $advisorData['is_download'] = $request->get('is_download');
            $advisorData['download_date'] = $request->get('download_date');
            $advisorData['role_id'] = $request->get('role_id');
            $advisorData['status'] = $request->get('status') === 'on' ? 1 : 0;

            if ($request->hasFile('avatar')) {
                $originalName = $request->file('avatar')->getClientOriginalName();
                $filename = str_replace(' ', '_', $originalName);
                $imagePath = $request->file('avatar')->storeAs('Advisor', $filename, 'public');
                $advisorData['avatar'] = $imagePath;
            }

            $advisors = $this->advisorService->create($advisorData);
            if (!empty($advisors)) {
                return redirect()->route('app-advisor-list')->with('success', 'Advisor Added Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Adding Advisor');
            }
        } catch (\Exception $error) {
            dd($error->getMessage());
            return redirect()->route('app-advisor-list')->with('error', 'Error while editing Advisor');
        }

    }

    public function edit($encrypted_id)
    {
        try {

            $id = decrypt($encrypted_id);
            $advisors = $this->advisorService->getAdvisor($id);
            $page_data['page_title'] = "Advisor Edit";
            $page_data['form_title'] = "Edit Advisor";
            $ClientType = ClientType::where('status', '1')->get();
            $users = User::where('role_id', '!=', 2)->get();
            $roles = Role::get();
            $userCategories = Category::get();

            return view('content/apps/Advisor/create-edit', compact('page_data', 'advisors', 'ClientType', 'users', 'roles','userCategories'));
        } catch (\Exception $error) {
            return redirect()->route("app/Advisor/list")->with('error', 'Error while editing Advisor');
        }
    }


    public function update(UpdateAdvisorRequest $request, $encrypted_id)
    {
        try {
            // dd($request->all());
            $id = decrypt($encrypted_id);

            $advisors = $this->advisorService->getAdvisor($id);
            $advisorData['name'] = $request->get('name');
            $advisorData['email'] = $request->get('email');
            $advisorData['user_category'] = $request->get('user_category');
            $advisorData['password'] = bcrypt($request->get('password'));
            $advisorData['phone_number'] = $request->get('phone_number');
            $advisorData['reporting_to'] = $request->get('reporting_to');
            $advisorData['is_download'] = $request->get('is_download');
            $advisorData['download_date'] = $request->get('download_date');
            $advisorData['role_id'] = $request->get('role_id');
            $advisorData['status'] = $request->get('status') === 'on' ? 1 : 0;
            // dd($advisorData);
            if ($request->hasFile('avatar')) {
                if ($advisors->avatar) {
                    Storage::disk('public')->delete($advisors->avatar);
                }

                $originalName = $request->file('avatar')->getClientOriginalName();
                $filename = str_replace(' ', '_', $originalName);
                $imagePath = $request->file('avatar')->storeAs('Advisor', $filename, 'public');
                $advisorData['avatar'] = $imagePath;
            }



            $updated = $this->advisorService->updateAdvisor($id, $advisorData);
            if (!empty($updated)) {
                return redirect()->route("app-advisor-list")->with('success', 'Advisor Updated Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Updating Advisor');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-advisor-list")->with('error', 'Error while editing Advisor');
        }
    }

    public function destroy($encrypted_id = '')
    {

        try {
            $id = decrypt($encrypted_id);
            $deleted = $this->advisorService->deleteAdvisor($id);
            if (!empty($deleted)) {
                return redirect()->route("app-advisor-list")->with('success', 'Advisor Deleted Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Deleting Advisor');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-advisor-list")->with('error', 'Error while Deleting Advisor');
        }
    }

    public function destroyimage($id)
    {
        $advisors = User::findOrFail($id);

        if ($advisors->image) {

            // Storage::delete($advisors->image);
            $advisors->update(['avatar' => '']);
        }

        return redirect()->back()->with('success', 'Image deleted successfully.');
    }
}
