<div x-data="{ tab: 'new' }">
    <nav class="-mb-px">
        <a x-bind:class="{ 'bg-gray-100': tab === 'new' }" class="no-underline px-8 border-gray-100 shadow-xs py-2 rounded-lg border" x-on:click.prevent="tab = 'new'"
           href="#">New</a>
        <a :class="{ 'bg-gray-100': tab === 'old' }" class="no-underline px-8 border-gray-100 shadow-xs py-2 rounded-lg border" x-on:click.prevent="tab = 'old'"
           href="#">Old</a>
    </nav>

    <div x-show="tab === 'new'">
        {{$new}}
    </div>
    <div x-show="tab === 'old'">
        {{$old}}
    </div>
</div>
