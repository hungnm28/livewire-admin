@props(['title'=>''])
<x-lf.card>
    <x-slot:header><span class="title">{{$title}}</span></x-slot:header>
    <div class="content">{{$slot}}</div>
    <x-slot:footer>
        <div><span class="btn" wire:click="store">Create</span></div>
        <x-lf.form.toggle name="create" title="Kept Form" />
        <div class="last">
            <span class="btn-warning" wire:click="resetForm">Refresh</span>
            <span class="btn-default" @click="$dispatch('resetModal')">Cancel</span>
        </div>
    </x-slot:footer>
</x-lf.card>
