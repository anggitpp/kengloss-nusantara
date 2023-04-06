<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\UserRequest;
use App\Models\Employee\Employee;
use App\Models\Setting\AppMasterData;
use App\Models\Setting\AppParameter;
use App\Models\Setting\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse as RedirectResponseAlias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;

ini_set('memory_limit', '4096M');

class UserController extends Controller
{
    public string $photoPath;
    public array $statusOption;
    public array $roles;
    public array $badges;

    public function __construct()
    {
        $this->middleware('auth');
        $this->photoPath = '/uploads/user/';
        $this->statusOption = defaultStatus();
        $this->badges = getAllBadges();

        \View::share('statusOption', $this->statusOption);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|JsonResponse
     */
    public function index(Request $request)
    {
        $isSuperadmin = Auth::user()->hasRole('Superadmin');
        $superAdminId = AppParameter::whereCode('SUPERADMIN')->first()->value;
        if($request->ajax()){
            $filter = $request->get('search')['value'];
            $user = User::with('role')->select(['id', 'name', 'email', 'role_id', 'last_login', 'created_at', 'status', 'photo']);
            if (!$isSuperadmin) $user->where('role_id', '!=', $superAdminId);
            return DataTables::of($user)
                ->filter(function ($query) use ($filter) {
                    if (isset($filter)) $query->where('name', 'like', "%{$filter}%")->orWhere('email', 'like', "%{$filter}%");
                })
                ->editColumn('name', function ($model) {
                    return view('components.datatables.photo-title-subtitle', [
                        'photo' => $model->photo,
                        'backgroundColor' => $this->badges[array_rand($this->badges)],
                        'textColor' => $this->badges[array_rand($this->badges)],
                        'title' => $model->name,
                        'subtitle' => $model->email,
                    ]);
                })
                ->editColumn('role_id', function ($model) {
                    return $model->role->name;
                })
                ->editColumn('last_login', function ($model) {
                    return view('components.datatables.badge', [
                        'value' => $model->last_login ? $model->last_login->diffForHumans() : '-',
                    ]);
                })
                ->editColumn('created_at', function ($model) {
                    return setDateTime($model->created_at, 'd M Y g:i A');
                })
                ->addColumn('action', function ($model) {
                    return view('components.views.action', [
                        'menu_path' => $this->menu_path(),
                        'url_edit' => route(str_replace('/', '.', $this->menu_path()).'.edit', $model->id),
                        'url_destroy' => route(str_replace('/', '.', $this->menu_path()).'.destroy', $model->id),
                        'url_slot' => route(str_replace('/', '.', $this->menu_path()).'.reset-password', $model->id),
                        'icon_slot' => 'fas fa-gear',
                    ]);
                })
                ->addColumn('status', function ($model) {
                    return view('components.views.status', [
                        'status' => $model->status,
                    ]);
                })
                ->addIndexColumn()
                ->make(true);
        }
        return view('settings.user.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        $isSuperadmin = Auth::user()->hasRole('Superadmin');
        $superAdminId = AppParameter::whereCode('SUPERADMIN')->first()->value;
        $roles = Role::where('status', 't');
        if (!$isSuperadmin) $roles->where('id', '!=', $superAdminId);
        $roles = $roles->pluck('name', 'id')->toArray();
//        $arrLocation = AppMasterData::whereAppMasterCategoryCode('ELK')->orderBy('order')->pluck('name', 'id')->toArray();

        return view('settings.user.form', [
//            'arrLocation' => $arrLocation,
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param UserRequest $request
     * @return JsonResponse
     */
    public function store(UserRequest $request)
    {
        $photo = '';
        if($request->hasFile('photo')) {
            $photo = $request->hasFile('photo') ? uploadFile(
                $request->file('photo'),
                Str::slug($request->input('name')) . '_' . time(),
                $this->photoPath, true) : '';
        }

//        $access_locations = '';
//        if(is_array($request->input('access_locations'))) $access_locations = implode(',', $request->input('access_locations'));

        $user = User::create([
            'name' => $request->input('name'),
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'description' => $request->input('description'),
            'employee_id' => '0',
            'role_id' => $request->input('role_id'),
            'photo' => $photo,
            'status' => $request->input('status'),
//            'access_locations' => $access_locations,
        ]);


        $user->assignRole($request->input('role_id'));

        return response()->json([
            'success'=>'User berhasil disimpan',
            'url'=> route('settings.users.index')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Application|Factory|View
     */
    public function edit(int $id)
    {
        $data['user'] = User::findOrFail($id);
        $data['arrLocation'] = AppMasterData::whereAppMasterCategoryCode('ELK')->orderBy('order')->pluck('name', 'id')->toArray();
        $data['locations'] = explode(',', $data['user']->access_locations);
        $isSuperadmin = Auth::user()->hasRole('Superadmin');
        $superAdminId = AppParameter::whereCode('SUPERADMIN')->first()->value;
        $roles = Role::where('status', 't');
        if (!$isSuperadmin) $roles->where('id', '!=', $superAdminId);
        $data['roles'] = $roles->pluck('name', 'id')->toArray();

        return view('settings.user.form', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UserRequest $request, int $id)
    {
        $access_locations = '';
        if(is_array($request->input('access_locations'))) $access_locations = implode(',', $request->input('access_locations'));

        $user = User::find($id);
        $photo = $user->photo;
        if($request->hasFile('photo')) $photo = UploadFile($request->file('photo'), Str::slug($request->input('name')).'_'.time(), $this->photoPath, true);
        $user->update([
            'name' => $request->input('name'),
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'description' => $request->input('description'),
            'photo' => $photo,
            'employee_id' => $request->input('employee_id'),
            'role_id' => $request->input('role_id'),
            'status' => $request->input('status'),
            'access_locations' => $access_locations,
        ]);

        $user->roles()->detach();
        $user->assignRole($request->input('role_id'));

        return response()->json([
            'success'=>'User berhasil disimpan',
            'url'=> route('settings.users.index')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return RedirectResponseAlias
     */
    public function destroy(int $id)
    {
        $user = User::find($id);
        if($user->photo != null)
            Storage::delete($user->photo);
        $user->roles()->detach();
        $user->delete();

        Alert::success('Success', 'Data user berhasil dihapus!');

        return redirect()->back();
    }

    public function resetPassword(int $id)
    {
        return view('settings.user.reset-password', [
            'id' => $id
        ]);
    }

    public function updatePassword(Request $request, int $id)
    {
        $request->validate([
            'password' => 'required',
        ]);

        $user = User::find($id);
        $user->update([
            'password' => Hash::make($request->input('password')),
        ]);

        return response()->json([
            'success'=>'Password berhasil direset',
            'url'=> route('settings.users.index')
        ]);
    }
}
