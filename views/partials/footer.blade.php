@if($errors->any())
    <div class="notif notif--error">
        <div class="notif__inner">{{ $errors->first() }}</div>
    </div>
@elseif (session('status'))
    <div class="notif notif--success">
        <div class="notif__inner">{{ session('status') }}</div>
    </div>
@endif

<footer class="footer">
    <div class="container">
        <span class="footer__copyright">CMS by <a href="https://area17.com" target="_blank" class="f--light-underlined" tabindex="0">AREA 17</a></span>
        <span class="footer__version">Version 1.0</span>
    </div>
</footer>
