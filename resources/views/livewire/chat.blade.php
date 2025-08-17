<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Chat') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Manage your profile and account settings') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div class="flex h-[550px] text-sm border rounded-xl shadow overflow-hidden bg-white dark:bg-gray-800 dark:border-gray-700">

        <!-- Users List -->
        <div class="w-1/4 border-r bg-gray-50 dark:bg-gray-900 dark:border-gray-700">
            <div class="p-4 font-bold text-gray-700 dark:text-gray-200 border-b dark:border-gray-700">Users</div>
            <div class="divide-y dark:divide-gray-700">
                @foreach ($users as $user)
                <div wire:click="selectUser({{$user->id}})" class="p-3 cursor-pointer hover:bg-blue-100 dark:hover:bg-gray-700 transition
                {{$selectedUser && $selectedUser->id === $user->id ? 'bg-blue-100 dark:bg-gray-700' : ''}}">
                    <div class="text-gray-800 dark:text-gray-100">{{$user->name}}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{$user->email}}</div>
                </div>
                @endforeach

            </div>
        </div>

        <!-- Chat Box -->
        <div class="w-3/4 flex flex-col">



            <!-- Header -->
            <div class="p-4 border-b bg-gray-50 dark:bg-gray-900 dark:border-gray-700">
                <div class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{$selectedUser->name}}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">{{$selectedUser->name}}</div>
            </div>


            <!-- Messages -->
            <div class="flex-1 p-4 overflow-y-auto space-y-3 bg-gray-100 dark:bg-gray-800">

                @foreach ($messages as $message)
                <div class="flex {{$message->sender_id===auth()->id() ? 'justify-start' : 'justify-end'}} ">
                    <div class="max-w-xs px-4 py-2 rounded-2xl shadow {{$message->sender_id===auth()->id()?'bg-blue-600 text-white text-sm' : 'bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-100 text-sm'}}">
                        {{$message->message}}
                    </div>
                </div>
                @endforeach
            </div>

            <div id="typing-indicator" class="px-4 pb-1 text-xs text text-gray-400 italic"></div>

            <!-- Input -->
            <form wire:submit='submit' class="p-4 border-t bg-white dark:bg-gray-900 dark:border-gray-700 flex items-center gap-2">
                <input type="text"
                    wire:model.live="newMessage"
                    class="flex-1 border border-gray-300 dark:border-gray-600 rounded-full px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-100"
                    placeholder="Type your message..." />
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-full transition">
                    Send
                </button>
            </form>

        </div>
    </div>
</div>


<script>
    window.userId = {{ auth()->id() }};
    window.selectedUserId = {{ $selectedUser->id ?? 'null' }};
</script>

<script>
    // Listen for typing events
    window.Echo.private(`chat.{{ $LoginID }}`).listenForWhisper('typing', (event) => {
        var t = document.getElementById('typing-indicator');
        if (event.selectedUserID == {{ auth()->id() }}) {
            t.innerText = `${event.userName} is typing...`;
            setTimeout(() => {
                t.innerText = '';
            }, 2000);
        }
    });

    // Listen for Livewire typing events
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('userTyping', (event) => {
            window.Echo.private(`chat.${event.selectedUserID}`).whisper('typing', {
                userID: event.userID,
                userName: event.userName,
                selectedUserID: event.selectedUserID
            });
        });

        // Listen for user selection changes
        Livewire.on('userSelected', (data) => {
            window.selectedUserId = data[0].userId;
        });
    });

    // Listen for new messages - this handles both real-time updates and notifications
    window.Echo.private(`chat.{{ $LoginID }}`).listen('.MessageSent', (e) => {
        console.log('Message received:', e);
        
        // If the message is not from the currently selected user, show notification
        if (e.sender_id != window.selectedUserId && window.showBrowserNotification) {
            window.showBrowserNotification(e);
        }
    });
</script>

<script>
    if ("Notification" in window && Notification.permission === "default") {
        Notification.requestPermission().then(permission => {
            console.log("Notification permission:", permission);
        });
    }
</script>