<?php

namespace App\Http\Controllers;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Services\FaqService;
use Illuminate\Http\Request;
use App\Http\Requests\Faq\CreateFaqRequest;
use App\Http\Requests\Faq\UpdateFaqRequest;
use \Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

class FaqController extends Controller
{
    protected $faqService;

    public function __construct(FaqService $faq)
    {
        $this->faqService = $faq;
        $this->middleware('permission:faq-list|faq-create|faq-edit|faq-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:faq-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:faq-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:faq-delete', ['only' => ['destroy']]);

        // Permission::create(['name' => 'faq-list', 'guard_name' => 'web', 'module_name' => 'Faq']);
        // Permission::create(['name' => 'faq-create', 'guard_name' => 'web', 'module_name' => 'Faq']);
        // Permission::create(['name' => 'faq-edit', 'guard_name' => 'web', 'module_name' => 'Faq']);
        // Permission::create(['name' => 'faq-delete', 'guard_name' => 'web', 'module_name' => 'Faq']);

    }
    public function index()
    {
        return view('content/apps/Faq/list');
    }
    public function bulkDelete(Request $request)
    {
        Faq::whereIn('id', $request->ids)->delete();

        return response()->json(['message' => 'Slider deleted successfully.']);
    }
    public function create()
    {
        $faq = "";
        $page_data['page_title'] = "faq Add";
        $page_data['form_title'] = "Add New faq";
        $faqCat = FaqCategory::where('status', '1')->get();
        return view('/content/apps/Faq/create-edit', compact('page_data', 'faq', 'faqCat'));
    }
    public function getAll()
    {
        $faq = $this->faqService->getAllFaq();
        return DataTables::of($faq)
            ->addColumn('faq_categories', function ($row) {
                return $row->faq_categories ? $row->faq_categories->name : 'N/A'; // Fetching displayname from clientType
            })
            ->addColumn('actions', function ($row) {
                $encryptedId = encrypt($row->id);
                // Update Button
                $updateButton = "<a class='btn btn-warning btn-sm ' href='" . route('app-faq-edit', $encryptedId) . "'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"feather feather-edit ficon\"><path d=\"M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7\"></path><path d=\"M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z\"></path></svg> </a>";

                // Delete Button
                $deleteButton = "<a class='btn btn-danger btn-sm  mx-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-faq-delete', $encryptedId) . "'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"feather feather-trash-2 ficon\"><polyline points=\"3 6 5 6 21 6\"></polyline><path d=\"M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2\"></path><line x1=\"10\" y1=\"11\" x2=\"10\" y2=\"17\"></line><line x1=\"14\" y1=\"11\" x2=\"14\" y2=\"17\"></line></svg> </a>";
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
    public function store(CreateFaqRequest $request)
    {
        try {
            $faqData['title'] = $request->get('title');
            $faqData['faq_category_id'] = $request->get('faq_category_id');
            $faqData['answer'] = $request->get('answer');
            $faqData['sequence'] = $request->get('sequence');
            $faqData['status'] = $request->get('status') === 'on' ? 1 : 0;

            $faq = $this->faqService->create($faqData);
            if (!empty($faq)) {
                return redirect()->route('app-faq-list')->with('success', 'Faq Added Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Adding Faq');
            }
        } catch (\Exception $error) {
            dd($error->getMessage());
            return redirect()->route('app-faq-list')->with('error', 'Error while editing Faq');
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
            $faq = $this->faqService->getFaq($id);
            $page_data['page_title'] = "faq Edit";
            $page_data['form_title'] = "Edit faq";
            $faqCat = FaqCategory::where('status', '1')->get();
            return view('content/apps/Faq/create-edit', compact('page_data', 'faq', 'faqCat'));
        } catch (\Exception $error) {
            return redirect()->route("app/Faq/list")->with('error', 'Error while editing Faq');
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $encrypted_id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFaqRequest $request, $encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);

            $faq = $this->faqService->getFaq($id);
            $faqData['title'] = $request->get('title');
            $faqData['faq_category_id'] = $request->get('faq_category_id');
            $faqData['answer'] = $request->get('answer');
            $faqData['sequence'] = $request->get('sequence');
            $faqData['status'] = $request->get('status') === 'on' ? 1 : 0;


            $updated = $this->faqService->updateFaq($id, $faqData);
            if (!empty($updated)) {
                return redirect()->route("app-faq-list")->with('success', 'Faq Updated Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Updating Faq');
            }
        } catch (\Exception $error) {
           
            return redirect()->route("app-faq-list")->with('error', 'Error while editing Faq');
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
            $deleted = $this->faqService->deleteFaq($id);
            if (!empty($deleted)) {
                return redirect()->route("app-faq-list")->with('success', 'Faq Deleted Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Deleting Faq');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-faq-list")->with('error', 'Error while editing Faq');
        }
    }

}
