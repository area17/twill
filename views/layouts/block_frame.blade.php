<iframe class="blockFrame" srcdoc="{{ $blockMarkup }}" style="width: 100%;" onload="resizeIframe(this);" scrolling="no"></iframe>
<script>
    function resizeIframe(blockFrame) {
        blockFrame.style.height = blockFrame.contentWindow.document.documentElement.offsetHeight + 'px';
    }
</script>
