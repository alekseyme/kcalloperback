<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Project;
use App\User;
use DB;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $projects = Project::with('users')->orderBy('name', 'ASC')->get();
        return response($projects, 200);
    }

    public function userprojects()
    {   
        if (auth()->user()->role === 0) {
            $projects = Project::orderBy('name', 'ASC')->get();
            return response($projects, 200);
        }

        $projects = auth()->user()->projects()->orderBy('name', 'ASC')->get();

        return response($projects, 200);
    }    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $project = Project::create([
            'name' => $request->name,
            'type' => $request->type,
            'scriptlink' => $request->scriptlink,
        ]);

        if($request->input('users'))
        {
            $project->users()->attach($request->input('users'));
        }

        return response()->json([
            'status' => 200,
            'name' => $project->name,
            'message' => 'Проект успешно создан',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $project = Project::with('users')->find($id);
        return $project;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $project = Project::find($id);

        $project->name = $request->input('name');
        $project->type = $request->input('type');
        $project->scriptlink = $request->input('scriptlink');

        $project->update();

        $project->users()->detach();
        if($request->input('users'))
        {
            $project->users()->attach($request->input('users'));
        }

        return response()->json([
            'message' => 'Проект успешно обновлён'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {   
        $project = Project::find($id);
        $project->users()->detach();
        $project->delete();

        return response()->json([
            'message' => 'Проект успешно удалён'
        ]);
    }
}
