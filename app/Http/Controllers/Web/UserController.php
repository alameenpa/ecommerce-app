<?php

namespace App\Http\Controllers\Web;

use App\DataTables\UsersDataTable;
use App\Http\Controllers\Controller;
use App\Repository\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->middleware('auth');
        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of users with initial form.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(UsersDataTable $dataTable)
    {
        return $dataTable->render('users.index');
    }

    /**
     * Store a newly created user or update an existing user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $userId = $request->id;
            $inputArray = [
                'name' => ucwords($request->name),
                'email' => $request->email,
            ];
            if ($userId == null) {
                $inputArray['password'] = Hash::make(rand());
            }
            $user = $this->userRepository->saveUser($userId, $inputArray);
            return Response()->json(array('success' => true));
        } catch (\Exception $e) {
            return response()->json(array('success' => false, 'message' => 'Operation Failed, please contact admin'));
        }
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $user = $this->userRepository->getUser($request->id);
        return Response()->json($user);
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try {
            $status = $this->userRepository->destroyUser($request->id);
            return Response()->json(array('success' => true));
        } catch (\Exception $e) {
            return response()->json(array('success' => false, 'message' => 'Operation Failed, please contact admin'));
        }
    }
}