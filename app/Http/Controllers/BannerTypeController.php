<?php

namespace App\Http\Controllers;

use Validator;
Use App\BannerType;
use Illuminate\Http\Request; 

class BannerTypeController extends Controller
{
    

    /**
     * @SWG\Post(
     *      path="/banner-type",
     *      tags={"Banner"},
     *      operationId="banner-type-create",
     *      summary="Create banner type",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="typeName",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=400, description="Bad request")
     *     
     * )
     */
    public function store(Request $request)
    {
        $validatedData = Validator::make($request->all(),[
            'typeName' => 'required|unique:banner_types|max:191',
        ]);
        if ($validatedData->fails()) {
            return response()->json($validatedData->errors(),400);
        }
        $bannerTypes = BannerType::create([
            'typeName'=>$bannerType['typeName']= $request->typeName,
        ]);

        $status= response()->json($bannerTypes, 200);
        if($status)							
        {
            $type= BannerType::where('id',$bannerTypes->id)->get()->toArray();
            $data = array('success' =>true, 'message' => 'Success! Banner Type created successfully.','data'=>$type[0]);
            echo json_encode($data);
        }
        else
        {
            return response("Category update failed. Please try again",500);
        }
    }

    

    /**
     * @SWG\Put(
     *      path="/banner-type/{id}",
     *      tags={"Banner"},
     *      operationId="banner-type-update",
     *      summary="Update banner type",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          in="path",
     *          required=true, 
     *          type="integer" 
     *      ),
     *      @SWG\Parameter(
     *          name="typeName",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=400, description="Bad request")
     *     
     * )
     */
    public function update(Request $request, $id)
    {
    
        $bannerType = BannerType::find($id);
        $validatedData = Validator::make($request->all(),[
            'typeName' => 'required|max:191',
        ]);
        if ($validatedData->fails()) {
            return response()->json($validatedData->errors(),400);
        }
        if($bannerType->typeName != $request->get('typeName')){
            $validatedData = Validator::make($request->all(),[
                'typeName' => 'required|unique:banner_types|max:191',
            ]);
            if ($validatedData->fails()) {
                return response()->json($validatedData->errors(),400);
            }
        }

        $bannerType->typeName = $request->get('typeName');
        $bannerTypeResponse= $bannerType->save();
        $status= response()->json($bannerTypeResponse, 200);
        if($status)							
        {
            $type= BannerType::where('id',$bannerType->id)->get()->toArray();
            $data = array('success' =>true, 'message' => 'Success! Banner Type updated successfully.','data'=>$type[0]);
            echo json_encode($data);
        }
        else
        {
            return response("Category update failed. Please try again",500);
        }
    } 

    
    /**
     * @SWG\Get(
     *      path="/banner-type",
     *      operationId="banner-type-index",
     *      tags={"Banner"},
     *      summary="Get banner type(category) lists",
     *      description="Returns banner type(category) lists",
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=400, description="Bad request")
     *     )
     *
     * Returns banner type(category) lists
     */
    public function index()
    {
      $bannerTypes= BannerType::all()->toArray(); 
     return response()->json($bannerTypes, 200);
    } 

    /**
     * @SWG\Delete(
     *      path="/banner-type/{id}",
     *      tags={"Banner"},
     *      operationId="banner-type-destroy",
     *      summary="Delete banner type child elements also deleted",
     *      @SWG\Parameter(
     *          name="id",
     *          in="path",
     *          required=true, 
     *          type="integer" 
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Success"
     *      ),
     * )
     */
    public function destroy($id)
    {
      $bannerType= BannerType::find($id);
      $bannerType->delete();
      return response()->json($bannerType->toArray(), 200);
    }
}
