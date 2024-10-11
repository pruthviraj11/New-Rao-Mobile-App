<?php

namespace App\Http\Controllers;
use App\Models\NewsCategory;
use App\Services\UserDocumentsService;
use Illuminate\Http\Request;
use App\Http\Requests\ApplicationStatuses\CreateApplicationStatusesRequest;
use App\Http\Requests\ApplicationStatuses\UpdateApplicationStatusesRequest;
use App\Models\ClientType;
use \Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

class UserDocumentsController extends Controller
{
    protected $userDocumentsService;

    public function __construct(UserDocumentsService $userDocumentsService)
    {
        $this->userDocumentsService = $userDocumentsService;
        $this->middleware('permission:user-documents-list|user-documents-create|user-documents-edit|user-documents-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:user-documents-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:user-documents-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:user-documents-delete', ['only' => ['destroy']]);

        // Permission::create(['name' => 'user-documents-list', 'guard_name' => 'web', 'module_name' => 'User Documents']);
        // Permission::create(['name' => 'user-documents-create', 'guard_name' => 'web', 'module_name' => 'User Documents']);
        // Permission::create(['name' => 'user-documents-edit', 'guard_name' => 'web', 'module_name' => 'User Documents']);
        // Permission::create(['name' => 'user-documents-delete', 'guard_name' => 'web', 'module_name' => 'User Documents']);

    }

    public function index()
    {
        return view('content/apps/UserDocuments/list');
    }

    public function getAll()
    {
        $userDocuments = $this->userDocumentsService->getAllUserDocuments();
        return DataTables::of($userDocuments)
            ->addColumn('client_type', function ($row) {
                return $row->clientType ? $row->clientType->displayname : 'N/A';
            })
            ->addColumn('actions', function ($row) {
                $encryptedId = encrypt($row->id);
                // Update Button
                $updateButton = "<a class='btn btn-warning btn-sm ' href='" . route('app-user-documents-edit', $encryptedId) . "'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"feather feather-edit ficon\"><path d=\"M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7\"></path><path d=\"M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z\"></path></svg> </a>";

                // Delete Button
                $deleteButton = "<a class='btn btn-danger btn-sm mx-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-user-documents-delete', $encryptedId) . "'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"feather feather-trash-2 ficon\"><polyline points=\"3 6 5 6 21 6\"></polyline><path d=\"M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2\"></path><line x1=\"10\" y1=\"11\" x2=\"10\" y2=\"17\"></line><line x1=\"14\" y1=\"11\" x2=\"14\" y2=\"17\"></line></svg> </a>";
                $buttons = $updateButton . " " . $deleteButton;
                return "<div class='d-flex justify-content-start'>" . $buttons . "</div>";
            })
            ->rawColumns(['actions'])->make(true);
    }
}
