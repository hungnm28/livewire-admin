@props(['id' => null, 'class' => 'sm:max-w-xl'])

<x-lf.modal :$id :$class {{ $attributes }}>
    <div class="header warning">
        <span class="icon">{!! lfIcon("warning") !!}</span>
        <span class="title">{{$title}}</span>
    </div>
    {{$slot}}
    <div class="footer">
        {{ $footer }}
    </div>
</x-lf.modal>
