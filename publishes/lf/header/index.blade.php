@props(["tools"=>null,'class'=>'','title'=>''])
<header class="page-header {{$class}}">
    <label for="la-page-control" class="h-menu">{!! mIcon("menu") !!}</label>
    <div class="w-full flex-auto">
        <span class="title">{{$title}}</span>
        {{$slot}}
    </div>
    @if($tools)
        <div class="tools">
            {{$tools}}
        </div>
    @endif
    <span class="h-btn" onclick="toggleFullScreen(this)">{!! mIcon("fullscreen") !!}</span>
    <x-lf.header.user />
</header>
