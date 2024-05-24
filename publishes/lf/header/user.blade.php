<div class="h-user" x-data="{open: false}">
    <label class="h-btn " :class="open ? 'active' : ''" @click="open = !open">{!! mIcon("person",32) !!}</label>
    <div class="box-user" x-show="open"  @click.away="open = false" style="display: none">
        <div class="user">
            <span class="icon">{!! mIcon("person",52) !!}</span>
            <span class="name">{{auth()->user()->name}}</span>
        </div>
        <div class="footer">
            <a href="{{route("profile.show")}}" class="btn">Profile</a>
            <form method="post" action="{{route('logout')}}">
                @csrf
                <button class="btn-default" >Logout</button>
            </form>
        </div>
    </div>
</div>
