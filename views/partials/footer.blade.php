@if($errors->any())
    <div class="notif notif--error">
        <div class="notif__inner">{{ $errors->first() }}</div>
    </div>
@elseif (session('status'))
    <div class="notif notif--success">
        <div class="notif__inner">
            <button type="button" class="notif__close" aria-label="alertClose" onclick="this.parentNode.parentNode.remove()">
                <span class="icon icon--close_modal">
                    <svg><title>Close</title><use xlink:href="#close_modal"></use></svg>
                </span>
            </button>
            {{ session('status') }}
        </div>
    </div>
@elseif (session('restoreMessage'))
    <div class="notif notif--warning">
        <div class="notif__inner">{{ session('restoreMessage') }}</div>
    </div>
@endif

<footer class="footer">
    <div class="container">
        <span class="footer__copyright"><a href="https://twill.io" target="_blank" class="f--light-hover" tabindex="0">Made with Twill</a></span>
        <span class="footer__version">Version {{ config('twill.version', '1.0') }}</span>
    </div>
</footer>
