@props(['label'=>null,'class'=>''])
<div class=" {{$class}} form-field">
    @if($label)
        <label class="form-label" for="lf-form-control-{{$name}}">{{$label}}</label>
    @endif
    <div class="form-group">
        {{$slot}}
    </div>
</div>
