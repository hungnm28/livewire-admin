@props(['name','label'=>null,'class'=>'class','params'=>[]])
<x-lf.form.field :name="$name" :label="$label" :class="$class">
    <div class="checkboxs">
        @foreach($params as $val => $title)
            <label class="item">
                <input type="checkbox" class="form-checkbox" wire:model.live="{{$name}}" value="{{$val}}" />
                <span class="label">{{$title}}</span>
            </label>
        @endforeach
    </div>
</x-lf.form.field>
