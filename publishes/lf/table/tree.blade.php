@props(['level'=>0,'has_child'=>0,'v'=>[]])
<tr>
    <td class="tree-box">
        <div class="tree-content">
            <div class="tree">
                @for($i=2;$i<=$level;$i++)
                    <span class="v {{data_get($v,$i,1) == 0?'v-0':''}}"></span>
                @endfor
                @if($level>1)
                    <span class="node"></span>
                @else
                    <span class="s"></span>

                @endif
                @if($has_child)
                    <span class="node-parent lv-{{$level}}">
                       @if($level==1)<span class="root"></span>@endif
                   </span>
                @endif
            </div>
            <div class="h"></div>
        </div>

    </td>
    {{$slot}}
</tr>
