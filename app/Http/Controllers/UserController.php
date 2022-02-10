<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Manager;
use App\Project;
use App\Infrapwd;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $users = User::with('manager')->orderBy('name', 'ASC')->get();
        return $users;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'rocketpwd' => $request->rocketpwd,
            'teampasspwd' => $request->teampasspwd,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);        

        if($request->input('infrapwd'))
        {
            $infrapassword = Infrapwd::create([
                'displayname' => $request->name,
                'username' => $request->username,
                'password' => $request->infrapwd,
            ]); 
            $infrapassword->users()->attach($user->id);
        }        

        if($request->input('projects'))
        {
            $user->projects()->attach($request->input('projects'));
        }

        return response()->json([
            'status' => 200,
            'name' => $user->name,
            'message'=>'Пользователь успешно создан'
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
        $user = User::with('projects')->find($id);

        return response()->json([
            'status' => 200,
            'user' => $user,
            // '123' => User::find($id)->infrapwds()->where('username', $user->username)->pluck('password')[0]
        ]);
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
        $user = User::find($id);

        $user->name = $request->input('name');
        $user->username = $request->input('username');
        $user->role = $request->input('role');
        $user->rocketpwd = $request->input('rocketpwd');
        $user->teampasspwd = $request->input('teampasspwd');

        $user->update();

        $user->projects()->detach();
        if($request->input('projects'))
        {
            $user->projects()->attach($request->input('projects'));
        }

        return response()->json([
            'message'=>'Пользователь успешно обновлён'
        ]);
    }

    public function changepassword(Request $request)
    {
        $user = User::find($request->userid);

        $user->password = Hash::make($request->password);

        $user->update();

        return response()->json([
            'message' => 'Пароль успешно изменён. При следующем входе в систему, используйте новый пароль',
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
        $user = User::find($id);
        $user->projects()->detach();
        $user->infrapwds()->delete();
        $user->infrapwds()->detach();
        $user->delete();

        return response()->json([
            'message'=>'Пользователь успешно удалён',
            'infrapwds'=>$user->infrapwds()->get()
        ]);
    }
}
