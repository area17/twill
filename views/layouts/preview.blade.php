<!DOCTYPE html>
  <html dir="ltr" lang="en-US" class="no-js">
  <head>
    <meta charset="UTF-8">
    <title>Preview</title>
    <link href="/assets/admin/vendor/viewpr/style.css" rel="stylesheet" />
  </head>
  <svg xmlns="http://www.w3.org/2000/svg" class="demo-hidden"><symbol id="reset" viewbox="0 0 41.591 30" class="sg-resize-icon"><path fill-rule="evenodd" clip-rule="evenodd" d="M1.364 29.5c-.476 0-.864-.388-.864-.864V1.364C.5.888.888.5 1.364.5h38.863c.477 0 .864.388.864.864v27.272c0 .476-.387.864-.863.864H1.364z" class="sg-fill"></path><path fill="currentColor" d="M40.227 1c.2 0 .364.163.364.364v27.273c0 .2-.162.364-.363.364H1.364c-.2 0-.364-.163-.364-.364V1.364c0-.2.163-.364.364-.364h38.863m0-1H1.364C.61 0 0 .61 0 1.364v27.273C0 29.39.61 30 1.364 30h38.864c.753 0 1.364-.61 1.364-1.364V1.364C41.592.61 40.98 0 40.227 0z" class="sg-stroke"></path></symbol><symbol id="1280" viewbox="0 0 38 30" class="sg-resize-icon"><path fill="currentColor" fill-rule="evenodd" clip-rule="evenodd" d="M25 24v6H13v-6h12z" class="sg-stroke"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M27 29v1H11v-1h16zM1.02 0h35.96C37.545 0 38 .468 38 1.044v22.91c0 .578-.456 1.046-1.02 1.046H1.02C.455 25 0 24.532 0 23.956V1.044C0 .468.456 0 1.02 0z" class="sg-fill"></path><path d="M1.02 1L1 23.956 36.98 24 37 1.044 36.98 1M14 25h10v4H14z" class="sg-fill"></path><path fill="currentColor" d="M36.98 0H1.02C.455 0 0 .468 0 1.044v22.91C0 24.533.456 25 1.02 25H13v4h-2v1h16v-1h-2v-4h11.98c.564 0 1.02-.468 1.02-1.044V1.044C38 .468 37.544 0 36.98 0zM24 29H14v-4h10v4zm12.98-5L1 23.956 1.02 1h35.96l.02.044L36.98 24z" class="sg-stroke"></path></symbol><symbol id="1024" viewbox="0 0 28 30" class="sg-resize-icon"><path fill-rule="evenodd" clip-rule="evenodd" d="M1.35 24.5c-.47 0-.85-.388-.85-.864V6.364c0-.476.38-.864.85-.864h25.3c.47 0 .85.388.85.864v17.272c0 .476-.38.864-.85.864H1.35z" class="sg-fill"></path><path fill="currentColor" d="M26.65 6c.193 0 .35.163.35.364v17.273c0 .2-.157.364-.35.364H1.35c-.193 0-.35-.163-.35-.364V6.364c0-.2.157-.364.35-.364h25.3m0-1H1.35C.603 5 0 5.61 0 6.364v17.273C0 24.39.604 25 1.35 25h25.3c.746 0 1.35-.61 1.35-1.364V6.364C28 5.61 27.396 5 26.65 5z" class="sg-stroke"></path><path fill="currentColor" d="M24 14c-.552 0-1 .448-1 1s.448 1 1 1 1-.448 1-1-.448-1-1-1z" class="sg-stroke"></path></symbol><symbol id="768" viewbox="0 0 20 30" class="sg-resize-icon"><path fill-rule="evenodd" clip-rule="evenodd" d="M1.364 28.5c-.476 0-.864-.38-.864-.85V2.35c0-.47.388-.85.864-.85h17.273c.476 0 .863.38.863.85v25.3c0 .47-.388.85-.863.85H1.364z" class="sg-fill"></path><path fill="currentColor" d="M18.636 2c.2 0 .364.157.364.35v25.3c0 .193-.163.35-.364.35H1.364c-.2 0-.364-.157-.364-.35V2.35c0-.193.163-.35.364-.35h17.272m0-1H1.364C.61 1 0 1.604 0 2.35v25.3C0 28.397.61 29 1.364 29h17.273C19.39 29 20 28.396 20 27.65V2.35C20 1.603 19.39 1 18.636 1z" class="sg-stroke"></path><path fill="currentColor" d="M10 24c-.552 0-1 .448-1 1s.448 1 1 1 1-.448 1-1-.448-1-1-1z" class="sg-stroke"></path></symbol><symbol id="320" viewbox="0 0 14 30" class="sg-resize-icon"><path fill-rule="evenodd" clip-rule="evenodd" d="M1.364 27.5c-.476 0-.864-.38-.864-.85v-16.3c0-.47.388-.85.864-.85h11.273c.476 0 .863.38.863.85v16.3c0 .47-.388.85-.863.85H1.364z" class="sg-fill"></path><path fill="currentColor" d="M12.636 10c.2 0 .364.157.364.35v16.3c0 .193-.163.35-.364.35H1.364c-.2 0-.364-.157-.364-.35v-16.3c0-.193.163-.35.364-.35h11.272m0-1H1.364C.61 9 0 9.604 0 10.35v16.3C0 27.397.61 28 1.364 28h11.273C13.39 28 14 27.396 14 26.65v-16.3C14 9.603 13.39 9 12.636 9z" class="sg-stroke"></path><path fill="currentColor" d="M7 23c-.552 0-1 .448-1 1s.448 1 1 1 1-.448 1-1-.448-1-1-1z" class="sg-stroke"></path></symbol></svg>
  <body>
    <div class="demo-navbar">
      <ul id="viewpr-list">
      </ul>
    </div>
    <iframe id="viewpr-content" class="demo-content" srcdoc="{{ $previewHtml or 'No preview'}}"></iframe>
    <script src="/assets/admin/vendor/viewpr/viewpr.min.js"></script>
    <script>
      window.onload = function () {
        viewpr(params = {
          svg: true
        });
        var anchors = document.getElementById('viewpr-content').contentWindow.document.querySelectorAll("a,button");
        for (var i = 0; i < anchors.length; i++) {
          anchors[i].setAttribute('disabled', 'disabled');
          anchors[i].onclick = function() {
            return false;
          };
        }
      }
    </script>
  </body>
  </html>
