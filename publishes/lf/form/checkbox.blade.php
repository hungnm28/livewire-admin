@props(['name','label'=>null,'class'=>'','iclass'=>'','params'=>[]])
<x-lf.form.field :$name :$label :$class >
    <div class="checkboxs">
        @foreach($params as $val => $title)
            <label class="item">
                <input type="checkbox" class="form-checkbox {{$iclass}}" wire:model.live="{{$name}}" value="{{$val}}"/>
                <span class="label">{{$title}}</span>
            </label>
        @endforeach
    </div>
</x-lf.form.field>
