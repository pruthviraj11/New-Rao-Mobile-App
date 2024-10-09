<?php

namespace App\Http\Controllers;
use App\Models\FaqCategory;
use App\Services\FaqCategoriesService;
use Illuminate\Http\Request;
use App\Http\Requests\FaqCategories\CreateFaqCategoriesRequest;
use App\Http\Requests\FaqCategories\UpdateFaqCategoriesRequest;
use App\Models\ClientType;
use \Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

class FaqCategoryController extends Controller
{
    protected $faqCategoriesService;

    public function __construct(FaqCategoriesService $faqCategories)
    {
        $this->newsCategoriesService = $faqCategories;
        $this->middleware('permission:faq-category-list|faq-category-create|faq-category-edit|faq-category-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:faq-category-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:faq-category-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:faq-category-delete', ['only' => ['destroy']]);

        // Permission::create(['name' => 'faq-category-list', 'guard_name' => 'web', 'module_name' => 'Faq Category']);
        // Permission::create(['name' => 'faq-category-create', 'guard_name' => 'web', 'module_name' => 'Faq Category']);
        // Permission::create(['name' => 'faq-category-edit', 'guard_name' => 'web', 'module_name' => 'Faq Category']);
        // Permission::create(['name' => 'faq-category-delete', 'guard_name' => 'web', 'module_name' => 'Faq Category']);

    }
    public function index()
    {
        return view('content/apps/FaqCategorie/list');
    }

    public function create()
    {
        $faqCategories = "";
        $page_data['page_title'] = "news categories Add";
        $page_data['form_title'] = "Add New news categories";
        $ClientType = ClientType::where('status', '1')->get();
        return view('/content/apps/FaqCategorie/create-edit', compact('page_data', 'newsCategories', 'ClientType'));
    }
    public function getAll()
    {
        $faqCategories = $this->newsCategoriesService->getAllFaqCategorie();
        return DataTables::of($faqCategories)
            ->addColumn('client_type', function ($row) {
                return $row->clientType ? $row->clientType->displayname : 'N/A';
            })
            ->addColumn('actions', function ($row) {
                $encryptedId = encrypt($row->id);
                // Update Button
                $updateButton = "<a class='btn btn-warning btn-sm ' href='" . route('app-faq-category-edit', $encryptedId) . "'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"feather feather-edit ficon\"><path d=\"M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7\"></path><path d=\"M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z\"></path></svg> </a>";

                // Delete Button
                $deleteButton = "<a class='btn btn-danger btn-sm  confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-faq-category-delete', $encryptedId) . "'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"feather feather-trash-2 ficon\"><polyline points=\"3 6 5 6 21 6\"></polyline><path d=\"M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2\"></path><line x1=\"10\" y1=\"11\" x2=\"10\" y2=\"17\"></line><line x1=\"14\" y1=\"11\" x2=\"14\" y2=\"17\"></line></svg> </a>";
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
    public function store(CreateFaqCategoriesRequest $request)
    {
        try {
            $faqCategoriesData['title'] = $request->get('title');
            $faqCategoriesData['client_type'] = $request->get('client_type');
            $faqCategoriesData['status'] = $request->get('status') === 'on' ? 1 : 0;
            if ($request->hasFile('image')) {
                $originalName = $request->file('image')->getClientOriginalName();
                $filename = str_replace(' ', '_', $originalName);
                $imagePath = $request->file('image')->storeAs('sliders', $filename, 'public');
                $faqCategoriesData['image'] = $imagePath;
            }
            $faqCategories = $this->newsCategoriesService->create($faqCategoriesData);
            if (!empty($faqCategories)) {
                return redirect()->route('app-faq-category-list')->with('success', 'FaqCategorie Added Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Adding FaqCategorie');
            }
        } catch (\Exception $error) {
            dd($error->getMessage());
            return redirect()->route('app-faq-category-list')->with('error', 'Error while editing FaqCategorie');
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
            $faqCategories = $this->newsCategoriesService->getFaqCategorie($id);
            $page_data['page_title'] = "news categories Edit";
            $page_data['form_title'] = "Edit news categories";
            $ClientType = ClientType::where('status', '1')->get();
            return view('content/apps/FaqCategorie/create-edit', compact('page_data', 'newsCategories', 'ClientType'));
        } catch (\Exception $error) {
            return redirect()->route("app/FaqCategorie/list")->with('error', 'Error while editing FaqCategories');
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $encrypted_id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFaqCategoriesRequest $request, $encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);

            $faqCategories = $this->newsCategoriesService->getFaqCategorie($id);
            $faqCategoriesData['title'] = $request->get('title');
            $faqCategoriesData['client_type'] = $request->get('client_type');
            $faqCategoriesData['status'] = $request->get('status') === 'on' ? 1 : 0;
            if ($request->hasFile('image')) {
                if ($faqCategories->image) {
                    Storage::disk('public')->delete($faqCategories->image);
                }

                $originalName = $request->file('image')->getClientOriginalName();
                $filename = str_replace(' ', '_', $originalName);
                $imagePath = $request->file('image')->storeAs('sliders', $filename, 'public');

                $faqCategoriesData['image'] = $imagePath;
            }

            $updated = $this->newsCategoriesService->updateFaqCategorie($id, $faqCategoriesData);
            if (!empty($updated)) {
                return redirect()->route("app-faq-category-list")->with('success', 'FaqCategories Updated Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Updating FaqCategories');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-faq-category-list")->with('error', 'Error while editing FaqCategories');
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
            $deleted = $this->newsCategoriesService->deleteFaqCategorie($id);
            if (!empty($deleted)) {
                return redirect()->route("app-faq-category-list")->with('success', 'FaqCategories Deleted Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Deleting FaqCategories');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-faq-category-list")->with('error', 'Error while editing FaqCategories');
        }
    }


}
