 @foreach ($data as $key=>$val)
 <div class="direct-chat-msg {{ $val['position']=="R"?'right':'' }}">
     <div class="direct-chat-infos clearfix">
         <span
             class="direct-chat-name
                                    {{ $val['position']=="R"?'float-right':'float-left' }}">{{ $val['position']=="R" ? get_user($val['sender']) : get_user($val['sender']) }}</span>
         &nbsp;
         <span class="direct-chat-timestamp created_at_{{ $key  }}
                                    {{ $val['position']=="R"?'float-left':'float-right' }}">
             {{ $val['created_at'] }}

         </span>
     </div>

     <img class="direct-chat-img" src="{{ $val['profile_pic'] }}" alt="message user image">

     <div class="direct-chat-text">
         {{ $val['messages'] }}
     </div>

 </div>
 @endforeach
 {{-- button load more --}}
 @if(isset($data))
 <div>
     @if($data->currentPage() > 1)
     {{-- <a href="{{ $posts->previousPageUrl() }}">Previous</a> --}}
     <button id="load_prev" class="btn btn-sm btn-warning">Load Prev</button>
     @endif

     @if($data->hasMorePages())
     {{-- <a href="{{ $posts->nextPageUrl() }}">Next</a> --}}

     <button id="load_more" class="btn btn-sm btn-success">Load More</button>
     @endif



 </div>
 @endif
