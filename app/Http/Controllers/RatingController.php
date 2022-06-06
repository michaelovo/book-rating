<?php

namespace App\Http\Controllers;
use Exception;
use App\Models\Book;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\RatingResource;
use Illuminate\Support\Facades\Validator;

class RatingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function store(Request $request, int $bookId)
    {
        try {
            $validator = Validator::make($request->all(), [
              'user_id' => 'numeric|required|exists:users,id',
              'rating' => 'numeric|required',
            ]);

            /* Check The Request For Errors */
            if ($validator->fails()) {
              return response()->json(['error'=>$validator->errors(), 'message'=>'Validation Failed!'],403);
            }

            /* Check if selected book exists */
            $book = Book::find($bookId);

            /* send back response when selected book not found */
           if (is_null($book)) {
                $errors = new \stdClass();
                $errors->book = ['Sorry, A Book with this ID does not exist in our record!'];
                return response()->json(['resp'=>$errors,'error'=>'Invalid book Id selected'], 404);
            }

            /* store book rating to Rating model */
            $rating = Rating::firstOrCreate([
                    'user_id' => $request->user()->id,
                    'book_id' => $book->id,
                    'rating' => $request->rating
                ]);

                /* send http response to browser */
            return response()->json(['data'=> new RatingResource($rating),'message'=>'Book Rated successfully'],200);

        } catch (Exception $e) {
            Log::error($e->getMessage(), [$e->getTrace()]);
            return response()->json(['error' => 'Sorry, Something went wrong. Please, try again'], 500);
          }
    }
}
