<header class="header border-b bg-primary border-primary h-header sticky top-0 z-header">
    <div class="container flex justify-between items-center h-full">
        <a href="/welcome.html">
            <x-twilldocs::logo />
        </a>
        <div class="flex items-center gap-x-32">
            <x-twilldocs::navLink url="/made" label="#MadeWithTwill" branded />
            <x-twilldocs::navLink url="https://demo.twill.io/" label="Demo" />
            <x-twilldocs::navLink url="https://discord.gg/cnWk7EFv8R" label="Chat" />
            <x-twilldocs::navLink url="/blog/" label="Blog" />
            <x-twilldocs::navLink url="/guides/" label="Guides" />
            <x-twilldocs::navLink url="/docs/" label="Docs" />
            <div id="docsearch"></div>
            <x-twilldocs::githubBtn />
            <x-twilldocs::menuBtn />
        </div>
    </div>
</header>
