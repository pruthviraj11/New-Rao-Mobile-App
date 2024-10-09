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
        $this->faqCategoriesService = $faqCategories;
        $this->middleware('permission:faq-categories-list|faq-categories-create|faq-categories-edit|faq-categories-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:faq-categories-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:faq-categories-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:faq-categories-delete', ['only' => ['destroy']]);

        // Permission::create(['name' => 'faq-categories-list', 'guard_name' => 'web', 'module_name' => 'Faq categories']);
        // Permission::create(['name' => 'faq-categories-create', 'guard_name' => 'web', 'module_name' => 'Faq categories']);
        // Permission::create(['name' => 'faq-categories-edit', 'guard_name' => 'web', 'module_name' => 'Faq categories']);
        // Permission::create(['name' => 'faq-categories-delete', 'guard_name' => 'web', 'module_name' => 'Faq categories']);

    }
    public function index()
    {
        return view('content/apps/FaqCategorie/list');
    }

    public function create()
    {
        $faqCategories = "";
        $page_data['page_title'] = "faq categories Add";
        $page_data['form_title'] = "Add New faq categories";
        $ClientType = ClientType::where('status', '1')->get();
        return view('/content/apps/FaqCategorie/create-edit', compact('page_data', 'faqCategories', 'ClientType'));
    }
    public function getAll()
    {
        $faqCategories = $this->faqCategoriesService->getAllFaqCategorie();
        return DataTables::of($faqCategories)
            ->addColumn('client_type', function ($row) {
                return $row->clientType ? $row->clientType->displayname : 'N/A'; // Fetching displayname from clientType
            })
            ->addColumn('actions', function ($row) {
                $encryptedId = encrypt($row->id);
                // Update Button
                $updateButton = "<a class='btn btn-warning btn-sm ' href='" . route('app-faq-categories-edit', $encryptedId) . "'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"feather feather-edit ficon\"><path d=\"M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7\"></path><path d=\"M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z\"></path></svg> </a>";

                // Delete Button
                $deleteButton = "<a class='btn btn-danger btn-sm  mx-1  confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-faq-categories-delete', $encryptedId) . "'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"feather feather-trash-2 ficon\"><polyline points=\"3 6 5 6 21 6\"></polyline><path d=\"M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2\"></path><line x1=\"10\" y1=\"11\" x2=\"10\" y2=\"17\"></line><line x1=\"14\" y1=\"11\" x2=\"14\" y2=\"17\"></line></svg> </a>";
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
    public function store(CreateFaqCategoriesRequest $request)
    {
        try {
            $faqCategoriesData['name'] = $request->get('name');
            $faqCategoriesData['category_id'] = $request->get('category_id');
            $faqCategoriesData['description'] = $request->get('description');
            $faqCategoriesData['status'] = $request->get('status') === 'on' ? 1 : 0;

            $faqCategories = $this->faqCategoriesService->create($faqCategoriesData);
            if (!empty($faqCategories)) {
                return redirect()->route('app-faq-categories-list')->with('success', 'FaqCategorie Added Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Adding FaqCategorie');
            }
        } catch (\Exception $error) {
            dd($error->getMessage());
            return redirect()->route('app-faq-categories-list')->with('error', 'Error while editing FaqCategorie');
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
            $faqCategories = $this->faqCategoriesService->getFaqCategorie($id);
            $page_data['page_title'] = "faq categories Edit";
            $page_data['form_title'] = "Edit faq categories";
            $ClientType = ClientType::where('status', '1')->get();
            return view('content/apps/FaqCategorie/create-edit', compact('page_data', 'faqCategories', 'ClientType'));
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

            $faqCategories = $this->faqCategoriesService->getFaqCategorie($id);
            $faqCategoriesData['name'] = $request->get('name');
            $faqCategoriesData['category_id'] = $request->get('category_id');
            $faqCategoriesData['description'] = $request->get('description');
            $faqCategoriesData['status'] = $request->get('status') === 'on' ? 1 : 0;


            $updated = $this->faqCategoriesService->updateFaqCategorie($id, $faqCategoriesData);
            if (!empty($updated)) {
                return redirect()->route("app-faq-categories-list")->with('success', 'FaqCategories Updated Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Updating FaqCategories');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-faq-categories-list")->with('error', 'Error while editing FaqCategories');
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
            $deleted = $this->faqCategoriesService->deleteFaqCategorie($id);
            if (!empty($deleted)) {
                return redirect()->route("app-faq-categories-list")->with('success', 'FaqCategories Deleted Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Deleting FaqCategories');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-faq-categories-list")->with('error', 'Error while editing FaqCategories');
        }
    }


}
