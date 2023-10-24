@props(["tools"=>'','children'=>null,'class'=>''])
<div class="item" {{$attributes}}>
   <div class="item-content {{$class}}" >
       {{$slot}}
       <div class="tools">
           {{$tools}}
       </div>
   </div>
    {{$children}}
</div>
