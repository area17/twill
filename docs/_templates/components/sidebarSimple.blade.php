<nav
    class="sidebar sidebar--mobileOnly w-3-cols xl:w-240 bg-primary sticky top-80 h-screen-minus-header pr-32 pb-16 border-r border-primary overflow-x-hidden overflow-y-auto"
    x-show="isMobile"
    x-bind:inert="if (isMobile) {
    if (openNav) {
        return false
    } else {
        return true
    }
    } else { return true }"
    x-bind:aria-hidden="if (isMobile) {
        if (openNav) {
            return false
        } else {
            return true
        }
    } else { return true }">
    <button
        class="nav-close hover:opacity-60 h-18 w-18 absolute right-20"
        x-cloak
        x-ref="closeMenu"
        aria-label="close nav"
        x-on:click="openNav = false;  $nextTick(() => $refs.openMenu.focus())">
        <svg class="h-18 w-18" xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10"><defs><style>.close-icon-line{fill:none;stroke-miterlimit:10;stroke-width:1.5px;}</style></defs><title>close_icon</title><line class="close-icon-line" x1="1" y1="1" x2="9" y2="9" stroke="currentColor"/><line class="close-icon-line" x1="9" y1="1" x2="1" y2="9" stroke="currentColor"/>
        </svg>
    </button>
    <ul role="list" class="flex flex-col flex-nowrap min-h-full py-28">
        <li><x-twilldocs::navLink url="/blog/" label="Blog" mobileNav="true" /></li>
        <li class="mt-12"><x-twilldocs::navLink url="/guides/" label="Guides" mobileNav="true" /></li>
        <li class="mt-12 mb-auto"><x-twilldocs::navLink url="/docs/" label="Docs" mobileNav="true" /></li>
        <li class="mt-12 pt-12 border-t border-t-primary"><x-twilldocs::navLink url="/made" label="#MadeWithTwill" mobileNav="true" branded /></li>
        <li class="mt-12"><x-twilldocs::navLink url="https://demo.twill.io/" label="Demo" mobileNav="true" /></li>
        <li class="mt-12"><x-twilldocs::navLink url="https://discord.gg/cnWk7EFv8R" label="Chat" mobileNav="true" /></li>
    </ul>
</nav>
