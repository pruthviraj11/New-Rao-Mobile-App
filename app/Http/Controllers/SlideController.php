<?php

namespace App\Http\Controllers;
use App\Models\Slide;
use App\Services\SliderService;
use Illuminate\Http\Request;
use App\Http\Requests\Slider\CreateSliderRequest;
use App\Http\Requests\Slider\UpdateSliderRequest;
use \Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

class SlideController extends Controller
{
    protected $sliderService;

    public function __construct(SliderService $slider)
    {
        $this->sliderService = $slider;
        $this->middleware('permission:sliders-list|sliders-create|sliders-edit|sliders-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:sliders-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:sliders-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:sliders-delete', ['only' => ['destroy']]);

        // Permission::create(['name' => 'sliders-list', 'guard_name' => 'web', 'module_name' => 'Slider']);
        // Permission::create(['name' => 'sliders-create', 'guard_name' => 'web', 'module_name' => 'Slider']);
        // Permission::create(['name' => 'sliders-edit', 'guard_name' => 'web', 'module_name' => 'Slider']);
        // Permission::create(['name' => 'sliders-delete', 'guard_name' => 'web', 'module_name' => 'Slider']);

    }
    public function index()
    {
        return view('content/apps/sliders/list');
    }

    public function create()
    {
        $slider = "";
        $page_data['page_title'] = "Sliders Add";
        $page_data['form_title'] = "Add New Sliders";
        return view('/content/apps/sliders/create-edit', compact('page_data', 'slider'));
    }
    public function getAll()
    {
        $slider = $this->sliderService->getAllSlider();
        return DataTables::of($slider)

            ->addColumn('actions', function ($row) {
                $encryptedId = encrypt($row->id);
                // Update Button
                $updateButton = "<a class='btn btn-warning btn-sm ' href='" . route('app-sliders-edit', $encryptedId) . "'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"feather feather-edit ficon\"><path d=\"M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7\"></path><path d=\"M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z\"></path></svg> </a>";

                // Delete Button
                $deleteButton = "<a class='btn btn-danger btn-sm  confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-sliders-delete', $encryptedId) . "'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"feather feather-trash-2 ficon\"><polyline points=\"3 6 5 6 21 6\"></polyline><path d=\"M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2\"></path><line x1=\"10\" y1=\"11\" x2=\"10\" y2=\"17\"></line><line x1=\"14\" y1=\"11\" x2=\"14\" y2=\"17\"></line></svg> </a>";
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
    public function store(CreateSliderRequest $request)
    {
        try {
            $sliderData['name'] = $request->get('name');
            $sliderData['sequence'] = $request->get('sequence');
            $sliderData['status'] = $request->get('status') === 'on' ? 1 : 0;
            if ($request->hasFile('image')) {
                $originalName = $request->file('image')->getClientOriginalName();
                $filename = str_replace(' ', '_', $originalName);
                $imagePath = $request->file('image')->storeAs('sliders', $filename, 'public');
                $sliderData['image'] = $imagePath;
            }
            $slider = $this->sliderService->create($sliderData);
            if (!empty($slider)) {
                return redirect()->route('app-sliders-list')->with('success', 'Slider Added Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Adding Slider');
            }
        } catch (\Exception $error) {
            dd($error->getMessage());
            return redirect()->route('app-sliders-list')->with('error', 'Error while editing Slider');
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
            $slider = $this->sliderService->getSlider($id);
            $page_data['page_title'] = "Sliders Edit";
            $page_data['form_title'] = "Edit Sliders";
            return view('content/apps/sliders/create-edit', compact('page_data', 'slider'));
        } catch (\Exception $error) {
            return redirect()->route("app/sliders/list")->with('error', 'Error while editing Sliders');
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $encrypted_id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSliderRequest $request, $encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);

            $slider = $this->sliderService->getSlider($id);
            $sliderData['name'] = $request->get('name');
            $sliderData['sequence'] = $request->get('sequence');
            $sliderData['status'] = $request->get('status') === 'on' ? 1 : 0;
            if ($request->hasFile('image')) {
                if ($slider->image) {
                    Storage::disk('public')->delete($slider->image);
                }

                $originalName = $request->file('image')->getClientOriginalName();
                $filename = str_replace(' ', '_', $originalName);
                $imagePath = $request->file('image')->storeAs('sliders', $filename, 'public');

                $sliderData['image'] = $imagePath;
            }

            $updated = $this->sliderService->updateSlider($id, $sliderData);
            if (!empty($updated)) {
                return redirect()->route("app-sliders-list")->with('success', 'Sliders Updated Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Updating Sliders');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-sliders-list")->with('error', 'Error while editing Sliders');
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
            $deleted = $this->sliderService->deleteSlider($id);
            if (!empty($deleted)) {
                return redirect()->route("app-sliders-list")->with('success', 'Sliders Deleted Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Deleting Sliders');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-sliders-list")->with('error', 'Error while editing Sliders');
        }
    }

    public function destroyimage($id)
    {
        $slider = Slide::findOrFail($id);

        if ($slider->image) {

            // Storage::delete($slider->image);
            $slider->update(['image' => null]);
        }

        return redirect()->back()->with('success', 'Image deleted successfully.');
    }


}