@props(['class'=>'','header'=>null,'footer'=>null])
<div class="card {{$class}}">
    @if($header)
        <div class="card-header">{{$header}}</div>
    @endif
    <div class="content">{{$slot}}</div>
    @if($footer)
        <div class="card-footer">
            {{$footer}}
        </div>
    @endif
</div>
