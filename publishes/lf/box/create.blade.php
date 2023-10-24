@props(['title'=>''])
<x-lf.card>
    <div class="card-header"><span class="title">{{$title}}</span></div>
    <div class="content">{{$slot}}</div>
    <div class="card-footer">
        <div class="flex-none flex space-x-4">
            <span class="btn" wire:click="store">Create</span>
            <span class="btn-default" wire:click="resetForm">Refresh</span>
        </div>
        <span class="btn-default" @click="$dispatch('resetModal')">Cancel</span>
    </div>
</x-lf.card>
