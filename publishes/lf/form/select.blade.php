@props(['name','label'=>null,'class'=>'class',"model"=>".live",'params'=>[],'id'])
@php($id = $id ?? md5($name))
<x-lf.form.field :$name :$label :$class :$id>
    <select name="{{$name}}" wire:model{{$model}}="{{$name}}" id="{{$id}}" class="form-input" {{$attributes}}>
        @foreach($params as $value=> $title)
            <option value="{{$value}}">{{$title}}</option>
        @endforeach
    </select>
</x-lf.form.field>
