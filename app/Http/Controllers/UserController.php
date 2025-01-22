<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{

    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param string $direction
     * @return Application|Factory|View
     */
    public function index(Request $request): View|Factory|Application
    {
        return view('user.index', [
            'users' => User::all(),
            'roles' => Role::all(),
            'contacts' => Contact::all(),
        ]);
    }

    /**
     * @param User $user
     * @param Request $request
     * @return array
     */
    public function show(User $user, Request $request)
    {
        return $user->only(['username', 'name', 'id', 'contact_id', 'roles', 'email']);
    }

    public function update(User $user, Request $request)
    {
        $request->validate([
            'username' => 'required',
            'name' => 'required',
            'email' => 'email|nullable',
            'password' => 'confirmed|nullable',
            'role_id' => 'required|integer|exists:roles,id',
            'contact_id' => 'nullable|integer|exists:contacts,id'
        ]);

        try {
            $role = Role::where('id', $request->get('role_id'))->first();
            $user->roles()->detach();
            $user->roles()->attach($role);

            $user->update([
                'username' => $request->get('username'),
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'contact_id' => $request->get('contact_id', null)
            ]);

            if ($request->get('password', false)) {
                $user->update([
                    'password' => Hash::make($request->input('password'))
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'User updated'
            ]);

        } catch (QueryException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->errorInfo
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users',
            'name' => 'required',
            'email' => 'email|nullable',
            'password' => 'required|confirmed',
            'role_id' => 'required|integer',
            'contact_id' => 'nullable|integer|exists:contacts,id'
        ]);

        try {
            $role = Role::where('id', $request->input('role_id'))->first();

            $user = new User();
            $user->username = $request->input('username');
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->contact_id = $request->input('contact_id');
            $user->password = Hash::make($request->input('password'));
            $user->save();

            $user->roles()->attach($role);

            return response()->json([
                'success' => true,
                'message' => 'User created'
            ]);

        } catch (QueryException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->errorInfo
            ]);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request)
    {
        try {
            User::findOrFail($request->id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'User has been deleted successfully!'
            ]);
        } catch (QueryException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->errorInfo
            ]);
        }

    }

    /**
     * Function for logging in through QR code
     * @param Request $request
     * @return RedirectResponse
     */
    public function qrLogin(Request $request): RedirectResponse
    {
        $request->validate([
            'qrcode' => 'required|string'
        ]);

        $user = User::where('qrcode', md5($request->get('qrcode')))->first();

        if ($user) {
            Auth::login($user);
            return redirect()->route('scanner');
        }

        abort(401, 'This action is unauthorized.');
    }

    /**
     * Function for generating a new QR code for users
     * @param User $user
     * @param Request $request
     * @return Application|Factory|View
     */
    public function generateQr(User $user, Request $request): View|Factory|Application
    {
        if ($request->user()->hasRole('admin')) {
            $randomString = Str::random(20);

            $user->update([
                'qrcode' => md5($randomString)
            ]);

            return view('documents.qrcode', [
                'string' => $randomString
            ]);
        }

        abort(401, 'This action is unauthorized.');
    }

    public function generateApiToken(Request $request)
    {
        if ($request->user()->hasRole('admin')) {
            $validatedData = $request->validate([
                'id' => 'required|integer|exists:users,id'
            ]);
            $user = User::find($validatedData['id']);
            $token = Str::random(80);
            $user->update([
                'api_token' => hash('sha256', $token),
            ]);
            return response()->json([
                'success' => true,
                'message' => 'API token generated',
                'api_token' => $token
            ]);
        } else {
            abort(401, 'This action is unauthorized.');
        }
    }


}
