<?php
namespace App\Http\Controllers\Api;
use App\Models\Comment;
use App\Models\ForumPost;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;


class ApiController extends Controller
{
    public function updateComment(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json(['status' => false, 'message' => 'Comment not found'], 404);
        }

        if ($comment->author_id !== Auth::id() && Auth::user()->role_id !== 1) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $comment->content = $request->content;
        $comment->save();

        return response()->json(['status' => true, 'message' => 'Comment updated successfully']);
    }

    public function deleteComment($id)
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json(['status' => false, 'message' => 'Comment not found'], 404);
        }

        if ($comment->author_id !== Auth::id() && Auth::user()->role_id !== 1) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json(['status' => true, 'message' => 'Comment deleted successfully']);
    }
    public function register(Request $request){
        $request->validate([
            "username" => "required",
            "name"=> "required",
            "email"=> "required|email|unique:users",
            "password"=>"required|confirmed",
        ]);

        User::create([
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'status'=> true,
            'message'=>'User created successfully',
        ]);
    }
    public function login(Request $request){
        $request->validate([
            "email"=> "required|email",
            "password"=>"required",
        ]);

        if(Auth::attempt([
            "email"=> $request->email,
            "password"=>$request->password
        ])){

            $user = Auth::user();

            $token = $user->createToken("myToken")->accessToken;

            return response()->json([
                'status'=> true,
                'message'=>'User logged in successfully',
                'token'=> $token,
                'name'=> $user->name // Return the user's name
            ]);

        }else{
            return response()->json([
                'status'=> false,
                'message'=>'Invalid login details',
            ]);
        }
    }
    public function profile(){
        $user = Auth::user();

        return response()->json([
            'status' => true,
            'message' => 'User profile',
            'data' => $user
        ]);
    }
    public function logout(){

        auth()->user()->token()->revoke();

        return response()->json([
            'status' => true,
            'message' => 'User logged out'
        ]);
    }

    public function getUserProfile(){
        $userData = User::where('id', Auth::user()->id)->first();
        return response()->json([
            'status' => true,
            'userData' => $userData
        ]);
    }
    public function updateProfile(Request $request){
        $user = Auth::user(); // Assuming the user is authenticated
        $user->update(['bio' => $request->input('bio')]);
        $user->update(['username' => $request->input('username')]);

        return response()->json([
           'status' => true,
           'message' => 'Profile updated successfully',
        ]);
     }

     public function deleteProfile(Request $request){
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $user->delete();
        auth()->user()->token()->revoke();

        return response()->json([
            'status' => true,
            'message' => 'Profile deleted',
        ]);
    }
    public function createForumPost(Request $request){
        $user = Auth::user();

        $request->validate([
            'nosaukums' => 'required|string|max:50',
            'saturs' => 'required|string|max:5000',
        ]);

        $date = Carbon::now();

        $forumPost = ForumPost::create([
        'nosaukums' => $request->nosaukums,
        'saturs' => $request->saturs,
        'author_id' => $user->id, // Use the authenticated user's ID as the author
        'date' => $date,
         ]);

        return response()->json([
            'status' => true,
            'message' => 'Forum post created successfully',
            'data' => $forumPost,
        ]);
    }
    // paņem kādu id tu gribi atrast
    public function getForumPost(Request $id){

        $user = Auth::user();
        $id = $id->id;

        $post = ForumPost::find($id);

        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Forum post not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Forum post found',
            'data' => $post,
        ]);
    }

    public function getAllForumPosts() {
        $user = Auth::user();
        $forumPosts = ForumPost::with('author')->orderBy('updated_at', 'desc')->get();

        return response()->json([
            'status' => true,
            'message' => 'All forum posts retrieved successfully',
            'data' => $forumPosts,
        ]);
    }
    public function createComment(Request $request){
        $request->validate([
            'post_id' => 'required|exists:forum_posts,id',
            'author_id' => 'required|exists:users,id',
            'content' => 'required|string|max:1000',
        ]);

        $comment = Comment::create([
            'post_id' => $request->post_id,
            'author_id' => $request->author_id,
            'content' => $request->content,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Comment created successfully',
            'data' => $comment,
        ]);
    }

    public function getComments(Request $request){
        if (!Auth::check()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $request->validate([
            'post_id' => 'required|exists:forum_posts,id',
        ]);

        $comments = Comment::where('post_id', $request->post_id)->with('author')->get();

        return response()->json([
            'status' => true,
            'message' => 'Comments retrieved successfully',
            'comments' => $comments, // Ensure the key is 'comments'
        ]);
    }

    public function deletePost($id)
{
    $user = Auth::user();
    $post = ForumPost::find($id);

    if (!$post) {
        return response()->json([
            'status' => false,
            'message' => 'Forum post not found',
        ], 404);
    }

    if ($user->id != $post->autors && $user->role_id != 1) {
        return response()->json([
            'status' => false,
            'message' => 'Unauthorized to delete this post',
        ], 403);
    }

    $post->delete();

    return response()->json([
        'status' => true,
        'message' => 'Forum post deleted successfully',
    ]);
}
public function uploadFile(Request $request)
{
    $user = Auth::user();

    if ($user->role_id!= 1) {
        return response()->json([
            'status' => false,
            'message' => 'Unauthorized',
        ], 403);
    }

    $request->validate([
        'file' => 'required|file|max:10240',
    ]);

    // Save the file locally in the 'uploads' directory within 'storage/app/public'
    $path = $request->file('file')->store('uploads', 'app');

    // Generate an absolute URL for the file using Laravel's Storage facade
    $url = Storage::url($path);

    return response()->json([
        'status' => true,
        'message' => 'File uploaded successfully',
        'url' => $url, // Return the absolute URL of the file
    ]);
}
public function deleteFile(Request $request)
{
    $user = Auth::user();

    if ($user->role_id!= 1) { // Check if the user is an admin
        return response()->json([
            'status' => false,
            'message' => 'Unauthorized',
        ], 403);
    }

    $request->validate([
        'url' => 'required|string',
    ]);

    // Correctly extract the file path from the URL
    // Assuming the URL is in the format '/storage/uploads/filename.extension'
    $filePath = basename(str_replace(Storage::url(''). '/', '', $request->url));

    if (Storage::disk('public')->exists($filePath)) {
        Storage::disk('public')->delete($filePath);

        return response()->json([
            'status' => true,
            'message' => 'File deleted successfully',
        ]);
    } else {
        return response()->json([
            'status' => false,
            'message' => 'File not found',
        ], 404);
    }
}


public function editForumPost(Request $request, $id)
{
    $user = Auth::user();
    $post = ForumPost::find($id);

    if (!$post) {
        return response()->json([
            'status' => false,
            'message' => 'Forum post not found',
        ], 404);
    }

    if ($user->id != $post->autors && $user->role_id != 1) {
        return response()->json([
            'status' => false,
            'message' => 'Unauthorized to edit this post',
        ], 403);
    }

    $request->validate([
        'nosaukums' => 'required|string|max:50',
        'saturs' => 'required|string|max:5000',
    ]);

    $post->nosaukums = $request->nosaukums;
    $post->saturs = $request->saturs;
    $post->save();

    return response()->json([
        'status' => true,
        'message' => 'Forum post edited successfully',
        'data' => $post,
    ]);
}

public function getAllUsers()
{
    $users = User::all();
    return response()->json([
        'status' => true,
        'users' => $users,
    ]);
}


// Add a new endpoint to delete a user's profile (accessible only to admin)
public function editUserProfile(Request $request, $id)
{
    $user = Auth::user();

    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'Unauthorized',
        ], 401);
    }

    if ($user->id != $id && $user->role_id != 1) {
        return response()->json([
            'status' => false,
            'message' => 'Unauthorized to edit this user profile',
        ], 403);
    }

    $targetUser = User::find($id);

    if (!$targetUser) {
        return response()->json([
            'status' => false,
            'message' => 'User not found',
        ], 404);
    }

    $targetUser->update($request->all());

    return response()->json([
        'status' => true,
        'message' => 'User profile updated successfully',
        'data' => $targetUser,
    ]);
}

public function deleteUserProfile($id)
{
    $user = Auth::user();

    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'Unauthorized',
        ], 401);
    }

    if ($user->role_id != 1) {
        return response()->json([
            'status' => false,
            'message' => 'Unauthorized to delete user profile',
        ], 403);
    }

    $targetUser = User::find($id);

    if (!$targetUser) {
        return response()->json([
            'status' => false,
            'message' => 'User not found',
        ], 404);
    }

    $targetUser->delete();

    return response()->json([
        'status' => true,
        'message' => 'User profile deleted successfully',
    ]);
}
}
