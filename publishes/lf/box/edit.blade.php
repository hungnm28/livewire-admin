@props(['title'=>''])
<x-lf.card class="primary">
    <div class="card-header"><span class="title">{{$title}}</span></div>
    <div class="content">{{$slot}}</div>
    <div class="card-footer">
        <div class="flex-none flex space-x-4">
            <span class="btn-primary" wire:click="store">Update</span>
            <span class="btn-default" wire:click="resetForm">Refresh</span>
        </div>
        <span class="btn-default" @click="$dispatch('resetModal')">Cancel</span>
    </div>
</x-lf.card>
