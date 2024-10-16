<?php

namespace App\Http\Controllers;
use App\Models\NewsCategory;
use App\Services\NewsService;
use Illuminate\Http\Request;
use App\Http\Requests\News\CreateNewsRequest;
use App\Http\Requests\News\UpdateNewsRequest;
use App\Models\ClientType;
use App\Models\News;
use \Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

class NewsController extends Controller
{
    protected $newsService;

    public function __construct(NewsService $news)
    {
        $this->newsService = $news;
        $this->middleware('permission:news-list|news-create|news-edit|news-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:news-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:news-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:news-delete', ['only' => ['destroy']]);

        // Permission::create(['name' => 'news-list', 'guard_name' => 'web', 'module_name' => 'News']);
        // Permission::create(['name' => 'news-create', 'guard_name' => 'web', 'module_name' => 'News']);
        // Permission::create(['name' => 'news-edit', 'guard_name' => 'web', 'module_name' => 'News']);
        // Permission::create(['name' => 'news-delete', 'guard_name' => 'web', 'module_name' => 'News']);

    }
    public function index()
    {
        return view('content/apps/News/list');
    }

    public function create()
    {
        $news = "";
        $page_data['page_title'] = "news Add";
        $page_data['form_title'] = "Add New news";
        $NewsCategories = NewsCategory::where('status', '1')->get();
        return view('/content/apps/News/create-edit', compact('page_data', 'news', 'NewsCategories'));
    }
    public function getAll()
    {
        $news = $this->newsService->getAllNews();
        return DataTables::of($news)
            ->addColumn('categories_name', function ($row) {
                return $row->category ? $row->category->title : 'N/A';
            })

            ->addColumn('actions', function ($row) {
                $encryptedId = encrypt($row->id);
                // Update Button
                $updateButton = "<a class='btn btn-warning btn-sm ' href='" . route('app-news-edit', $encryptedId) . "'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"feather feather-edit ficon\"><path d=\"M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7\"></path><path d=\"M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z\"></path></svg> </a>";

                // Delete Button
                $deleteButton = "<a class='btn btn-danger btn-sm mx-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-news-delete', $encryptedId) . "'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"feather feather-trash-2 ficon\"><polyline points=\"3 6 5 6 21 6\"></polyline><path d=\"M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2\"></path><line x1=\"10\" y1=\"11\" x2=\"10\" y2=\"17\"></line><line x1=\"14\" y1=\"11\" x2=\"14\" y2=\"17\"></line></svg> </a>";
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
    public function store(CreateNewsRequest $request)
    {
        try {
            $newsData['title'] = $request->get('title');
            $newsData['long_description'] = $request->get('long_description');
            $newsData['date'] = $request->get('date');
            // $newsData['news_button_text'] = $request->get('news_button_text');
            $newsData['category_id'] = $request->get('category_id');
            $newsData['status'] = $request->get('status') === 'on' ? 1 : 0;
            if ($request->hasFile('file')) {
                $originalName = $request->file('file')->getClientOriginalName();
                $filename = str_replace(' ', '_', $originalName);
                $filePath = $request->file('file')->storeAs('news', $filename, 'public');
                $newsData['file'] = $filePath;
            }
            $news = $this->newsService->create($newsData);
            if (!empty($news)) {
                return redirect()->route('app-news-list')->with('success', 'News Added Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Adding News');
            }
        } catch (\Exception $error) {
            dd($error->getMessage());
            return redirect()->route('app-news-list')->with('error', 'Error while editing News');
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
            // dd($id);
            $news = $this->newsService->getNews($id);
            $page_data['page_title'] = "news Edit";
            $page_data['form_title'] = "Edit news";
            $NewsCategories = NewsCategory::where('status', '1')->get();
            return view('content/apps/News/create-edit', compact('page_data', 'news', 'NewsCategories'));
        } catch (\Exception $error) {
            return redirect()->route("app/News/list")->with('error', 'Error while editing News');
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $encrypted_id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateNewsRequest $request, $encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);

            $news = $this->newsService->getNews($id);
            $newsData['title'] = $request->get('title');
            $newsData['long_description'] = $request->get('long_description');
            $newsData['date'] = $request->get('date');
            // $newsData['news_button_text'] = $request->get('news_button_text');
            $newsData['category_id'] = $request->get('category_id');
            $newsData['status'] = $request->get('status') === 'on' ? 1 : 0;
            if ($request->hasFile('file')) {
                if ($news->file) {
                    Storage::disk('public')->delete($news->file);
                }

                $originalName = $request->file('file')->getClientOriginalName();
                $filename = str_replace(' ', '_', $originalName);
                $filePath = $request->file('file')->storeAs('news', $filename, 'public');

                $newsData['file'] = $filePath;
            }

            $updated = $this->newsService->updateNews($id, $newsData);
            if (!empty($updated)) {
                return redirect()->route("app-news-list")->with('success', 'News Updated Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Updating News');
            }
        } catch (\Exception $error) {
            dd($error->getMessage());
            return redirect()->route("app-news-list")->with('error', 'Error while editing News');
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
            $deleted = $this->newsService->deleteNews($id);
            if (!empty($deleted)) {
                return redirect()->route("app-news-list")->with('success', 'News Deleted Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Deleting News');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-news-list")->with('error', 'Error while editing News');
        }

    }
    public function bulkDelete(Request $request)
    {
        News::whereIn('id', $request->ids)->delete();

        return response()->json(['message' => 'News Categories deleted successfully.']);
    }
    public function destroyimage($id)
    {
        $news = News::findOrFail($id);
        // dd($news);
        if ($news->file) {
            $news->update(['file' => '']);
        }

        return redirect()->back()->with('success', 'Image deleted successfully.');
    }



}