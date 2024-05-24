@props(['title'=>'','record_id'=>0])
<x-lf.card class="info">
    <x-slot:header><span class="title">{{$title}}</span></x-slot:header>
    {{$slot}}
    <x-slot:footer>
        <span class="btn-primary" wire:click="$parent.modalEdit({{$record_id}})">Edit</span>
        <span class="btn-warning" wire:click="$parent.modalDelete({{$record_id}})">Delete</span>
        <div class="last">
            <span class="btn-default" @click="$dispatch('resetModal')">Close</span>
        </div>
    </x-slot:footer>
</x-lf.card>
