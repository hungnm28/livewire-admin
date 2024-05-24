@props(["name","fields"=>[]])
@if(data_get($fields,"$name.status"))
    <td {{$attributes}}>{{$slot}}</td>
@endif
