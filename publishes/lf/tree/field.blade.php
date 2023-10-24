@props(['label'=>''])
<div class="field">
    @if($label)
        <span class="label">{{$label}}</span>
    @endif
    <div class="value">{{$slot}}</div>
</div>
