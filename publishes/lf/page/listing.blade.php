@props(['title'=>'','data'=>null])
<x-lf.page :title="$title">
    {{$slot}}
    <x-slot:tools>
        <span class="h-btn" wire:click="modalCreate" title="Create">{!! lfIcon("add") !!}</span>
    </x-slot:tools>
    @if($data)
        <x-slot:footer>
            {{$data->links()}}
        </x-slot:footer>
    @endif

</x-lf.page>
