<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Gate;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Gate::any(['isAdmin', 'isAuthor'])) {
            return User::latest()->paginate(1);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => "required|string|max:191",
            'email' => "required|string|email|max:191|unique:users",
            'password' => "required|string|min:6",
        ]);

        return User::create([
            'name' => $request->name,
            'email' => $request->email,
            'bio' => $request->bio,
            'type' => $request->type,
            'password' => Hash::make($request->password),

        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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

        $user = User::findOrFail($id);
        $this->validate($request, [
            'name' => "required|string|max:191",
            'email' => "required|string|email|max:191|unique:users,email," . $user->id,
            'password' => "sometimes|string|min:6",
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'bio' => $request->bio,
            'type' => $request->type,
            'photo' => $request->photo,
            'password' => Hash::make($request->password),

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
        $this->authorize('isAdmin');
        User::findOrFail($id)->delete();
    }

    public function profile()
    {
        return Auth('api')->user();
    }

    public function updateProfile(Request $request)
    {
        $user = Auth('api')->user();

        $this->validate($request, [
            'name' => "required|string|max:191",
            'email' => "required|string|email|max:191|unique:users,email," . $user->id,
        ]);


        $currentPhoto = $user->photo;
        if ($request->photo != $currentPhoto) {

            $name = time() . '.' . explode('/', explode(':', substr($request->photo, 0, strpos($request->photo, ';')))[1])[1];
            \Image::make($request->photo)->resize(128, 128)->save(public_path('img/userprofile/') . $name);

            $request->merge(['photo' => $name]);


            $userOldPhoto =  public_path('img/userprofile/') . $currentPhoto;
            if ($currentPhoto != "profile.png") {
                if (file_exists($userOldPhoto)) {
                    unlink($userOldPhoto);
                }
            }
        }

        if (!empty($request->password)) {
            $this->validate($request, [
                'password' => "required|string|min:6",
            ]);
            $request->merge(['password' => Hash::make($request->password)]);
            $user->update($request->all());
        }



        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'bio' => $request->bio,
            'photo' => $request->photo,
        ]);

        return ['meassage' => 'success'];
    }

    public function search()
    {
        if ($search = Request::get('q')) {
            $result = User::where('name', 'LIKE', "%$search%")
                ->orWhere('email', 'LIKE', "%$search%")
                ->orWhere('type', 'LIKE', "%$search%")
                ->paginate(1);
        }
        return $result;
    }
}
