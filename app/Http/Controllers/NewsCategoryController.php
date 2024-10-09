<?php

namespace App\Http\Controllers;
use App\Models\NewsCategory;
use App\Services\NewsCategoriesService;
use Illuminate\Http\Request;
use App\Http\Requests\NewsCategories\CreateNewsCategoriesRequest;
use App\Http\Requests\NewsCategories\UpdateNewsCategoriesRequest;
use App\Models\ClientType;
use \Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

class NewsCategoryController extends Controller
{
    protected $newsCategoriesService;

    public function __construct(NewsCategoriesService $newsCategories)
    {
        $this->newsCategoriesService = $newsCategories;
        $this->middleware('permission:news-categories-list|news-categories-create|news-categories-edit|news-categories-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:news-categories-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:news-categories-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:news-categories-delete', ['only' => ['destroy']]);

        // Permission::create(['name' => 'news-categories-list', 'guard_name' => 'web', 'module_name' => 'News Categories']);
        // Permission::create(['name' => 'news-categories-create', 'guard_name' => 'web', 'module_name' => 'News Categories']);
        // Permission::create(['name' => 'news-categories-edit', 'guard_name' => 'web', 'module_name' => 'News Categories']);
        // Permission::create(['name' => 'news-categories-delete', 'guard_name' => 'web', 'module_name' => 'News Categories']);

    }
    public function index()
    {
        return view('content/apps/NewsCategorie/list');
    }

    public function create()
    {
        $newsCategories = "";
        $page_data['page_title'] = "news categories Add";
        $page_data['form_title'] = "Add New news categories";
        $ClientType = ClientType::where('status', '1')->get();
        return view('/content/apps/NewsCategorie/create-edit', compact('page_data', 'newsCategories', 'ClientType'));
    }
    public function getAll()
    {
        $newsCategories = $this->newsCategoriesService->getAllNewsCategorie();
        return DataTables::of($newsCategories)
            ->addColumn('client_type', function ($row) {
                return $row->clientType ? $row->clientType->displayname : 'N/A';
            })
            ->addColumn('actions', function ($row) {
                $encryptedId = encrypt($row->id);
                // Update Button
                $updateButton = "<a class='btn btn-warning btn-sm ' href='" . route('app-news-categories-edit', $encryptedId) . "'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"feather feather-edit ficon\"><path d=\"M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7\"></path><path d=\"M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z\"></path></svg> </a>";

                // Delete Button
                $deleteButton = "<a class='btn btn-danger btn-sm  confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-news-categories-delete', $encryptedId) . "'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"feather feather-trash-2 ficon\"><polyline points=\"3 6 5 6 21 6\"></polyline><path d=\"M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2\"></path><line x1=\"10\" y1=\"11\" x2=\"10\" y2=\"17\"></line><line x1=\"14\" y1=\"11\" x2=\"14\" y2=\"17\"></line></svg> </a>";
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
    public function store(CreateNewsCategoriesRequest $request)
    {
        try {
            $newsCategoriesData['title'] = $request->get('title');
            $newsCategoriesData['client_type'] = $request->get('client_type');
            $newsCategoriesData['status'] = $request->get('status') === 'on' ? 1 : 0;
            if ($request->hasFile('image')) {
                $originalName = $request->file('image')->getClientOriginalName();
                $filename = str_replace(' ', '_', $originalName);
                $imagePath = $request->file('image')->storeAs('sliders', $filename, 'public');
                $newsCategoriesData['image'] = $imagePath;
            }
            $newsCategories = $this->newsCategoriesService->create($newsCategoriesData);
            if (!empty($newsCategories)) {
                return redirect()->route('app-news-categories-list')->with('success', 'NewsCategorie Added Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Adding NewsCategorie');
            }
        } catch (\Exception $error) {
            dd($error->getMessage());
            return redirect()->route('app-news-categories-list')->with('error', 'Error while editing NewsCategorie');
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
            $newsCategories = $this->newsCategoriesService->getNewsCategorie($id);
            $page_data['page_title'] = "news categories Edit";
            $page_data['form_title'] = "Edit news categories";
            $ClientType = ClientType::where('status', '1')->get();
            return view('content/apps/NewsCategorie/create-edit', compact('page_data', 'newsCategories', 'ClientType'));
        } catch (\Exception $error) {
            return redirect()->route("app/NewsCategorie/list")->with('error', 'Error while editing NewsCategories');
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $encrypted_id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateNewsCategoriesRequest $request, $encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);

            $newsCategories = $this->newsCategoriesService->getNewsCategorie($id);
            $newsCategoriesData['title'] = $request->get('title');
            $newsCategoriesData['client_type'] = $request->get('client_type');
            $newsCategoriesData['status'] = $request->get('status') === 'on' ? 1 : 0;
            if ($request->hasFile('image')) {
                if ($newsCategories->image) {
                    Storage::disk('public')->delete($newsCategories->image);
                }

                $originalName = $request->file('image')->getClientOriginalName();
                $filename = str_replace(' ', '_', $originalName);
                $imagePath = $request->file('image')->storeAs('sliders', $filename, 'public');

                $newsCategoriesData['image'] = $imagePath;
            }

            $updated = $this->newsCategoriesService->updateNewsCategorie($id, $newsCategoriesData);
            if (!empty($updated)) {
                return redirect()->route("app-news-categories-list")->with('success', 'NewsCategories Updated Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Updating NewsCategories');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-news-categories-list")->with('error', 'Error while editing NewsCategories');
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
            $deleted = $this->newsCategoriesService->deleteNewsCategorie($id);
            if (!empty($deleted)) {
                return redirect()->route("app-news-categories-list")->with('success', 'NewsCategories Deleted Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Deleting NewsCategories');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-news-categories-list")->with('error', 'Error while editing NewsCategories');
        }
    }

    
}
