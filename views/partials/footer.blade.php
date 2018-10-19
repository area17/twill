@include('twill::partials.toaster')
<footer class="footer">
    <div class="container">
        <span class="footer__copyright"><a href="https://twill.io" target="_blank" class="f--light-hover" tabindex="0">Made with Twill</a></span>
        <span class="footer__version">Version {{ config('twill.version', '1.0') }}</span>
    </div>
</footer>
