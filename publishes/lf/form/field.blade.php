@props(['name','label'=>null,'class'=>''])
<div id="lf-field-{{$name}}" class=" {{$class}} form-field  @error($name) error @enderror">
    @if($label)
        <label class="form-label" for="lf-form-control-{{$name}}">{{$label}}</label>
    @endif
    <div class="w-full block">
        {{$slot}}
    </div>
        @error($name)
        <label class="message" for="lf-form-control-{{$name}}">{{$message}}</label>
        @enderror
</div>
