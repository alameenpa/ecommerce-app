<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            return datatables()->of($this->userRepository->getUsers())
                ->addColumn('action', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip" onclick="editFunc(' . $row->id . ')" data-original-title="Edit" class="edit btn btn-success edit">Edit</a>';
                    $btn = $btn . ' <a href="javascript:void(0);" id="delete-student" onclick="deleteFunc(' . $row->id . ')" data-toggle="tooltip" data-original-title="Delete" class="delete btn btn-danger">Delete</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('users.index');
    }

    /**
     * Store a newly created resource in storage.
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
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $user = $this->userRepository->getUser($request->id);
        return Response()->json($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
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
