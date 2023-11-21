@props(['name','label'=>null,'class'=>'',"model"=>"",'rows'=>5,'id'])
@php($id = $id ?? md5($name))
<x-lf.form.field :$name :$label :$class :$id>
    <textarea id="{{$id}}" name="{{$name}}" wire:model{{$model}}="{{$name}}"  class="form-textarea" {{$attributes}} rows="{{$rows}}">{{$slot}}</textarea>
</x-lf.form.field>
