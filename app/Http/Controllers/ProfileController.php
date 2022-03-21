<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Traits\ImageUpload;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    use ImageUpload;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function updateProfile(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                'avatar'     =>  'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            /* Check the validator status */
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors(), 'message'=>'Validation Failed!'],403);
            }

            /* Confirm its the Auth user */
            $user = User::find(Auth::id());

            /* Avatar Temp storage */
            $avatar = ImageUpload::uploadSingleImage($request->avatar, 'images/avatars');

             /* Check If The Avatar Was Uploaded */
             if (is_null($avatar) || $avatar == '') {
                $errors = new \stdClass();
                $errors->avatar = ['Sorry, There was a problem uploading your avatar!'];
                return response()->json(['resp'=>$errors, 'error'=>'Failed to upload the selected avatar!'], 204);
            }

            /* Check Existing Avatar Path */
            $old_avatar_path = $user->avatar;

            /* Replace Existing Auth User Avatar */
            if (!is_null($old_avatar_path) || !empty($old_avatar_path)) {

                ImageUpload::deleteFile($old_avatar_path);
                $user->update([
                    'avatar'=>$avatar
                ]);

                 /* Prepare the success response */
                 $data = new \stdClass();
                 $data->user = new UserResource($user);

                 return response()->json(['data'=>$data, 'success'=>'Avatar updated successfully!'], 200);
            }


            $user->update([
                'avatar'=>$avatar
            ]);

             /* Prepare the success response */
             $data = new \stdClass();
             $data->user = new UserResource($user);

             return response()->json(['data'=>$data, 'success'=>'Avatar Uploaded successfully!'], 200);

        }catch(Exception $e){
            Log::error($e->getMessage(),[$e->getTrace()]);
            return response()->json(['error'=>'Sorry, Something went wrong. Please, try again'],500);
        }
    }
}
