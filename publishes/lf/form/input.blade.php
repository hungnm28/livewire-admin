@props(['name','label'=>null,'type'=>'text','class'=>'class',"model"=>".live.debounce.300ms",'datalist'=>null])
<x-lf.form.field :name="$name" :label="$label" :class="$class">
    <input name="{{$name}}" type="{{$type}}" wire:model{{$model}}="{{$name}}" id="lf-form-control-{{$name}}" @if($datalist) list="lff-list-{{$name}}" @endif class="form-input" {{$attributes}} />
    @if($datalist)
        <datalist id="lff-list-{{$name}}">
            @foreach($datalist as $dt)
                <option  value="{{$dt}}">
            @endforeach
        </datalist>
    @endif
</x-lf.form.field>
