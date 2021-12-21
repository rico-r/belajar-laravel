<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $request=Request::capture();
        if($request->expectsJson()){
            return Post::all(["id", "tittle", "body"]);
        }
        return view("layouts.index")->with("posts", Post::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("layouts.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            "tittle" => "required|unique:posts|max:150",
            "body" => "required"
        ]);

        $post=Post::create($request->all());
        if($request->expectsJson()){
            return ["status" => "success", "id" => $post->id];
        }
        return redirect()->route("posts.index")->with("message", "Post berhasil dibuat");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view("layouts.edit", [
            "post" => Post::findOrfail($id)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $post=Post::findOrFail($id);
        $request->validate([
            "tittle" => [
                "required",
                "max:150",
                Rule::unique("posts")->ignore($post)
            ],
            "body" => "required"
        ]);
        
        $post->update($request->all());
        if($request->expectsJson()){
            return ["status" => "success"];
        }
        return redirect()->route("posts.index")->with("message", "Data berhasil diperbaharui");        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        Post::destroy($id);
        if($request->expectsJson()){
            return ["status" => "success"];
        }
        return back()->with("message", "post berhasil dihapus");
    }

}
