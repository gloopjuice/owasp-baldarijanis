<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\ForumPost; // Assuming you have a ForumPost model
use App\Models\Comment; // Assuming you have a Comment model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    /**
     * Register a new user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        // Validate the request input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|max:255|unique:users,username',
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:7|confirmed', // Minimum 7 characters and password confirmation
        ]);

        // If validation fails, return a 422 response with the errors
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create the user and hash the password
        $user = User::create([
            'email' => $request->email,
            'username' => $request->username,
            'name' => $request->name,
            'password' => Hash::make($request->password),
        ]);

        // Return success response with the created user data
        return response()->json([
            'message' => 'Registration successful!',
            'user' => $user
        ], 201);
    }

    /**
     * Log in a user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // Validate the request input
        $credentials = $request->only('email', 'password');

        if (auth()->attempt($credentials)) {
            $user = auth()->user();
            return response()->json(['message' => 'Login successful!', 'user' => $user], 200);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Get the authenticated user's profile.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile()
    {
        return response()->json(auth()->user(), 200);
    }

    /**
     * Log out a user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out'], 200);
    }

    /**
     * Delete a user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteUser()
    {
        $user = auth()->user();
        $user->delete();
        
        return response()->json(['message' => 'User deleted successfully'], 200);
    }

    /**
     * Get the user's profile.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserProfile()
    {
        return response()->json(auth()->user(), 200);
    }

    /**
     * Update the user's profile.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'username' => 'sometimes|string|max:255|unique:users,username,' . $user->id,
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'bio' => 'sometimes|string|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->update($request->only('name', 'username', 'email', 'bio'));

        return response()->json(['message' => 'Profile updated successfully', 'user' => $user], 200);
    }

    /**
     * Delete user profile.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteProfile()
    {
        $user = auth()->user();
        $user->delete();
        
        return response()->json(['message' => 'Profile deleted successfully'], 200);
    }

    /**
     * Create a forum post.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createForumPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $post = ForumPost::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return response()->json(['message' => 'Post created successfully!', 'post' => $post], 201);
    }

    /**
     * Get a specific forum post.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $post = ForumPost::find($id);
        if (!$post) {
            return response()->json(['error' => 'Post not found'], 404);
        }
        return response()->json($post, 200);
    }

    /**
     * Get all forum posts.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllForumPosts()
    {
        $posts = ForumPost::all();
        return response()->json($posts, 200);
    }

    /**
     * Edit a forum post.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function editForumPost(Request $request, $id)
    {
        $post = ForumPost::find($id);
        if (!$post) {
            return response()->json(['error' => 'Post not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $post->update($request->only('title', 'content'));

        return response()->json(['message' => 'Post updated successfully!', 'post' => $post], 200);
    }

    /**
     * Delete a forum post.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deletePost($id)
    {
        $post = ForumPost::find($id);
        if (!$post) {
            return response()->json(['error' => 'Post not found'], 404);
        }

        $post->delete();
        return response()->json(['message' => 'Post deleted successfully'], 200);
    }

    /**
     * Upload a file.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:jpg,png,pdf|max:2048', // Adjust file types and sizes as needed
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $filePath = $request->file('file')->store('uploads', 'public');

        return response()->json(['message' => 'File uploaded successfully!', 'file_path' => $filePath], 201);
    }

    /**
     * Delete a file.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file_path' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if (\Storage::disk('public')->exists($request->file_path)) {
            \Storage::disk('public')->delete($request->file_path);
            return response()->json(['message' => 'File deleted successfully'], 200);
        }

        return response()->json(['error' => 'File not found'], 404);
    }

    /**
     * Get all users.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllUsers()
    {
        $users = User::all();
        return response()->json($users, 200);
    }

    /**
     * Edit user profile.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function editUserProfile(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'username' => 'sometimes|string|max:255|unique:users,username,' . $user->id,
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'bio' => 'sometimes|string|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->update($request->only('name', 'username', 'email', 'bio'));

        return response()->json(['message' => 'User profile updated successfully', 'user' => $user], 200);
    }

    /**
     * Delete a user profile.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteUserProfile($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->delete();
        return response()->json(['message' => 'User profile deleted successfully'], 200);
    }

    /**
     * Create a comment.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|exists:forum_posts,id', // Assuming comments are linked to forum posts
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $comment = Comment::create([
            'user_id' => auth()->id(),
            'post_id' => $request->post_id,
            'content' => $request->content,
        ]);

        return response()->json(['message' => 'Comment created successfully!', 'comment' => $comment], 201);
    }

    /**
     * Get comments for a specific forum post.
     *
     * @param int $postId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getComments($postId)
    {
        $comments = Comment::where('post_id', $postId)->get();
        return response()->json($comments, 200);
    }

    /**
     * Update a comment.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateComment(Request $request, $id)
    {
        $comment = Comment::find($id);
        if (!$comment) {
            return response()->json(['error' => 'Comment not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'content' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $comment->update($request->only('content'));

        return response()->json(['message' => 'Comment updated successfully!', 'comment' => $comment], 200);
    }

    /**
     * Delete a comment.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteComment($id)
    {
        $comment = Comment::find($id);
        if (!$comment) {
            return response()->json(['error' => 'Comment not found'], 404);
        }

        $comment->delete();
        return response()->json(['message' => 'Comment deleted successfully'], 200);
    }
}
