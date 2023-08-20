<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Infrapwd;

class InfrapwdController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $infrapwds = Infrapwd::with('users')->orderBy('displayname', 'ASC')->get();
        return response($infrapwds, 200);
    }

    public function userlogins()
    {
        $userlogins = auth()->user()->infrapwds()->get();

        return response($userlogins, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $infrapwd = Infrapwd::create([
            'displayname' => $request->displayname,
            'username' => $request->username,
            'password' => $request->password,
            'access_to_all' => $request->access_to_all,
        ]);

        if($request->input('users'))
        {
            $infrapwd->users()->attach($request->input('users'));
        }

        return response()->json([
            'message' => 'Логин успешно создан',
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
        $infrapwd = Infrapwd::with('users')->find($id);
        return $infrapwd;
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
        $infrapwd = Infrapwd::find($id);

        $infrapwd->displayname = $request->input('displayname');
        $infrapwd->username = $request->input('username');
        $infrapwd->password = $request->input('password');
        $infrapwd->access_to_all = $request->input('access_to_all');

        $infrapwd->update();

        $infrapwd->users()->detach();
        if($request->input('users'))
        {
            $infrapwd->users()->attach($request->input('users'));
        }

        return response()->json([
            'message' => 'Логин успешно обновлён'
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
        $infrapwd = Infrapwd::find($id);
        $infrapwd->users()->detach();
        $infrapwd->delete();

        return response()->json([
            'message' => 'Менеджер успешно удалён'
        ]);
    }
}
