<?php

namespace App\Livewire;

use App\Events\MessageSent;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Chat extends Component
{

    public $users ;
    public $selectedUser;
    public $newMessage;
    public $messages;
    public $LoginID;

    public function mount()
    {
        $this->users = User::whereNot('id', Auth::id())->latest()->get();
        $this->selectedUser = $this->users->first() ?? null;
        $this->loadMessages();
        $this->LoginID = Auth::id();


    }

    public function selectUser($id)
    {
        $this->selectedUser= User::find($id);
        $this->loadMessages();
        
        // Update the selected user ID in JavaScript
        $this->dispatch('userSelected', ['userId' => $id]);
    }

    public function submit()
    {
        if (empty($this->newMessage)) {
            return;
        }

        $message = ChatMessage::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $this->selectedUser->id,
            'message' => $this->newMessage,
        ]);

        $this->messages->push($message);

        $this->newMessage = '';
        broadcast(new MessageSent($message));
    }

    public function updatedNewMessage($value)
    {
        $this->dispatch('userTyping',userID: $this->LoginID, userName: Auth::user()->name, selectedUserID: $this->selectedUser->id);
    }


    public function getListeners()
    {
        return [
            "echo-private:chat.{$this->LoginID},MessageSent" => 'newChatMessageNotification',
        ];
    }

    public function newChatMessageNotification($message)
    {
        if($message['sender_id'] == $this->selectedUser->id){
            $messageObj = ChatMessage::find($message['id']);
            $this->messages->push($messageObj);
        }
    }

    public function loadMessages()
    {
        $this->messages = ChatMessage::query()
            ->where(function ($q) {
                $q->where('sender_id', Auth::id())
                    ->where('receiver_id', $this->selectedUser->id);
            })
            ->orWhere(function ($q) {
                $q->where('sender_id', $this->selectedUser->id)
                    ->where('receiver_id', Auth::id());
            })
            ->orderBy('created_at', 'asc')->get();
    }

    public function render()
    {
        return view('livewire.chat');
    }
}
