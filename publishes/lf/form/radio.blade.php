@props(['name','label'=>null,'class'=>'','params'=>[]])
<x-lf.form.field :name="$name" :label="$label" :class="$class">
    <div class="checkboxs">
        @foreach($params as $val => $title)
            <label class="item">
                <input type="radio" class="form-radio" name="{{$name}}[]" wire:model="{{$name}}" value="{{$val}}" />
                <span class="label">{{$title}}</span>
            </label>
        @endforeach
    </div>
</x-lf.form.field>
