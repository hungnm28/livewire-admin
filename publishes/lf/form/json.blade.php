@props(['name','label'=>null,'type'=>'text','class'=>'',"model"=>".live.debounce.300ms",'data'=>[],'id'])
@php($id = $id ?? md5($name))
<x-lf.form.field :$name :$label :class="'form-json ' .$class" :$id>
    <div class="params">
        @foreach($data as $k=>$val)
            <div class="item">
                <span class="key">{{$k}}:</span>
                <span class="value">{{$val}}</span>
                <span class="btn-default xs" wire:click="jsonDelete('{{$name}}','{{$k}}')">{!! mIcon("delete",11) !!}</span>
            </div>
        @endforeach
    </div>
    <div class="form-group" x-data="{key:'',value:''
        ,addItem(){
            if(this.key && this.value){
                $wire.jsonAdd('{{$name}}',this.key,this.value);
                this.key = '';
                this.value = '';
            }
        }
    }">
        <input class="form-input" x-model="key" type="text" name="" placeholder="key ..." />
        <input class="form-input" x-model="value" type="{{$type}}" name="" placeholder="value ..." />
        <span class="btn" @click="addItem">{!! mIcon("add") !!}</span>
    </div>

</x-lf.form.field>
