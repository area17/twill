<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<meta name="robots" content="noindex,nofollow" />

<title>{{ config('app.name') }}</title>

{{-- Global styles --}}
<link href="{{ mix('/assets/admin/css/app.css')}}" rel="stylesheet" />

<!-- Fonts -->
<link href="/assets/admin/fonts/Inter-Regular.woff2" rel="preload" as="font" type="font/woff2" crossorigin>
<link href="/assets/admin/fonts/Inter-Medium.woff2" rel="preload" as="font" type="font/woff2" crossorigin>

<!-- head.js -->
<script>
    !function(e){function t(){if(/in/.test(e.readyState))setTimeout(t,9);else for(var c=0;c<e.styleSheets.length;c++){var s=e.styleSheets[c];"html4css"!==s.title&&(s.disabled=!0)}}function c(){!e.readyState&&e.addEventListener&&(e.body?setTimeout(function(){e.readyState="complete"},500):setTimeout(c,9))}var s,i=window.A17||{},n=e.documentElement,l=window,o=e.getElementsByTagName("head")[0];i.browserSpec="addEventListener"in l&&l.history.pushState&&e.implementation.hasFeature("http://www.w3.org/TR/SVG11/feature#BasicStructure","1.1")?"html5":"html4",i.touch=!!("ontouchstart"in l||l.documentTouch&&e instanceof DocumentTouch),i.objectFit="objectFit"in n.style,window.A17=i,n.className=n.className.replace(/\bno-js\b/," js "+i.browserSpec+(i.touch?" touch":" no-touch")+(i.objectFit?" objectFit":" no-objectFit")),"html4"===i.browserSpec?(s=e.createElement("link"),s.rel="stylesheet",s.title="html4css",s.href="static/styles/html4css.css",o.appendChild(s),s=e.createElement("script"),s.src="//legacypicturefill.s3.amazonaws.com/legacypicturefill.min.js",o.appendChild(s),c(),t()):(s=e.createElement("script"),s.src="//cdnjs.cloudflare.com/ajax/libs/picturefill/3.0.2/picturefill.min.js",o.appendChild(s))}(document);
</script>

@stack('extra_css')
@stack('extra_js_head')
