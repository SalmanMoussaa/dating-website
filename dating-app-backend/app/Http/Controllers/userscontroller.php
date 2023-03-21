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
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        $credentials = $request->only('email', 'password');

        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::user();
        return response()->json([
                'status' => 'success',
                'user' => $user,
                'authorisation' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
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
    function sendmessage(Request $request, $sender_id,$receiver_id){
        $sender = User::find($sender_id);
        $receiver= User::find($receiver_id);
        if (!$sender||!$receiver ){
            return response()->json(['message' => 'Invalid sender or receiver ID']);
        }
        
        $message = new messages;
        $message->receiver_id = $receiver_id;
        $message->sender_id = $sender_id;
        $message->content = $request->content;
        $message->save();
        return response()->json(['message'=>'message sent succefully']);
      }  
    
      // Like and dislike function
      function likeuser(Request $request, $sender_id,$receiver_id){
        $sender = User::find($sender_id);
        $receiver= User::find($receiver_id);
        if (!$sender||!$receiver ){
            return response()->json(['message' => 'Invalid sender or receiver ID']);
        }
        $like= favorites::where('receiver_id', $receiver_id)->where('sender_id', $sender_id)->first();
        if ($like){
          $like->delete();
            return response()->json(['message'=>'user removed from favorites']);
        }
        else{
        $like = new favorites;
        $like->receiver_id = $receiver_id;
        $like->sender_id = $sender_id;
        $like->save();
        return response()->json(['message'=>'user added to favorites']);
      }
    
      }
       // block function
      function blockuser(Request $request, $sender_id,$receiver_id){
        $sender = User::find($sender_id);
        $receiver= User::find($receiver_id);
        if (!$sender||!$receiver ){
            return response()->json(['message' => 'Invalid sender or receiver ID']);
        }
        $block= blocks::where("sender_id", $sender_id)->where("receiver_id", $receiver_id)->first();
        if($block){
          $block->delete();
          return response()->json(['message'=>'user unblocked']);
        }
        else{
        $block = new blocks;
        $block->receiver_id = $receiver_id;
        $block->sender_id = $sender_id;
        $block->save();
        return response()->json(['message'=>'user blocked']);
        }
      }
    
      }



