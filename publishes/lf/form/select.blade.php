@props(['name','label'=>null,'class'=>'class',"model"=>".live",'params'=>[]])
<x-lf.form.field :name="$name" :label="$label" :class="$class">
    <select name="{{$name}}" wire:model{{$model}}="{{$name}}" id="lf-form-control-{{$name}}" class="form-input" {{$attributes}}>
        @foreach($params as $value=> $title)
            <option value="{{$value}}">{{$title}}</option>
        @endforeach
    </select>
</x-lf.form.field>
