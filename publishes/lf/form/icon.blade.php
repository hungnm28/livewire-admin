@props(["name","label"=>null,"class"=>null,"val"=>null])
@php
    $icons = \Hungnm28\LivewireAdmin\Supports\IconSupport::getIconNames();
@endphp
<x-lf.form.field :$name :$label :$class>
    <div x-data="{open : false}" class="form-icon">
        <label @click="open=!open" class="btn-primary">@if($val) <span class="icon">{!! mIcon($val) !!}</span> <span class="text">{{$val}}</span> @else Icon @endif</label>
        <div x-show="open" @click.away="open = false" style="display: none" class="box-icons">
            <div class="icons">
                @foreach($icons as $ic)
                    <label @click="open = false" class="item">
                        <input type="radio" class="hidden" wire:model.live="{{$name}}" value="{{$ic}}">
                        <span class="item-show @if($ic == $val)active @endif">
                            <span class="icon">{!! mIcon($ic,22) !!}</span>
                            <span class="text">{{$ic}}</span>
                        </span>
                    </label>
                @endforeach
            </div>
        </div>
    </div>
</x-lf.form.field>

