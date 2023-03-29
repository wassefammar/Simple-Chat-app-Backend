<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ChatMessageController extends Controller
{
    //
    public function index(Request $request){
        $data=$request->validate([
            'chat_id'=>'required|integer',
            'page'=>'required|numeric',
            'page_size'=>'nullable|numeric'
        ]);
        $chatId=$data['chat_id'];
        $currentPage=$data['page'];
        $pageSize=$data['page_size'] ?? 15;
        $chat=Chat::find($chatId);

        $messages= ChatMessage::where('chat_id',$chatId)
                   ->with('user')
                   ->latest('created_at')
                   ->paginate(
                    $pageSize,
                    ['*'],
                    'page',
                    $currentPage
                   );

        return response()->json([
           'messages'=> $messages->getCollection()
         ],200);           


      
    }

    public function store(Request $request){
        $data=$request->validate([
            'chat_id'=>'required|integer',
            'message'=>'required|string'
        ]);
        $data['user_id']=auth('sanctum')->user()->id;

        $chatMessage= ChatMessage::create($data);
        $chatMessage->load('user');


        return response()->json([
            'note'=>'message sent successfully',
            'message'=> $chatMessage
          ],200); 


    }
}
