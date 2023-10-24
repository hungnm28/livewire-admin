@props(["name","fields"=>[]])
@if(data_get($fields,"$name.status"))
    <td>{{$slot}}</td>
@endif
