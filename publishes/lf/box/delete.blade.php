@props(['title'=>''])
<x-lf.card class="warning">
    <div class="card-header"><span class="title">{{$title}}</span></div>
    <div class="delete-confirm">
        <span class="label"><span class="icon"> {!! lfIcon("warning") !!}</span> Are you sure!</span>
        <p class="note">Do you really want to delete this record? <br>
            This process cannot be undone.</p>
    </div>
    <div class="card-footer">
        <div class="flex-none flex space-x-4">
            <span class="btn-warning" wire:click="deleteRecord">Delete</span>
        </div>
        <span class="btn-default" wire:click="resetModal">Cancel</span>
    </div>
</x-lf.card>
