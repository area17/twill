<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="csrf-token" content="{{ csrf_token() }}" data-behavior="csrf_token">
<title>{{ config('app.name') }}</title>
<link href="/assets/admin/a17cms.css" rel="stylesheet" />
<script src="/assets/admin/a17cms.js"></script>
@verbatim
    <script>
        // TODO: push this to A17 CMS UI repo (added scrolling="no" to the iframe)
        a17cms.Helpers.modal = function(){
          var klass = "a17modal";
          var lightbox_html = "";
          var iframe_html = "";
          var active_class = "modal-opened";
          var lightbox_active = false;

          // lightbox html:
          lightbox_html =  '<div class="'+klass+'_container">\n';
          lightbox_html += '<div class="'+klass+'_mask"></div>\n';
          lightbox_html += '<div class="'+klass+'" style="display:none;">\n';
          lightbox_html += '<div class="'+klass+'_title"><span>{{title}}</span> <a href="#" class="close js-close-lb">Close</a></div>\n';
          lightbox_html += '<div class="'+klass+'_inner">\n{{content}}\n</div>\n</div>\n</div>';
          // iframe html
          iframe_html = '<iframe src="{{url}}" sandbox="allow-forms allow-same-origin allow-scripts allow-top-navigation allow-popups allow-modals" scrolling="no"></iframe>\n';

          // show an iframed modal
          function show_modal_iframe(config) {

            var this_lightbox_html = lightbox_html.replace("{{content}}",iframe_html);
            this_lightbox_html = this_lightbox_html.replace("{{url}}",config.url);
            this_lightbox_html = this_lightbox_html.replace("{{title}}",config.title);
            //
            var $lightbox = $(this_lightbox_html);
            //
            if(config.meta) {
              attach_data($("."+klass,$lightbox), config.meta);
            }
            //
            $('body').append($lightbox);
            //
            lightbox_active = true;
            //
            $('.'+klass+'_mask, .'+klass+' .js-close-lb').on("click",function(event){
              event.preventDefault();
              $.event.trigger({ type: "modal_close" });
            });
            $("."+klass+" iframe").on("load",function(){
              $.event.trigger({ type: "modal_loaded" });
              try {
                $(this).contents().on("keyup",function(event) {
                  if (lightbox_active && event.keyCode == 27) {
                    $.event.trigger({ type: "modal_close" });
                  }
                });
              } catch(err){}
            });
          }

          function show_modal_html(config) {

            var this_lightbox_html = lightbox_html.replace("{{content}}",config.content);
            this_lightbox_html = this_lightbox_html.replace("{{title}}",config.title);
            //
            var $lightbox = $(this_lightbox_html);
            //
            $('body').append($lightbox);
            //
            lightbox_active = true;
            //
            $('.'+klass+'_mask, .'+klass+' .js-close-lb').on("click",function(event){
              event.preventDefault();
              $.event.trigger({ type: "modal_close" });
            });
            a17cms.LoadBehavior($('.'+klass+'_inner')[0]);
            $.event.trigger({ type: "modal_loaded" });
          }

          // close!
          function close_modal() {
            if (lightbox_active) {
              $('.'+klass+'_container').remove();
              $("html").removeClass(active_class);
              lightbox_active = false;
            }
          }

          // attach datas
          function attach_data($modal, datas){
            $.each(datas, function(key, value) {
              $modal.data(key, value);
            });
          }

          // return active lightbox element
          function get_active(){
            return lightbox_active ? $("."+klass).filter('*:first') : undefined;
          }

          // listen for opens
          $(document).on("modal_open",function(event){
            switch (event.modal_config.type) {
              case "iframe":
                show_modal_iframe(event.modal_config);
                break;
              case "html":
                show_modal_html(event.modal_config);
                break;
              default:
                return false;
            }
          });

          // listen for closes
          $(document).on("modal_close",function(event){
            close_modal();
          });

          // listen for lightbox_load
          $(document).on("modal_loaded",function(event){
            $("html").addClass(active_class);
            $("."+klass).show();
          });

          // listen for title updates
          $(document).on("modal_update_title",function(event){
            $('.'+klass+'_title > span').text(event.title);
          });

          // close on escape press
          $(document).on("keyup",function(event) {
            if (lightbox_active && event.keyCode == 27) {
              $.event.trigger({ type: "modal_close" });
            }
          });

          // Public method
          a17cms.Helpers.modal.get_active = get_active;

        };
    </script>
@endverbatim
@stack('extra_css')
@stack('extra_js')
