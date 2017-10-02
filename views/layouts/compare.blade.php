<!DOCTYPE html>
  <html dir="ltr" lang="en-US" class="no-js">
  <head>
    @include('cms-toolkit::layouts.head')
    <style>
      iframe.preview {
        width: 100%;
        height: 100vh;
      }

      iframe.preview:first-child {
        border-right: 2px solid #b8b8b8;
      }
    </style>
  </head>
  <body style="display:flex; margin: 0">
    <iframe id="iframe_left" class="preview" frameborder="0" srcdoc="{{ $compareHtml }}"></iframe>
    <iframe id="iframe_right" class="preview" frameborder="0" srcdoc="{{ $previewHtml }}"></iframe>
    <script type="text/javascript">
      window.onload = function () {
        $("#iframe_left").contents().scroll(function() {
          $("#iframe_right").contents().scrollTop($("#iframe_left").contents().scrollTop());
        });

        $("#iframe_right").contents().scroll(function() {
          $("#iframe_left").contents().scrollTop($("#iframe_right").contents().scrollTop());
        });

        [].forEach.call(document.querySelectorAll('.preview'), function(el) {
            disableNavigation(el.contentWindow.document.querySelectorAll("a,button"));
          }
        );

        function disableNavigation(anchors) {
          for (var i = 0; i < anchors.length; i++) {
            anchors[i].setAttribute('disabled', 'disabled');
            anchors[i].onclick = function() {
              return false;
            };
          }
        }
      }
    </script>
  </body>
  </html>
