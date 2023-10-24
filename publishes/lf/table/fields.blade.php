@props(['fields'=>[]])
<div class="inline-block relative" x-data="{fields: false}">
    <label @click="fields = !fields" class="btn-danger xs">{!! lfIcon("list",12) !!}</label>
    <div x-show="fields" @click.away="fields=false" style="display: none"
         class="absolute right-0 bg-white rounded shadow-2xl max-h-60 overflow-auto text-slate-700 z-40">
        @foreach($fields as $k =>$field)
            <label class="flex items-center p-2 px-4 border-b last:border-none text-sm">
                <input type="checkbox" class="mr-1" wire:model.live="fields.{{$k}}.status"/>
                <span class="whitespace-nowrap text-slate-700">{{$field['label']}}</span>
            </label>
        @endforeach
    </div>
</div>
