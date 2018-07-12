<?php

namespace App\Http\Controllers;
use Validator;
Use App\PostCategory;
Use App\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function store(Request $request)
    {
      if($request->id == null)
        $post = new Post();
      else
        $post = Post::find($request->id);
      $validatedData = Validator::make($request->all(),[
          'title' => 'required|max:191',
          'categories' => 'array',
          'categories.*.id' => 'exists:post_categories,id'
      ]);
      if ($validatedData->fails()) {
          return response()->json($validatedData->errors(),400);
      }
      if(strtolower($post->title) != strtolower($request->get('title'))){
          $validatedData = Validator::make($request->all(),[
              'title' => 'required|unique:posts',
          ]);
          if ($validatedData->fails()) {
              return response()->json($validatedData->errors(),400);
          }
      }

      $post->title=$request->title;
      $post->description = $request->description;
      
      if($request->file('image') != null):
          $imageName=null;
          $image = $request->file('image');
          $imageName = uniqid().'post.'.$image->getClientOriginalExtension();
          $destinationPath = public_path('/uploads/post');
          $image->move($destinationPath, $imageName);
          $imageName = "/uploads/post/".$imageName;
          $post->image= $imageName;
      endif;
      
      try{
        $post->save();
        $categoryArray=[];
        foreach($request->categories as $category){
            if(array_key_exists('selected',$category)){
                if($category['selected'] == true){
                    if(array_key_exists('id',$category)){
                        $categoryArray[]=$category['id'];
                    }
                }
            }
        }
        $post->post_categories()->sync($categoryArray);
        return response()->json($post, 200);  
      }catch(\Exception $e){
          return response("Post update failed. Please try again",500);
      }
    }

    public function show($id){
      return Post::with('post_categories')->where('id','=',$id)->get()[0];
    }

    public function index()
    {
      $post= Post::with('post_categories')->get()->toArray(); 
     return response()->json($post, 200);
    } 

    public function destroy($id)
    {
        $post=Post::find($id);    
        $post->delete();
        return response()->json("Successfully deleted", 200);
    }
}