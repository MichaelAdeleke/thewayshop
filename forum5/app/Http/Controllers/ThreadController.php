<?php

namespace App\Http\Controllers;

use App\Thread;
use App\ReplyController;
use App\channel;
use App\User;
use Illuminate\Http\Request;

class ThreadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param channel $channel
     * @return \Illuminate\Http\Response
     */
    public function index(channel $channel)
    {
        //
        if($channel->exists){
           $threads=$channel->threads()->latest();
        }else{
            $threads=Thread::latest();
        }
        // seeing thread  of a particular person only
        if($username=request('by')){
            $user=\App\User::where('name',$username)->firstOrFail();
            $threads->where('user_id',$user->id);
        }
        $threads=$threads->get();
        return view('index',compact('threads'));
    }

    /**
     * Show the form for creating  new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view ('create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $this->validate($request,[
            'title'=>'required',
            'body'=>'required',
            'channel_id'=>'required|exists:channels,id'
        ]);
        $thread=Thread::create([
            'user_id'=>auth()->id(),
            'channel_id'=>request('channel_id'),
            'title'=>request('title'),
            'body'=>request('body')
        ]);
        return redirect($thread->path())->with('flash',' your thread has been published');
    }

    /**
     * Display the specified resource.
     *
     * @param  $channelId
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function show($channelId,Thread $thread)
    {
        //
        return view('show',compact('thread'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function edit(Thread $thread)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Thread $thread)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function destroy($channelId,Thread $thread)
    {    

        if($thread->user_id !=auth()->id()){
            if(request()->wantsJson()){
                return response(['status'=>'you do not have permission to delete this thread'],403);
            }

            
        return redirect('/login');
        }
        //
        $thread->delete();
        if(request()->wantsJson()){
         return response([],204);
         $thread->replies()->delete();
        }
        
        return redirect('/threads');
        

    }
    public function __construct(){
        $this->middleware('auth')->except(['index','show']);
    }
}
