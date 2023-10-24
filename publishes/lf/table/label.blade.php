@props(["name","fields"=>[]])
@if(data_get($fields,"$name.status"))
    <th>{{$slot}}</th>
@endif
