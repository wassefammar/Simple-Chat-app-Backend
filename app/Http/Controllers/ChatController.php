<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\GetChatRequest;
use App\Http\Requests\StoreChatRequest;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $data=$request->validate([
        'is_private'=>'nullable|boolean'
      ]);
      $isPrivate=1;
     /*  if($request->has('is_private')) {
        $isPrivate=(int)$data['is_private'];
      } */  

      $chats=Chat::where('is_private',$isPrivate)
                  ->HasParticipants(auth('sanctum')->user()->id)
                  ->whereHas('messages')
                  ->with('lastmessages.user','participants.user')
                  ->latest('updated_at')
                  ->get();

       if($chats){
        return response()->json([
            'chats'=> $chats
        ],200);
       }
       else{
        return response()->json([
            'message'=> 'no chat found'
        ],200);
       }         
    }


    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


    public function prepareStoreData(Request $request){
      $data=$request->validate([
        'user_id'=>'required|integer',
        'name'=>'nullable|string',
        'is_private'=>'required|boolean'
    ]);
      $otherUserId=$data['user_id'];
      unset($data['user_id']);
      $data['created_by']=auth('sanctum')->user()->id;

      return[
        'otherUserId'=>$otherUserId,
        'userId'=>auth('sanctum')->user()->id,
        'data'=>$data
      ];

    }

    //has previous chat

    public function getPreviousChat(int $otherUserId):mixed{
        $userId=auth('sanctum')->user()->id;
        return Chat::where('is_private',1)
                ->whereHas('participants',function($query) use ($userId){
                    $query->where('user_id',$userId);
                })
                ->whereHas('participants',function($query) use ($otherUserId){
                    $query->where('user_id',$otherUserId);
                })
                ->first();
    }


    public function store(Request $request)
    {
        //
        $data=$this->prepareStoreData($request);
        $contact=User::find($data['otherUserId']);
        if(!$contact){
            return response()->json([
                'message'=> "you cannot create a chat with non existant user."
            ],200);
        }elseif($data['userId']==$data['otherUserId']){
            return response()->json([
                'message'=> "you cannot create a chat with yourself"
            ],200);        
        }

        $previousChat= $this->getPreviousChat($data['otherUserId']);
        if($previousChat==null){
            $chat= Chat::create($data['data']);
            $chat->participants()->createMany([
                ['user_id'=>$data['userId']],
                ['user_id'=>$data['otherUserId']]
            ]);
            $chat->refresh()->load('lastmessages.user','participants.user');
        }
        else{
            return response()->json([
                'chat'=> $previousChat->load('lastmessages.user','participants.user')
            ],200);
        }
       

    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Chat $chat)
    {
        //
        $chat->load('lastmessages.user','participants.user');
        return response()->json([
            'chat'=> $chat
        ],200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
