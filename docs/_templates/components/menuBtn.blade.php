<button
    class="h-18 w-18 hover:opacity-60"
    x-show="isMobile"
    x-cloak
    aria-label="open nav"
    x-on:click="openNav = true; $nextTick(() => $refs.closeMenu.focus())" x-ref="openMenu">
    <svg
        xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" viewBox="0 0 448 512"
        class="icon">
        <path fill="currentColor"
            d="M436 124H12c-6.627 0-12-5.373-12-12V80c0-6.627 5.373-12 12-12h424c6.627 0 12 5.373 12 12v32c0 6.627-5.373 12-12 12zm0 160H12c-6.627 0-12-5.373-12-12v-32c0-6.627 5.373-12 12-12h424c6.627 0 12 5.373 12 12v32c0 6.627-5.373 12-12 12zm0 160H12c-6.627 0-12-5.373-12-12v-32c0-6.627 5.373-12 12-12h424c6.627 0 12 5.373 12 12v32c0 6.627-5.373 12-12 12z">
        </path>
    </svg>
</button>
