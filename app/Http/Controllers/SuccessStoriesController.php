<?php

namespace App\Http\Controllers;
use App\Models\NewsCategory;
use App\Services\SuccessStoriesService;
use Illuminate\Http\Request;
use App\Http\Requests\SuccessStories\CreateSuccessStoriesRequest;
use App\Http\Requests\SuccessStories\UpdateSuccessStoriesRequest;
use App\Models\ClientType;
use \Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

class SuccessStoriesController extends Controller
{

    protected $successStoriesService;

    public function __construct(SuccessStoriesService $successStories)
    {
        $this->successStoriesService = $successStories;
        $this->middleware('permission:success-stories-list|success-stories-create|success-stories-edit|success-stories-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:success-stories-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:success-stories-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:success-stories-delete', ['only' => ['destroy']]);

        // Permission::create(['name' => 'success-stories-list', 'guard_name' => 'web', 'module_name' => 'Success Stories']);
        // Permission::create(['name' => 'success-stories-create', 'guard_name' => 'web', 'module_name' => 'Success Stories']);
        // Permission::create(['name' => 'success-stories-edit', 'guard_name' => 'web', 'module_name' => 'Success Stories']);
        // Permission::create(['name' => 'success-stories-delete', 'guard_name' => 'web', 'module_name' => 'Success Stories']);

    }

    public function index()
    {
        return view('content/apps/SuccessStories/list');
    }

    public function create()
    {
        $successStories = "";
        $page_data['page_title'] = "success stories Add";
        $page_data['form_title'] = "Add New success stories";
        $ClientType = ClientType::where('status', '1')->get();
        return view('/content/apps/SuccessStories/create-edit', compact('page_data', 'successStories', 'ClientType'));
    }

    public function getAll()
    {
        $successStories = $this->successStoriesService->getAllSuccessStory();
        return DataTables::of($successStories)
            ->addColumn('client_type', function ($row) {
                return $row->clientType ? $row->clientType->displayname : 'N/A';
            })
            ->addColumn('actions', function ($row) {
                $encryptedId = encrypt($row->id);
                // Update Button
                $updateButton = "<a class='btn btn-warning btn-sm ' href='" . route('app-success-stories-edit', $encryptedId) . "'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"feather feather-edit ficon\"><path d=\"M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7\"></path><path d=\"M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z\"></path></svg> </a>";

                // Delete Button
                $deleteButton = "<a class='btn btn-danger btn-sm  confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-success-stories-delete', $encryptedId) . "'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"feather feather-trash-2 ficon\"><polyline points=\"3 6 5 6 21 6\"></polyline><path d=\"M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2\"></path><line x1=\"10\" y1=\"11\" x2=\"10\" y2=\"17\"></line><line x1=\"14\" y1=\"11\" x2=\"14\" y2=\"17\"></line></svg> </a>";
                return $updateButton . " " . $deleteButton;
            })
            ->rawColumns(['actions'])->make(true);
    }

    public function store(CreateSuccessStoriesRequest $request)
    {
        try {
            $successStoriesData['title'] = $request->get('title');
            $successStoriesData['short_description'] = $request->get('short_description');
            $successStoriesData['long_description'] = $request->get('long_description');
            $successStoriesData['date'] = $request->get('date');
            $successStoriesData['candidate_name'] = $request->get('candidate_name');

            if ($request->hasFile('file')) {
                $originalName = $request->file('file')->getClientOriginalName();
                $filename = str_replace(' ', '_', $originalName);
                $imagePath = $request->file('file')->storeAs('successStories', $filename, 'public');
                $successStoriesData['file'] = $imagePath;
            }
            $successStoriesData['candidate_image'] = $request->get('candidate_image');
            $successStoriesData['candidate_type'] = $request->get('candidate_type');
            $successStoriesData['for_home'] = $request->get('for_home');
            $successStoriesData['ratings'] = $request->get('ratings');
            $successStoriesData['video_thumbnail'] = $request->get('video_thumbnail');
            $successStoriesData['status'] = $request->get('status') === 'on' ? 1 : 0;

            $successStories = $this->successStoriesService->create($successStoriesData);
            if (!empty($successStories)) {
                return redirect()->route('app-success-stories-list')->with('success', 'Success Stories Added Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Adding Success Stories');
            }
        } catch (\Exception $error) {
            dd($error->getMessage());
            return redirect()->route('app-success-stories-list')->with('error', 'Error while editing Success Stories');
        }

    }

    public function edit($encrypted_id)
    {
        try {

            $id = decrypt($encrypted_id);
            $successStories = $this->successStoriesService->getSuccessStory($id);
            $page_data['page_title'] = "success stories Edit";
            $page_data['form_title'] = "Edit success stories";
            $ClientType = ClientType::where('status', '1')->get();
            return view('content/apps/SuccessStories/create-edit', compact('page_data', 'successStories', 'ClientType'));
        } catch (\Exception $error) {
            return redirect()->route("app/SuccessStories/list")->with('error', 'Error while editing Success Stories');
        }
    }

    public function update(UpdateSuccessStoriesRequest $request, $encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);

            $successStories = $this->successStoriesService->getSuccessStory($id);
            $successStoriesData['title'] = $request->get('title');
            $successStoriesData['short_description'] = $request->get('short_description');

            $successStoriesData['long_description'] = $request->get('long_description');
            $successStoriesData['date'] = $request->get('date');
            $successStoriesData['candidate_name'] = $request->get('candidate_name');
            if ($request->hasFile('file')) {
                if ($successStories->file) {
                    Storage::disk('public')->delete($successStories->file);
                }

                $originalName = $request->file('file')->getClientOriginalName();
                $filename = str_replace(' ', '_', $originalName);
                $imagePath = $request->file('file')->storeAs('successStories', $filename, 'public');

                $successStoriesData['file'] = $imagePath;
            }
            $successStoriesData['candidate_image'] = $request->get('candidate_image');
            $successStoriesData['candidate_type'] = $request->get('candidate_type');
            $successStoriesData['for_home'] = $request->get('for_home');
            $successStoriesData['ratings'] = $request->get('ratings');
            $successStoriesData['video_thumbnail'] = $request->get('video_thumbnail');

            $successStoriesData['status'] = $request->get('status') === 'on' ? 1 : 0;


            $updated = $this->successStoriesService->updateSuccessStory($id, $successStoriesData);
            if (!empty($updated)) {
                return redirect()->route("app-success-stories-list")->with('success', 'Success Stories Updated Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Updating Success Stories');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-success-stories-list")->with('error', 'Error while editing Success Stories');
        }
    }

    public function destroy($encrypted_id = '')
    {

        try {
            $id = decrypt($encrypted_id);
            $deleted = $this->successStoriesService->deleteSuccessStory($id);
            if (!empty($deleted)) {
                return redirect()->route("app-success-stories-list")->with('success', 'Success Stories Deleted Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Deleting Success Stories');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-success-stories-list")->with('error', 'Error while editing Success Stories');
        }
    }
}
