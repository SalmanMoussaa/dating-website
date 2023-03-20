<?php

namespace App\Http\Controllers;
use Auth;
use Validator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class userscontroller extends Controller
{
    //

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Register user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|min:2|max:100',
        'email' => 'required|string|email|max:100|unique:users',
        'password' => 'required|string|min:6',
        'dob' => 'required|integer|min:2|max:120',
        'location' => 'required|string|min:2|max:100',
        'gender' => 'required|integer',
        'bio' => 'required|string|min:2|max:1000',
        'profile_picture' => 'required|string'
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 400);
    }

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'location' => $request->location,
        'dob' => $request->dob,
        'gender' => $request->gender,
        'bio' => $request->bio,
    ]);

    if ($request->profile_picture) {
        $encoded = $request->profile_picture;
        $id = $user->id;

        $decoded = base64_decode($encoded);

        $file_path = public_path('images/' . $id . '.png');

        file_put_contents($file_path, $decoded);

        $user->pic = 'http://localhost/images/' . $id . '.png';
        $user->save();
    }

    return response()->json([
        'message' => 'User successfully registered',
        'user' => $user
    ], 201);
}


    /**
     * login user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',

        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (!$token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user_id = auth()->user()->id;
        return $this->respondWithTokenAndId($token, $user_id);
    }

    private function respondWithTokenAndId($token, $user_id)
{
    return response()->json([
        'access_token' => $token,
        'token_type' => 'bearer',
        'user_id' => $user_id,
    ]);
}



    /**
     * Logout user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'User successfully logged out.']);
    }

    /**
     * Refresh token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get user profile.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile()
    {
        return response()->json(auth()->user());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
    function getoppgender($id){
        $user = User::find($id);
        if($user->gender == "1"){
            $oppositegender = "0";
        }else{
             $oppositegender = "1";
        }
        $users = User::where('gender',$oppositegender)->get();
        return response()->json([
            "users" => $users
        ]);
    }
    // get user
    function getuser($id){
        $user = user::find($id);
        return response()->json([
            "user" => $user
        ]);
    }
    // display sent and received messages
    function getmessage($sender_id, $receiver_id){
        $messages = Messages::where(function ($query) use ($sender_id, $receiver_id) {
            $query->where('sender_id', $sender_id)
                  ->where('receiver_id', $receiver_id);
        })->orWhere(function ($query) use ($sender_id, $receiver_id) {
            $query->where('sender_id', $receiver_id)
                  ->where('receiver_id', $sender_id);
        })->get();
        
        return response()->json([
            "messages" =>  $messages
        ]);
    }
    // display blocked users
    function getblocks($sender_id, $receiver_id){
        $block = blocks::where('sender_id', $sender_id)->where('receiver_id', $receiver_id)->get();
        return response()->json([
            "block" =>  $block
        ]);
    }
    
    // display liked
    function getfavorites($sender_id, $receiver_id){
       $favorites = favorites::where('sender_id',$sender_id)->where( 'receiver_id' ,$receiver_id)->get();
       return response()->json(['favorites'=> $favorites]);
    }
    // edit profile 
    function editprofile(Request $request)
    {
        $user = User::find($request->id);

        $user->update([
            "name" =>$request->name,
            "email" => $request->email,
            "age" => $request->age,
            "location"=>$request->location,
            "bio"=>$request->bio
        ]);

        return response()->json(['message' => 'User profile updated successfully', 'user' => $user]);
    }
    // upload image
    function uploadImage(Request $request){
        $encoded = $request->encoded;
        $name = $request->name;

        $decoded = base64_decode($encoded);

        $file_path = public_path('images/'. $name . '.png');

        file_put_contents($file_path,$decoded);

        User::where("name",$name)->update("pic", "http://localhost/images" . $name . ".png");
    }
}


