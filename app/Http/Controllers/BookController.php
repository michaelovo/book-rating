<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\BookResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    public function index()
    {
      return BookResource::collection(Book::with('ratings')->paginate(25));
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
              'description' => 'string|required',
              'title' => 'string|required|unique:books,title',
            ]);

            /* Check The Request For Errors */
            if ($validator->fails()) {
              return response()->json(['error'=>$validator->errors(), 'message'=>'Validation Failed!'],403);
            }

            /* Create The Book */
            $book = Book::create([
                'user_id' => $request->user()->id,
                'title' => $request->title,
                'description' => $request->description,
            ]);
            return response()->json(['book'=> new BookResource($book),'message'=>'New Book Added successfully'],201);

          } catch (Exception $e) {
            Log::error($e->getMessage(), [$e->getTrace()]);
            return response()->json(['error' => 'Sorry, Something went wrong. Please, try again'], 500);
          }
    }

    public function fetchBookId(int $book)
    {
        try {

            $book = Book::find($book);
            if (is_null($book)) {
                $errors = new \stdClass();
                $errors->book = ['Sorry, A Book with this ID could not be retrieved!'];
                return response()->json(['resp'=>$errors,'error'=>'Invalid book option selected'], 201);
            }

              /* Prepare the success response */
            $data = new \stdClass();
            $data->book = new BookResource($book);
            return response()->json(['data'=>$data,'Success'=>'Book retrieved successfully'],200);
        }
        catch (Exception $e) {
            Log::error($e->getMessage(), [$e->getTrace()]);
            return response()->json(['error' => 'Sorry, Something went wrong. Please, try again'], 500);

        }
    }

    public function updateBook(Request $request, int $bookId)
    {
        try{

            $validator = Validator::make($request->all(), [
                'title' => 'string|required|unique:books,title,' . $bookId,
                'description' => 'string|required'
              ]);

              /* Check the validator status */
                if ($validator->fails()) {
                    return response()->json(['error'=>$validator->errors(), 'message'=>'Validation Failed!'],403);
                  }

            /* Check if book is valid */
            $book = Book::find($bookId);
            if (is_null($book)) {
                $errors = new \stdClass();
                $errors->book = ['Sorry, A Book with this ID could not be retrieved!'];
                return response()->json([
                    'resp' => $errors,
                    'book' => $book
                ], 201);
            }

            // check if currently authenticated user is the owner of the book
            if ($request->user()->id !== $book->user_id) {
                $errors = new \stdClass();
                $errors->book = ['You can only edit your own book(s).'];
                return response()->json([
                    'resp' => $errors,
                    'error' => 'Permission denied!'
                ], 403);
            }

            /*Update book */
             $book->update($request->all());

             /* Prepare the success response */
                $data = new \stdClass();
                $data->book = new BookResource($book);

                return response()->json(['data'=>$data, 'success'=>'Book updated successfully!'], 200);

        }catch(Exception $e){
            Log::error($e->getMessage(),[$e->getTrace()]);
            return response()->json(['error'=>'Sorry, Something went wrong. Please, try again'],500);
        }

    }

    public function deleteBook(int $bookId)
    {
        try {
            $book = Book::find($bookId);
            if (is_null($book)) {
                $errors = new \stdClass();
                $errors->book = ['Sorry, A Book with this ID could not be retrieved!'];
                return response()->json(['resp'=>$errors,'error'=>'Invalid book option selected'], 201);
            }
            $book->delete();

             /* Prepare A Success Response */
                $data = new \stdClass();
                $data->book = [];

                return response()->json(['data'=>$data, 'success'=>'Book Deleted successfully!'], 200);

        } catch(Exception $e){
            Log::error($e->getMessage(),[$e->getTrace()]);
            return response()->json(['error'=>'Sorry, Something went wrong. Please, try again'],500);
        }
    }
}
