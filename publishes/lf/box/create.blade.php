@props(['title'=>''])
<x-lf.card>
    <div class="card-header"><span class="title">{{$title}}</span></div>
    <div class="content">{{$slot}}</div>
    <div class="card-footer">
        <div class="flex-none flex space-x-4">
            <span class="btn" wire:click="store">Create</span>
            <span class="btn-default" wire:click="resetForm">Refresh</span>
        </div>
        <div class="flex-none flex space-x-4">
            <x-lf.form.toggle name="create" title="Kept Form" class="w-auto mt-2" />
            <span class="btn-default" @click="$dispatch('resetModal')">Cancel</span>
        </div>
    </div>
</x-lf.card>
