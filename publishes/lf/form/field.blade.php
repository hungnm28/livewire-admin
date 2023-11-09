@props(['name','label'=>null,'class'=>'','id'])
@php($id = $id?? md5($name))
<div id="lf-field-{{$name}}" class=" {{$class}} form-field  @error($name) error @enderror">
    @if($label)
        <label class="form-label" for="{{$id}}">{{$label}}</label>
    @endif
    <div class="w-full block">
        {{$slot}}
    </div>
    @error($name)
        <label class="message" for="{{$id}}">{{$message}}</label>
    @enderror
</div>
