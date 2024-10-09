<?php

namespace App\Http\Controllers;
use App\Models\NewsCategory;
use App\Services\EventService;
use Illuminate\Http\Request;
use App\Http\Requests\SuccessStories\CreateSuccessStoriesRequest;
use App\Http\Requests\SuccessStories\UpdateSuccessStoriesRequest;
use App\Models\ClientType;
use \Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

class EventsController extends Controller
{

    protected $eventsService;


    public function __construct(EventService $events)
    {
        $this->successStoriesService = $events;
        $this->middleware('permission:events-list|events-create|events-edit|events-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:events-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:events-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:events-delete', ['only' => ['destroy']]);

        // Permission::create(['name' => 'events-list', 'guard_name' => 'web', 'module_name' => 'Events']);
        // Permission::create(['name' => 'events-create', 'guard_name' => 'web', 'module_name' => 'Events']);
        // Permission::create(['name' => 'events-edit', 'guard_name' => 'web', 'module_name' => 'Events']);
        // Permission::create(['name' => 'events-delete', 'guard_name' => 'web', 'module_name' => 'Events']);

    }

    public function index()
    {
        return view('content/apps/Events/list');
    }


}
