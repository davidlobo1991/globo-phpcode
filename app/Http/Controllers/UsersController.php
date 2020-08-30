<?php

namespace App\Http\Controllers;

use App\Http\Notification\Facade\Toastr;
use App\User;
use Globobalear\Resellers\Models\ResellersType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Yajra\Datatables\Datatables;
use App\Role;
use App\Permission;
use App\RolePermission;
use Auth;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        if(Auth::user()->role->role_permission('view_users')){
            return view('users.index');
        }else{
            abort(403);
        }
    }

    /**
     * Get data info to display
     *
     * @param Datatables $datatables
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Datatables $datatables)
    {
        $query = User::select('users.id', 'users.name', 'users.email','resellers_types.name as resellertype','roles.guard_name as role')
        ->leftJoin('resellers_types', 'resellers_types.id', '=', 'users.resellers_type_id')
        ->leftJoin('roles', 'roles.id', '=', 'users.role_id')
        ->get();
        return $datatables->collection($query)
            ->addColumn('action', 'users.actions')
            ->make(true);
    }

    /**
     * Create a new resource in storage.
     *
     * @return View
     */
    public function create()
    {
        if(Auth::user()->role->role_permission('create_users')){
            $role = Role::pluck('guard_name', 'id')->toArray();
            $resellerType = ResellersType::pluck('name', 'id')->toArray();
            return view('users.create')
            ->with('role', $role)
            ->with('resellerType', $resellerType);
        }else{
            abort(403);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'resellers_type_id' => $request->resellers_type_id
        ]);

        Toastr::success('User create successfully');

        return redirect()->route('users.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User $user
     * @return Response
     */
    public function show(User $user)
    {
        if(Auth::user()->role->role_permission('show_users')){
        return redirect()->route('users.edit', $user->id);
        }else{
            abort(403);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User $user
     * @return View
     */
    public function edit(User $user)
    {
        if(Auth::user()->role->role_permission('edit_users')){
        $role = Role::pluck('guard_name', 'id')->toArray();
        $resellerType = ResellersType::pluck('name', 'id')->toArray();
        return view('users.edit')
            ->with('user', $user)
            ->with('role', $role)
            ->with('resellerType', $resellerType);
        }else{
            abort(403);
        }
    }


    /**
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->resellers_type_id = $request->resellers_type_id;
        $user->role_id = $request->role_id;

        if (isset($request->password)) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        Toastr::success('User updated successfully');

        return redirect()->route('users.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User $user
     * @return Response
     */
    public function destroy(User $user)
    {
        if(Auth::user()->role->role_permission('delete_users')){
        $user->delete();
        Toastr::success(trans('menu.user') . trans('flash.deleted') . trans('flash.successfully'), 'success');
        return redirect()->route('users.index');
        }else{
            abort(403);
        }
    }
}
