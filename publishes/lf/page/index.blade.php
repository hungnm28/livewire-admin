@props(['title'=>'','tools'=>'','class'=>'','footer'=>null])
<div class="page {{$class}}">
    <x-lf.header :title="$title" >
        <x-slot:tools>
            {{$tools}}
        </x-slot:tools>
    </x-lf.header>
   <div class="page-content">{{$slot}}</div>
    @if($footer)
        <div class="page-footer">{{$footer}}</div>
    @endif
    <x-lf.toast />
</div>
