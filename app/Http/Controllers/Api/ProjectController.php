<?php

namespace App\Http\Controllers\Api;

use AnonymousResourceCollection;
use App\Http\Resources\ProjectResource;
use App\Project;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;

class ProjectController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        /*this will be always on the latest*/
        $result = Project::orderBy('created_at', 'desc')->get();

        if (isset($request->search)) {
            $result = full_text_search($result, $request->search, ["id", "created_at", "slug","updated_at","user_id","category_id"]);
        }

        return ProjectResource::collection(paginate($result, $request->itemsPerPage, $request->page));

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return ProjectResource
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'title' => 'required|min:10',
            'abstract' => 'required',
            'category_id' => 'required',

        ]);


        if ($validation->fails()) {
            return response()->json($validation->errors(), 422);

        }

            $request['user_id'] = Auth::user()->id;



//        return $request->all();
        $project = Project::create($request->all());

        if ($request['featured'] != null) {
            $project->addMediaFromRequest('featured')->toMediaCollection('featured');
        }
        return new  ProjectResource($project);


    }

    /**
     * Display the specified resource.
     *
     * @param Project $project
     * @return ProjectResource
     */
    public function show(Project $project)
    {
        return new ProjectResource($project);

    }

    public function update(Request $request, Project $project)
    {


        $validation = Validator::make($request->all(), [
            'title' => 'required|min:10',
            'abstract' => 'required',
            'category_id' => 'required'
        ]);


        if ($validation->fails()) {
            return response()->json($validation->errors(), 422);

        }

            $request['user_id'] = Auth::user()->id;


        $project->fill($request->all())->save();

        if ($request['featured'] != null) {
            $project->clearMediaCollection('featured');

            $project->addMediaFromRequest('featured')->toMediaCollection('featured');
        }
        $return = ["status" => "Success"];
        return response()->json($return, 201);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param Project $project
     * @return Response
     * @throws \Exception
     */
    public function destroy(Project $project)
    {
        if ($project->delete()) {
            $return = ["status" => "Success",
            "message"=>"Delete Successful"];
            return response()->json($return, 200);
        }else{
            $return = ["status" => "error",
                "error" => [
                    "code" => 500,
                    "errors" => 'Not Deleted'
                ]];
            return response()->json($return, 500);
        }

    }
}
