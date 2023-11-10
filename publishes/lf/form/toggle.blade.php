@props(['name','label'=>null,'class'=>'','id','title'=>null])
@php($id = $id ?? md5($name))
<x-lf.form.field :$name :$label :$class :$id>
    <label class="relative inline-flex items-center cursor-pointer">
        <input type="checkbox" wire:model.live="{{$name}}" class="sr-only peer" value="1" {{$attributes}}>
        <span
                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-1 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
        </span>
        @if($title)
            <span class="h-full flex items-center justify-center px-2 peer-checked:color-blue-600">{{$title}}</span>
        @endif
    </label>
</x-lf.form.field>
