@extends('cms-toolkit::layouts.main')

@section('content')
    <div class="columns">
        <div class="col">
            @php
                $bucketablesList = collect(array_keys($featurableItemsByBucketable))->toArray();
            @endphp
            @if (count($bucketablesList) > 1)
                <div class="box">
                    <header class="header_small">
                        <h3>What would you like to feature today?</h3>
                    </header>
                    <div class="simple_form">
                        @formField('select', [
                            'field' => "bucketable",
                            'field_name' => "",
                            'list' => collect($featurableItemsByBucketable)->map(function ($bucketable) {
                                return $bucketable['name'];
                            })->values()->toArray(),
                            'data_behavior' => 'selector connected_select',
                            'data_connected_actions' => 'featurable_actions',
                        ])
                        <script>
                            var featurable_actions = [
                                @foreach($bucketablesList as $index => $bucketable)
                                    {
                                      "target": "#{{ $bucketable }}",
                                      "value": "",
                                      "perform": "hide, disable"
                                    },
                                    {
                                      "target": "#{{ $bucketable }}",
                                      "value": "{{ $index }}",
                                      "perform": "show, enable"
                                    }@unless($loop->last),@endunless
                                @endforeach
                            ];
                        </script>
                    </div>
                </div>
            @endif
            @foreach($featurableItemsByBucketable as $bucketable => $featurableItems)
                <section class="box" data-behavior="ajax_listing" id="{{ $bucketable }}">
                    <header class="header_small">
                        <h3><b>{{ $featurableItems['name'] }}</b></h3>
                    </header>
                    @resourceView($bucketable, 'bucketable_list', [
                        'bucketable' => $bucketable,
                        'bucketableName' => $featurableItems['name'],
                        'items' => $featurableItems['items'],
                        'buckets' => $featurableItems['buckets'],
                        'all_buckets' => $buckets
                    ])
                </section>
            @endforeach
        </div>
        <div class="col">
            @foreach($buckets as $bucketKey => $bucket)
                <section class="box box-bucket{{ $loop->index + 1 }}">
                    <header>
                        <h3>{{ $bucket['name'] }}</h3>
                    </header>
                    @extendableView('bucketed_list', [
                        'bucketKey' => $bucketKey,
                        'bucket' => $bucket,
                        'sectionKey' => $sectionKey,
                        'items' => $featuredItemsByBucket[$bucketKey] ?? [],
                    ])
                </section>
            @endforeach
        </div>
    </div>
    {{-- TODO: push this to A17 CMS UI repo --}}
    @verbatim
    <script>
        a17cms.Helpers.listing_update_ajax = function(options){

          var ajax_url, ajax_data;

          if (options.url !== undefined && options.url !== "") {
            ajax_url = options.url;
          } else {
            return false;
          }

          if (options.data === undefined) {
            ajax_data = "";
          } else {
            ajax_data = options.data;
            if ((typeof ajax_data).toLowerCase() === "number") {
              ajax_data = ajax_data.toString();
            } else if (options.data.constructor === Array) {
              if (!ajax_data[0] instanceof Object) {
                ajax_data = ajax_data.join(",");
              } else {
                ajax_data = JSON.stringify(ajax_data);
              }
            }
          }

          if (options.action === undefined || options.action === "post") {
            $.ajax({
              url: ajax_url,
              type: 'POST',
              cache: false,
              data: ajax_data,
              success: function(result) {
                console.log("posted:");
                console.log(ajax_data);
              }
            });
          } else if (options.action === "delete") {
            $.ajax({
              url: ajax_url,
              type: 'DELETE',
              cache: false,
              data: ajax_data,
              success: function(result) {
                console.log("deleted:");
                console.log(ajax_data);
              }
            });
          }
        };

        a17cms.Behaviors.add_to_bucket = function(container){

          var datas = container.data();
          var data_target = datas.bucketTarget;
          var messageNoTarget = (datas.bucketMessageNoTarget === undefined) ? "No target selected for bucket select." : datas.bucketMessageNoTarget;
          var messageLimit = (datas.bucketMessageLimit === undefined) ? "{{name}} has a limit of {{limit}} items" : datas.bucketMessageLimit;

          var targets = [];
          var add_to_bucket_classes = [];

          var empty_tr = ".empty_table";

          // work some things out
          if (data_target !== undefined && data_target.length > 0) {
            data_target = data_target.split(",");
            // work out what the targets are
            $.each(data_target,function(i,target){
              target = target.split(":");
              $target_el = $('table[data-bucket="'+target[0]+'"]');
              targets.push({
                target: targets,
                ids: [],
                types: [],
                el: $target_el,
                btn_class: target[1] || "icon-add",
                limit: $target_el.data("bucket-limit") * 1 || false,
                name: target[0],
                add_url: $target_el.data("bucket-add-url"),
                remove_url: $target_el.data("bucket-remove-url")
              });
              // work out what buttons to look for clicks on
              add_to_bucket_classes.push( (target[1] !== undefined) ? "."+target[1] : ".icon-add");
            });
          } else {
            alert(messageNoTarget);
            return false;
          }

          // investigate targets
          $.each(targets,function(i,target){
            // find data ids in the targets
            var data_ids = [];
            var $target = target.el;
            // store the data ids that are already in the target
            target.ids = a17cms.Helpers.gather_table_row_data_ids($target);
            // disable relevant add buttons in container and other targets
            disableEnableAddBtns(target);
            // build the data types in the target
            // TO DO: make this less clunky...
            $("thead > tr > *",$target).each(function(j){
              // check if its a tool or regular content
              if ((encodeURI(this.textContent) === "%E2%80%94" || this.textContent === "-" || this.textContent === "&ndash;" || this.textContent === "&mdash;" || this.textContent === "&nbsp;" || this.textContent === " " || this.textContent === "") && $(this).data("content") !== undefined) {
                // check for what content should live in the cell
                var data_content = $(this).data("content").toLowerCase();
                // split out data
                var element_and_text = data_content.split(":");
                var element_and_classes = element_and_text[0].split(".");
                // is it a link?
                var is_link = (element_and_classes[0] === "a") ? ' href="#"' : '';
                // whats the el type?
                var element_type = element_and_classes[0];
                // whats the tagname
                element_and_classes.shift();
                var element_classes = ' class="' + element_and_classes.join(" ") + '"';
                // build and push
                target.types.push({
                  el:'<'+element_type+is_link+element_classes+'>'+(element_and_text[1] || "")+'</'+element_type+'>'
                });
              } else {
                target.types.push(this.textContent);
              }
            });
          });

          // build the data types in the container
          function build_data_types(table) {
            var data_types = [];
            $("thead > tr > *",table).each(function(i){
              var dataType = this.textContent;
              if (dataType === "-" || dataType === "—") {
                dataType = "bkt_tool"+i;
              } else if (this.textContent === "" || this.textContent === " ") {
                dataType = "bkt_blank"+i;
              }
              data_types.push(dataType);
            });
            return data_types;
          }

          // make an object that sums up the row to be cloned
          function serializeRow(row) {
            var row_obj = {};
            var container_data_types = build_data_types(row.parents("table"));
            row_obj.data_id = row.data("id");
            row_obj.data_type = row.data("type");
            row.children().each(function(i){
              row_obj[ container_data_types[i] ] = $(this).html();
            });
            return row_obj;
          }

          // generate html for new row
          function generateRow(target_obj,new_row_obj) {
            var new_row_html = [];
            new_row_html.push('<tr data-id="'+new_row_obj.data_id+'" data-type="'+new_row_obj.data_type+'">');
            // loop over the types (or columns) of the target table
            // and then fill in what you can
            $.each(target_obj.types,function(i,type){
              var td_className = "";
              if (type === 'Image' || type === "image") {
                td_className = ' class="thumb"';
              }
              // test for forbidden
              if (type.el) {
                // is this an element?
                var class_regex = /(?:class="|class=')(.*)(?:"|')/i;
                // find its class name if it has one
                var el_class = class_regex.exec(type.el)[1] || "";
                var forbidden = false;
                // look through the new row object to find forbidden items
                $.each(new_row_obj,function(i,item){
                  if (!forbidden && typeof item === "string") {
                    // is item a string, does it match the class names and have a class of forbidden?
                    forbidden = item.search(el_class) > 0 && item.search('forbidden') > 0;
                  }
                });
                // if so
                if (forbidden) {
                  // update the classname of the item we're goin to insert
                  type.el = type.el.replace(class_regex,function(m,p){
                    return 'class="'+p+' forbidden"';
                  });
                }
              }
              //
              new_row_html.push('<td'+td_className+'>'+(type.el || new_row_obj[type] || "")+'</td>');
            });
            new_row_html.push('</tr>');
            return new_row_html.join("");
          }

          // handle add to buckets
          function addToBucket(el) {
            var $this = $(el);
            var target_index_array = 0;
            var target;

            // determine target
            $.each(targets,function(i,target){
              if ($this.attr("class").match(target.btn_class) !== null) {
                target_index_array = i;
              }
            });
            //
            target = targets[target_index_array];
            // is there a limit set?
            if (target.limit && $("tbody tr:not("+empty_tr+")",target.el).length >= target.limit) {
              alert(messageLimit.replace('{{name}}', target.name).replace('{{limit}}', target.limit));
            } else {
              // get data from html and generate new html
              var this_row_data = serializeRow($this.parents("tr"));
              //
              var new_row_html = generateRow(target,this_row_data);
              // insert
              $("tbody",target.el).append(new_row_html);

              // hide empty listing notice
              if($(empty_tr,target.el).length) $(empty_tr,target.el).hide();

              // update
              // store the data ids that are already in the target
              target.ids = a17cms.Helpers.gather_table_row_data_ids(target.el);
              // disable relevant add buttons in container and other targets
              disableEnableAddBtns(target);
              // do ajax
              a17cms.Helpers.listing_update_ajax({
                url: target.add_url,
                data: { id: this_row_data.data_id, type: this_row_data.data_type },
                action: "post"
              });
            }
          }

          // handle remove from buckets
          function removeFromBucket(el,target) {
            var $this = $(el);
            var id = $this.parents("tr").data("id");
            var type = $this.parents("tr").data("type");

            // show empty listing notice
            var $tbody = $this.closest("tbody"), $empty_tr = $tbody.find(empty_tr);
            if($empty_tr.length && $tbody.find("tr:not("+empty_tr+")").length <=1) $tbody.find(empty_tr).show();

            // remove
            $this.parents("tr").remove();
            // do class update
            $('tr[data-id="'+id+'"][data-type="'+type+'"] .'+target.btn_class,container).removeClass("disabled");
            // update
            // store the data ids that are already in the target
            target.ids = a17cms.Helpers.gather_table_row_data_ids(target.el);
            // disable relevant add buttons in container and other targets
            disableEnableAddBtns(target);



            // do ajax
            a17cms.Helpers.listing_update_ajax({
              url: target.remove_url,
              data: { id: id, type: type },
              action: "delete"
            });
          }

          //
          function disableEnableAddBtns(target_obj) {

            // remove disabled classes
            var $bts_in_container = $('tr .'+target_obj.btn_class,container);

            $bts_in_container.removeClass("disabled");
            $bts_in_container.closest("tr").removeClass("tr_disabled");

            $.each(targets,function(i,target){
              var $bts_in_target = $('tr .'+target_obj.btn_class,target.el);
              $bts_in_target.removeClass("disabled");
              $bts_in_target.closest("tr").removeClass("tr_disabled");
            });
            // then add some
            $.each(targets,function(i,target_obj){
              $.each(target_obj.ids,function(i,obj){
                // look in container
                var $bts_in_container_by_id = $('tr[data-id="'+obj.id+'"][data-type="'+obj.type+'"] .'+target_obj.btn_class,container);
                $bts_in_container_by_id.addClass("disabled");
                $bts_in_container_by_id.closest("tr").addClass("tr_disabled");

                // look in other targets
                $.each(targets,function(i,target){
                  if(target_obj.el !== target.el) {
                    var $bts_in_target_by_id = $('tr[data-id="'+obj.id+'"][data-type="'+obj.type+'"] .'+target_obj.btn_class,target.el);
                    $bts_in_target_by_id.addClass("disabled");
                    $bts_in_target_by_id.closest("tr").addClass("tr_disabled");
                  }
                });
              });
            });
          }



          // add item click on container
          container.on("click",add_to_bucket_classes.join(","),function(event){
            event.preventDefault();
            addToBucket(this);
          });

          // add/remove item clicks from targets
          $.each(targets,function(i,target){
            target.el.on("click",add_to_bucket_classes.join(","),function(event){
              event.preventDefault();
              addToBucket(this);
            });
            target.el.on("click",".icon-remove",function(event){
              event.preventDefault();
              removeFromBucket(this,target);
            });
          });
        };

        a17cms.Helpers.gather_table_row_data_ids = function(table){
          var id_arry = [];
          $("tbody tr",table).each(function(i,tr){
            if (tr.hasAttribute("data-id")) {
                id_arry.push({
                    'id': $(tr).attr("data-id"),
                    'type': $(tr).attr("data-type")
                });
            }
          });
          return id_arry;
        };
    </script>
    @endverbatim
@endsection

@section('footer')
    <footer id="footer">
        <ul>
            <li><a href="{{ route("admin.featured.$sectionKey.save") }}" class="btn btn-primary">Save</a></li>
            <li><a href="{{ route("admin.featured.$sectionKey.cancel")  }}" class="btn btn-secondary">Cancel</a></li>
        </ul>
    </footer>
@stop
