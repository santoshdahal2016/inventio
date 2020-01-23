<?php

namespace App\Http\Controllers\Api;

use App\Category;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;

class CategoryController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        /*this will be always on the latest : ASC ,desc*/
        $result = Category::orderBy('position', 'ASC')->get();

        if (isset($request->search)) {
            $result = full_text_search($result, $request->search, ["id", "created_at", "updated_at", "position"]);
        }

        return CategoryResource::collection(paginate($result, $request->itemsPerPage, $request->page));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return CategoryResource
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'title' => 'required|min:3',
        ]);


        if ($validation->fails()) {
            return response()->json($validation->errors(), 422);

        }
        $category = new Category();
        $category->title = $request->title;
        $category->save();

        return new CategoryResource($category);
    }

    /**
     * Display the specified resource.
     *
     * @param Category $category
     * @return Response
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Category $category
     * @return Response
     */
    public function update(Request $request, Category $category)
    {
        $validation = Validator::make($request->all(), [
            'title' => 'required|min:3',
            'position' => 'required|numeric'
        ]);


        if ($validation->fails()) {
            return response()->json($validation->errors(), 422);

        }

        $category->fill($request->all())->save();

        $return = ["status" => "Success"];
        return response()->json($return, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Category $category
     * @return Response
     * @throws Exception
     */
    public function destroy(Category $category)
    {

        if ($category->delete()) {
            $category->next()->decrement('position');

            $return = [
                "status" => "Success",
                "message" => "Category deleted successfully"
            ];
            return response()->json($return, 200);
        } else {
            $return = ["status" => "error",
                "error" => [
                    "code" => 500,
                    "errors" => 'Not Deleted'
                ]];
            return response()->json($return, 500);
        }

    }


    /**
     * Update the specified resource in storage.
     *
     * @param Category $category1
     * @param Category $category2
     * @return Response
     * @throws Exception
     */
    public function sort(Category $category1, Category $category2)
    {
        try {
            $category1->moveAfter($category2);
            $return = ["status" => "Success"];
            return response()->json($return, 200);


        } catch (Exception $e) {
            $return = ["status" => "error"];
            return response()->json($return, 500);

        }

    }
}
