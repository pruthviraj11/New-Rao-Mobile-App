<?php

namespace App\Http\Controllers;
use App\Models\NewsCategory;
use App\Services\OurServicesService;
use Illuminate\Http\Request;
use App\Http\Requests\OurServices\CreateOurServicesRequest;
use App\Http\Requests\OurServices\UpdateOurServicesRequest;
use App\Models\ClientType;
use \Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

class OurServicesController extends Controller
{

    protected $ourServicesService;

    public function __construct(OurServicesService $ourServicesService)
    {
        $this->ourServicesService = $ourServicesService;
        $this->middleware('permission:our-services-list|our-services-create|our-services-edit|our-services-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:our-services-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:our-services-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:our-services-delete', ['only' => ['destroy']]);

        // Permission::create(['name' => 'our-services-list', 'guard_name' => 'web', 'module_name' => 'Our Services']);
        // Permission::create(['name' => 'our-services-create', 'guard_name' => 'web', 'module_name' => 'Our Services']);
        // Permission::create(['name' => 'our-services-edit', 'guard_name' => 'web', 'module_name' => 'Our Services']);
        // Permission::create(['name' => 'our-services-delete', 'guard_name' => 'web', 'module_name' => 'Our Services']);

    }

    public function index()
    {
        return view('content/apps/OurServices/list');
    }

    public function create()
    {
        $ourServices = "";
        $page_data['page_title'] = "Our Services Add";
        $page_data['form_title'] = "Add New Our Services";
        $ClientType = ClientType::where('status', '1')->get();
        return view('/content/apps/OurServices/create-edit', compact('page_data', 'ourServices', 'ClientType'));
    }

    public function getAll()
    {
        $ourServices = $this->ourServicesService->getAllOurServices();
        return DataTables::of($ourServices)
            ->addColumn('client_type', function ($row) {
                return $row->clientType ? $row->clientType->displayname : 'N/A';
            })
            ->addColumn('actions', function ($row) {
                $encryptedId = encrypt($row->id);
                // Update Button
                $updateButton = "<a class='btn btn-warning btn-sm ' href='" . route('app-our-services-edit', $encryptedId) . "'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"feather feather-edit ficon\"><path d=\"M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7\"></path><path d=\"M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z\"></path></svg> </a>";

                // Delete Button
                $deleteButton = "<a class='btn btn-danger btn-sm mx-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-our-services-delete', $encryptedId) . "'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"feather feather-trash-2 ficon\"><polyline points=\"3 6 5 6 21 6\"></polyline><path d=\"M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2\"></path><line x1=\"10\" y1=\"11\" x2=\"10\" y2=\"17\"></line><line x1=\"14\" y1=\"11\" x2=\"14\" y2=\"17\"></line></svg> </a>";
                $buttons = $updateButton . " " . $deleteButton;
                return "<div class='d-flex justify-content-start'>" . $buttons . "</div>";
            })
            ->rawColumns(['actions'])->make(true);
    }

    public function store(CreateOurServicesRequest $request)
    {
        try {
            $ourServicesData['title'] = $request->get('title');
            $ourServicesData['short_description'] = $request->get('short_description');

            if ($request->hasFile('file')) {
                $originalName = $request->file('file')->getClientOriginalName();
                $filename = str_replace(' ', '_', $originalName);
                $imagePath = $request->file('file')->storeAs('ourServices', $filename, 'public');
                $ourServicesData['file'] = $imagePath;
            }

            $ourServicesData['contact_no'] = $request->get('contact_no');



            $ourServicesData['status'] = $request->get('status') === 'on' ? 1 : 0;

            $ourServices = $this->ourServicesService->create($ourServicesData);
            if (!empty($ourServices)) {
                return redirect()->route('app-our-services-list')->with('success', 'Our Services Added Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Adding Our Services');
            }
        } catch (\Exception $error) {
            dd($error->getMessage());
            return redirect()->route('app-our-services-list')->with('error', 'Error while editing Our Services');
        }

    }

    public function edit($encrypted_id)
    {
        try {

            $id = decrypt($encrypted_id);
            $ourServices = $this->ourServicesService->getOurServices($id);
            $page_data['page_title'] = "Our Services Edit";
            $page_data['form_title'] = "Edit Our Services";
            $ClientType = ClientType::where('status', '1')->get();
            return view('content/apps/OurServices/create-edit', compact('page_data', 'ourServices', 'ClientType'));
        } catch (\Exception $error) {
            return redirect()->route("app/OurServices/list")->with('error', 'Error while editing Our Services');
        }
    }

    public function update(UpdateOurServicesRequest $request, $encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);

            $ourServices = $this->ourServicesService->getOurServices($id);
            $ourServicesData['title'] = $request->get('title');
            $ourServicesData['short_description'] = $request->get('short_description');
            if ($request->hasFile('file')) {
                if ($ourServices->file) {
                    Storage::disk('public')->delete($ourServices->file);
                }

                $originalName = $request->file('file')->getClientOriginalName();
                $filename = str_replace(' ', '_', $originalName);
                $imagePath = $request->file('file')->storeAs('ourServices', $filename, 'public');

                $ourServicesData['file'] = $imagePath;
            }
            $ourServicesData['contact_no'] = $request->get('contact_no');
            $ourServicesData['status'] = $request->get('status') === 'on' ? 1 : 0;


            $updated = $this->ourServicesService->updateOurServices($id, $ourServicesData);
            if (!empty($updated)) {
                return redirect()->route("app-our-services-list")->with('success', 'Our Services Updated Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Updating Our Services');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-our-services-list")->with('error', 'Error while editing Our Services');
        }
    }

    public function destroy($encrypted_id = '')
    {

        try {
            $id = decrypt($encrypted_id);
            $deleted = $this->ourServicesService->deleteOurServices($id);
            if (!empty($deleted)) {
                return redirect()->route("app-our-services-list")->with('success', 'Our Services Deleted Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Deleting Our Services');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-our-services-list")->with('error', 'Error while editing Our Services');
        }
    }
}
