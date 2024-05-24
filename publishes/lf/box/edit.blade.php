@props(['title'=>''])
<x-lf.card class="primary">
    <div class="card-header"><span class="title">{{$title}}</span></div>
    <div class="content">{{$slot}}</div>
    <div class="card-footer">
        <span class="btn-primary" wire:click="store">Update</span>
        <div class="last">
            <span class="btn-default" wire:click="resetForm">Refresh</span>
            <span class="btn-default" @click="$dispatch('resetModal')">Cancel</span>
        </div>

    </div>
</x-lf.card>
