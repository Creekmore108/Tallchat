<div
x-data="{height:0,conversationElement:document.getElementById('conversation')}"
x-init="
    height= conversationElement.scrollHeight;
    $nextTick(()=>conversationElement.scrollTop= height);
"
@scroll-bottom.window="
$nextTick(()=>conversationElement.scrollTop= height);
"

class="w-full overflow-hidden">
    <div class="border-b flex flex-col overflow-y-scroll grow h-full">
        {{-- header --}}
        <header class="w-full sticky inset-x-0 flex pb-[5px] pt-[5px] top-0 z-10 bg-white border-b">

            <div class="flex w-full items-center px-2 lg:px-4 gap-2 md:gap-5">
                {{-- Left arrow --}}
                <a class="shrink-0 lg:hidden" href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-6 w-6">
                        <path fill-rule="evenodd"
                            d="M7.28 7.72a.75.75 0 0 1 0 1.06l-2.47 2.47H21a.75.75 0 0 1 0 1.5H4.81l2.47 2.47a.75.75 0 1 1-1.06 1.06l-3.75-3.75a.75.75 0 0 1 0-1.06l3.75-3.75a.75.75 0 0 1 1.06 0Z"
                            clip-rule="evenodd" />
                    </svg>
                </a>

                {{-- avatar --}}
                <div class="shrink-0">
                    <x-avatar class="h-9 w-9 lg:w-11 lg:h-11" />
                </div>

                <h6 class="font-bold truncate">{{ $selectedConversation->getReceiver()->email }}</h6>

            </div>

        </header>


        {{-- body --}}
        <main
            @scroll="
                scropTop = $el.scrollTop;

                if(scropTop <= 0){
                    {{-- window.livewire.dispatchTo('loadMore'); --}}
                    $wire.dispatch('loadMore');
                }
            "
            @update-chat-height.window="
                newHeight = $el.scrollHeight;

                oldHeight = height;
                $el.scrollTop = newHeight - oldHeight

                height=newHeight;
            "
            id="conversation"
            class="flex flex-col gap-3 p-2.5 overflow-y-auto flex-grow overscroll-contain overflow-x-hidden w-full my-auto ">

            @if ($loadedMessages)

            @php
                $previousMessage = null;
            @endphp

                @foreach ($loadedMessages as $key => $message)

                @if($key > 0)
                @php
                    $previousMessage = $loadedMessages->get($key-1)
                @endphp
                @endif
                    <div @class([
                        'max-w-[85%] md:max-w-[78%] flex w-auto gap-2 relative mt-2',
                        'ml-auto' => $message->sender_id === auth()->id(),
                    ])>

                        {{-- avatar --}}

                        <div @class([
                            'shrink-0',
                            'invisible' =>$previousMessage?->sender_id == $message->sender_id,
                            'hidden' =>$message->sender_id == auth()->id(),
                            ])>

                            <x-avatar />
                        </div>
                        {{-- Message Body --}}

                        <div @class([
                            'flex flex-wrap text-[15px] rounded-xl p-2.5 flex-col text-black bg-[#f6f6f8fb]',
                            'rounded-bl-none border border-gray-200/40 ' => !(
                                $message->sender_id === auth()->id()
                            ),
                            'rounded-br-none bg-blue-500/80 text-white' =>
                                $message->sender_id === auth()->id(),
                        ])>

                            <p class="whitespace-normal truncate text-sm md:text-base tracking-wide lg:tracking-normal">
                                {{ $message->body }}
                            </p>

                            <div class="ml-auto flex gap-2">
                                <p @class([
                                    'text-xs',
                                    'text-gray-500' => !($message->sender_id === auth()->id()),
                                    'text-white' => $message->sender_id === auth()->id(),
                                ])>
                                    {{ $message->created_at->inUserTimezone()->format('g:i a')}}
                                </p>

                                {{-- Message status, only show if messge belongs to auth --}}
                                @if ($message->sender_id === auth()->id())
                                    <div>
                                        {{-- Double ticks --}}
                                        @if ($message->isRead())
                                            <span @class(['text-gray-200'])>

                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-check2-all" viewBox="0 0 16 16">
                                                    <path
                                                        d="M12.354 4.354a.5.5 0 0 0-.708-.708L5 10.293 1.854 7.146a.5.5 0 1 0-.708.708l3.5 3.5a.5.5 0 0 0 .708 0zm-4.208 7-.896-.897.707-.707.543.543 6.646-6.647a.5.5 0 0 1 .708.708l-7 7a.5.5 0 0 1-.708 0" />
                                                    <path
                                                        d="m5.354 7.146.896.897-.707.707-.897-.896a.5.5 0 1 1 .708-.708" />
                                                </svg>

                                            </span>
                                        @else
                                            {{-- Single ticks --}}
                                            <span @class(['text-gray-200'])>

                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-check2" viewBox="0 0 16 16">
                                                    <path
                                                        d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0" />
                                                </svg>

                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>

                        </div>


                    </div>
                @endforeach
            @endif
        </main>

        {{-- footer send message --}}
        <footer class="shrink-0 z-10 bg-white inset-x-0">
            <div class="p-2 border-t">
                <form
                    x-data="{ body: @entangle('body') }"
                    {{-- x-on:keydown.shift="shift = true"
                    x-on:keyup.shift="shift = false" --}}
                    @submit.prevent="$wire.sendMessage"
                    {{-- x-on:keyup.enter.prevent="if (!shift) { $wire.sendMessage}" --}}
                    method="POST"
                    autocapitalize="off">
                    @csrf

                    <input type="hidden" autocomplete="false">

                    <div class="grid grid-cols-12">
                        <input x-model="body" type="text" autocomplete="off" autofocus
                            placeholder="Write your message here" maxlength="1700"
                            class="col-span-10 bg-gray-100 border-0 outline-0 focus:border-0 focus:ring-0 hover:ring-0 rounded-lg focus:outline-none">
                        <button class="col-span-2" type="submit">Send</button>
                    </div>

                </form>

            </div>
        </footer>
    </div>

</div>
