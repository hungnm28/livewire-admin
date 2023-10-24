@props(['name','label'=>null,'class'=>'class',"model"=>"",'rows'=>5])
<x-lf.form.field :name="$name" :label="$label" :class="$class">
    <textarea id="lf-form-control-{{$name}}" name="{{$name}}" wire:model{{$model}}="{{$name}}"  class="form-textarea" {{$attributes}} rows="{{$rows}}">{{$slot}}</textarea>
</x-lf.form.field>
