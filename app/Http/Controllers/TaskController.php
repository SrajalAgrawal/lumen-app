<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Task;
use App\Http\Middleware\isAdmin;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;


class TaskController extends Controller
{
    public function createTask(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string|max:40',
            'desc' => 'required|string|max:200',
            'assigned_to' => 'required',
            'due_date' => 'required|date',
        ]);
        $task = new Task;
        $to = User::findorfail($request->assigned_to);

        $task->status = 'assigned';
        $task->desc = $request->desc;
        $task->title = $request->title;
        $task->due_date = $request->due_date;
        $task->assigned_to_name = ($to->name);
        $task->assigned_by_name = (auth()->user()->name);
        $task->assigned_to = $request->assigned_to;
        $task->assigned_by = (auth()->user()->id);

        if($to->name){
            $task->save();
            return response()->json($task, 200);
        }
        return response()->json("Error Creating Task", 200);
    }

    public function getTasks(Request $request)
    {
        $this->validate($request, [
            'search' => 'string|max:50',
            'sort' => ['required', Rule::in(['title','due_date','assigned_by','assigned_to','status']),],
        ]);

        $filter = $request->search;
        $sort = $request->sort;

        $results = DB::table('tasks')
            ->where(function ($query) {
                if(auth()->user()->role!=1)
                {
                    $query->where('assigned_to', '=', auth()->user()->id)
                        ->orWhere('assigned_by', '=', auth()->user()->id);
                }
            })
            ->where(function ($query) use ($filter) {
                $query->where('assigned_to_name', 'LIKE', "%{$filter}%")
                    ->orWhere('assigned_by_name', 'LIKE', "%{$filter}%")
                    ->orWhere('status', 'LIKE', "%{$filter}%")
                    ->orWhere('desc', 'LIKE', "%{$filter}%")
                    ->orWhere('title', 'LIKE', "%{$filter}%");
            })->orderBy($sort,"asc")
            ->paginate(10);
        return response()->json($results);

    }

    public function statistics(Request $request)
    {
        $this->validate($request, [
            'type' => ['required',Rule::in(['assigned_to','assigned_by']),],
        ]);
        
        $Completed= Task::where($request->type, '=', auth()->user()->id)
                        ->where('status','Completed')->count();
        $Assigned = Task::where($request->type, '=', auth()->user()->id)
                        ->where('status','assigned')->count();
        $InProgress= Task::where($request->type, '=', auth()->user()->id)
                        ->where('status','In Progress')->count();

        return response([$Assigned, $InProgress, $Completed], 200);
    }

    public function changeStatus(Request $request)
    {
        $this->validate($request, [
            'status_change_to' => ['required',Rule::in(['Completed','In Progress']),],
            'task' => 'required|integer',
        ]);

        $user = auth()->user();
        $task = Task::findOrFail($request->task);

        if ($user->role == 1 || $task->assigned_by == $user->id || $task->assigned_to == $user->id) {
            $task->status = $request->status_change_to;
            $task->save();
            return response("Changed");
        }
        return response("not Authorized", 401);
    }

    public function changeStatusBulk(Request $request)
    {
        $this->validate($request, [
            'bulkAction' => ['required',Rule::in(['Completed','In Progress']),],
        ]);
        Task::whereIn('id', $request->idArr)->update(['status' => $request->bulkAction]);
        return response($request);
    }
}