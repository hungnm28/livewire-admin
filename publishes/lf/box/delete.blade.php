@props(['title'=>''])
<x-lf.card class="warning">
    <x-slot:header><span class="title">{{$title}}</span></x-slot:header>
    <div class="delete-confirm">
        <span class="label"><span class="icon"> {!! mIcon("warning") !!}</span> Are you sure!</span>
        <p class="note">Do you really want to delete this record? <br>
            This process cannot be undone.</p>
    </div>
    <x-slot:footer>
        <span class="btn-warning" wire:click="deleteRecord">Delete</span>
        <div class="last"><span class="btn-default" wire:click="resetModal">Cancel</span></div>
    </x-slot:footer>
</x-lf.card>
