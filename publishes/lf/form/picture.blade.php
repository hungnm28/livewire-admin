@props(["name","label"=>null,"class"=>null,"src"=>null])

<x-lf.form.field :name="$name" :label="$label" :class="$class">
    <label class="form-picture">
        <span class="picture">
            <img src="{{$src}}" />
        </span>
        <input class="form-input" type="file" wire:model="{{$name}}"  />
    </label>
</x-lf.form.field>
