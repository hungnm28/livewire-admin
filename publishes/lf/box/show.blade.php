@props(['title'=>'','record_id'=>0])
<x-lf.card class="info">
    <div class="card-header"><span class="title">{{$title}}</span></div>
    <div class="content">{{$slot}}</div>
    <div class="card-footer">
        <div class="flex-none flex space-x-4">
            <span class="btn-primary" wire:click="$parent.modalEdit({{$record_id}})">Edit</span>
            <span class="btn-warning" wire:click="$parent.modalDelete({{$record_id}})">Delete</span>
        </div>
        <span class="btn-default" @click="$dispatch('resetModal')">Close</span>
    </div>
</x-lf.card>
