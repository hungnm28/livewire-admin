@props(['name','label'=>null,'type'=>'text','class'=>'',"model"=>".live.debounce.300ms",'datalist'=>null,'id'])
@php($id = $id ?? md5($name))
<x-lf.form.field :$name :$label :$class :$id>
    <input name="{{$name}}" type="{{$type}}" wire:model{{$model}}="{{$name}}" id="{{$id}}" @if($datalist) list="lff-list-{{$name}}" @endif class="form-input" {{$attributes}} />
    @if($datalist)
        <datalist id="lff-list-{{$name}}">
            @foreach($datalist as $dt)
                <option  value="{{$dt}}">
            @endforeach
        </datalist>
    @endif
</x-lf.form.field>
