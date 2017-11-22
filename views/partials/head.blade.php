<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
<meta http-equiv="X-UA-Compatible" content="IE=Edge">

<title>{{ config('app.name') }}</title>

{{-- Global styles --}}
<link href="{{ mix('/assets/admin/css/app.css')}}" rel="stylesheet" />

<!-- head.js -->
<script>
    !function(e){function t(){if(/in/.test(e.readyState))setTimeout(t,9);else for(var s=0;s<e.styleSheets.length;s++){var i=e.styleSheets[s];"html4css"!==i.title&&(i.disabled=!0)}}function s(){!e.readyState&&e.addEventListener&&(e.body?setTimeout(function(){e.readyState="complete"},500):setTimeout(s,9))}function i(){if(/in/.test(e.readyState))setTimeout(i,9);else{var t=e.body,s=e.createElement("div");s.className="svg-sprite",s.innerHTML=c.responseText,t.insertBefore(s,t.childNodes[0])}}var n,c,o=window.A17||{},a=e.documentElement,l=window,r=e.getElementsByTagName("head")[0];o.browserSpec="addEventListener"in l&&l.history.pushState&&e.implementation.hasFeature("http://www.w3.org/TR/SVG11/feature#BasicStructure","1.1")?"html5":"html4",o.touch=!!("ontouchstart"in l||l.documentTouch&&e instanceof DocumentTouch),o.objectFit="objectFit"in a.style,window.A17=o,a.className=a.className.replace(/\bno-js\b/," js "+o.browserSpec+(o.touch?" touch":" no-touch")+(o.objectFit?" objectFit":" no-objectFit")),"html4"===o.browserSpec?(n=e.createElement("link"),n.rel="stylesheet",n.title="html4css",n.href="static/styles/html4css.css",r.appendChild(n),n=e.createElement("script"),n.src="//legacypicturefill.s3.amazonaws.com/legacypicturefill.min.js",r.appendChild(n),s(),t()):(n=e.createElement("script"),n.src="//cdnjs.cloudflare.com/ajax/libs/picturefill/3.0.2/picturefill.min.js",r.appendChild(n),c=new XMLHttpRequest,c.open("GET","/assets/admin/icons/icons.svg",!0),c.send(),c.onload=function(e){c.status>=200&&c.status<400&&i()})}(document);
</script>

@stack('extra_css')
@stack('extra_js_head')
