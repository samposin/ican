<div class="container">
 
  <div class="row m-t">
    <div class="col-sm-12">
     
       <nav class="navbar navbar-default card-box sub-navbar">
        <div class="container-fluid">

          <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-title-navbar" aria-expanded="false">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand no-link" href="javascript:void(0);">{{ trans('forms::global.module_name_plural') }} ({{ count($forms) }})</a>
          </div>

          <div class="collapse navbar-collapse" id="bs-title-navbar">

            <div class="navbar-form navbar-right">

                <div class="input-group input-group" style="margin:0 5px 0 0">
                  <span class="input-group-addon" onClick="if ($('#grid_search:visible').length) { $('#grid_search').delay().animate({width:'0px'}, 150, '').hide(0); } else { $('#grid_search').show().animate({width:'180px'}, 500, 'easeOutBounce'); }"><i class="mi search"></i></span>
                  <input type="text" class="form-control input" id="grid_search" placeholder="{{ trans('global.search_') }}" style="width:0px;display: none">
                </div>

                <div class="input-group input-group" style="margin:0 5px 0 0">
                  <span class="input-group-addon" onClick="if ($('#order_selector:visible').length) { $('#order_selector').delay().animate({width:'0px'}, 150, '').hide(0); } else { $('#order_selector').show().animate({width:'180px'}, 500, 'easeOutBounce'); }"><i class="mi sort"></i></span>
                  <div style="width: 0; overflow: hidden; display: none" id="order_selector">
                  <div style="min-width:180px">
                    <select id="order" class="select2-required-no-search">
                      <option value="new_first"<?php if ($order == 'new_first') echo ' selected'; ?>>{{ trans('global.new_first') }}</option>
                      <option value="old_first"<?php if ($order == 'old_first') echo ' selected'; ?>>{{ trans('global.old_first') }}</option>
                      <option value="high_converting_first"<?php if ($order == 'high_converting_first') echo ' selected'; ?>>{{ trans('global.high_conversion_first') }}</option>
                      <option value="low_converting_first"<?php if ($order == 'low_converting_first') echo ' selected'; ?>>{{ trans('global.low_conversion_first') }}</option>
                      <option value="most_visited_first"<?php if ($order == 'most_visited_first') echo ' selected'; ?>>{{ trans('global.most_visited_first') }}</option>
                      <option value="least_visited_first"<?php if ($order == 'least_visited_first') echo ' selected'; ?>>{{ trans('global.least_visited_first') }}</option>
                    </select>
                  </div>
                  </div>
                </div>

<script>
$('#order').on('change', function() {
  document.location = '#/forms/order/' + $(this).val();
});
</script>

                <a href="#/forms/create" class="btn btn-success"><i class="fa fa-plus" aria-hidden="true"></i> {{ trans('forms::global.create_form') }}</a>
            </div>

          </div>
        </div>
      </nav>
    
    </div>
  </div>

  <div class="row grid" id="grid">
    <div class="grid-sizer col-xs-6 col-sm-3 col-lg-3" style="display:none"></div>
<?php 
$i = 1;
foreach($forms as $form) {
  $sl_form = \Platform\Controllers\Core\Secure::array2string(['form_id' => $form->id]);
  $edit_url = '#/forms/editor/' . $sl_form;

  $local_domain = 'f/' . $form->local_domain;
  $url = $form->url();

  $sl_form = \Platform\Controllers\Core\Secure::array2string(['form_id' => $form->id]);

  // Check if published file exists
  $variant = 1;
  $storage_root = 'forms/form/' . \Platform\Controllers\Core\Secure::staticHash(\Platform\Controllers\Core\Secure::userId()) . '/' .  \Platform\Controllers\Core\Secure::staticHash($form->id, true) . '/' . $variant;
  $published = (\Storage::disk('public')->exists($storage_root . '/published/index.blade.php')) ? '<span class="badge badge-xs badge-success pull-right">' . trans('global.published') . '</span>' : '<span class="badge badge-xs badge-danger pull-right">' . trans('global.not_published') . '</span>';
?>
    <div class="grid-item col-xs-6 col-sm-3 col-lg-3" style="max-width: 250px" id="item{{ $i }}">

      <div class="grid-item-content portlet shadow-box" data-sl="{{ $sl_form }}">

        <div class="btn-group pull-right">
          <button type="button" class="btn btn-default dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="mi more_vert"></i>
          </button>
          <ul class="dropdown-menu m-t-0">
            <li><a href="{{ $edit_url }}">{{ trans('forms::global.edit_form') }}</a></li>
<?php if (Gate::allows('limitation', 'forms.edit_html')) { ?>
            <li><a href="{{ '#/forms/source/' . $sl_form }}">{{ trans('forms::global.edit_html') }}</a></li>
<?php } ?>
            <li role="separator" class="divider"></li>
            <li><a href="#/forms/entries/{{ $sl_form }}">{{ trans('forms::global.view_entries') }}</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="javascript:void(0);" class="onClickDelete">{{ trans('global.delete') }}</a></li>
          </ul>
        </div>

        <div class="portlet-heading portlet-default">
          <h3 class="portlet-title text-dark" title="{{ $form['name'] }}">{{ $form['name'] }}</h3>
          <div class="clearfix"></div>
        </div>

        <div class="portlet-body" style="padding:0">
         <table class="table table-hover table-striped" style="margin-bottom: 0">
           <tr>
             <td width="33" class="text-center"><i class="mi open_in_browser"></i></td>
             <td><a href="{{ $url }}" target="_blank" class="link">{{ trans('global.visit_online') }}</a></td>
             <td class="text-right"> {!! $published !!}</td>
           </tr>
           <tr>
             <td width="33" class="text-center"><i class="mi pageview"></i></td>
             <td>{{ trans('global.visits') }}:</td>
             <td class="text-right"><strong>{{ number_format($form->visits) }}</strong></td>
           </tr>
           <tr>
             <td class="text-center"><i class="mi input"></i></td>
             <td><a href="#/forms/entries/{{ $sl_form }}" class="link">{{ trans('forms::global.entries') }}</a>:</td>
             <td class="text-right"><strong>{{ number_format($form->entries) }}</strong></td>
           </tr>
         </table>
        </div>

        <div>
          <a href="{{ $edit_url }}" class="preview-container" id="container{{ $i }}" title="{{ $form['name'] }}">
            <iframe src="{{ url($local_domain . '?preview=1') }}" id="frame{{ $i }}" class="preview_frame" frameborder="0" seamless></iframe>
          </a>
        </div>

      </div>

    </div>
<?php 
  $i++;
} 
?>
  </div>
</div>

<style type="text/css">
.panel-footer {
  padding: 0px !important;
}
.preview-container {
  border-top: 2px solid #e5e5e5;
  display: block;
  width:100%;
  height: 120px;
}
.loader.loader-xs {
  margin: -6px auto 0;
}
.preview_frame {
  pointer-events: none;
  position: absolute;
  width: 400%;
  -ms-zoom: 0.25;
  -moz-transform: scale(0.25);
  -moz-transform-origin: 0 0;
  -o-transform: scale(0.25);
  -o-transform-origin: 0 0;
  -webkit-transform: scale(0.25);
  -webkit-transform-origin: 0 0;
}
.portlet-title {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  width: 100%;
}
</style>

<script>
$(function() {
  var $grid = $('.grid').masonry({
    itemSelector: '.grid-item',
    columnWidth: '.grid-sizer',
    percentPosition: true,
    transitionDuration: '0.2s'
  });
  
  $('#grid').liveFilter('#grid_search', 'div.grid-item', {
    filterChildSelector: '.portlet-title',
    after: function() {
      $grid.masonry();
    }
  });

/*
  $('.preview-container').tooltip({
    placement : 'top',
    template: '<div class="tooltip" style="margin-top: 21px"  role="tooltip"><div class="tooltip-inner"></div></div>'
  });
*/

  blockUI('.preview-container');
  $(window).resize(resizeEditFrame);

  function resizeEditFrame() {
    $('.preview_frame').each(function() {
      var frame_height = parseInt($(this).contents().find('html').height());
      var frame_width = parseInt($(this).contents().find('html').width());

      $(this).height(frame_height);

      $(this).parent().height(frame_height / 4);
      //$(this).parent().width(frame_width / 4);
      $(this).parent().width('100%');
    });
  }

<?php
$i = 1;
foreach($forms as $form) {
?>
  $('#frame{{ $i }}').on('load', function() {
    resizeEditFrame();
    unblockUI('#container{{ $i }}');
<?php if ($i == count($forms)) { ?>
    setTimeout(function() {
      $grid.masonry('reloadItems').masonry();
    }, 200);
<?php } ?>
  });
<?php
  $i++;
}
?>

$('.onClickDelete').on('click', function() {
  var sl = $(this).parents('.grid-item-content').attr('data-sl');
  var $item = $(this).parents('.grid-item');

  swal({
    title: _lang['delete'],
    text: _lang['confirm'],
    showCancelButton: true,
    cancelButtonText: _lang['cancel'],
    confirmButtonColor: "#da4429",
    confirmButtonText: _lang['yes_delete']
  }).then(function (result) {

    blockUI();

    var jqxhr = $.ajax({
      url: "{{ url('forms/delete') }}",
      data: {sl: sl,  _token: '<?= csrf_token() ?>'},
      method: 'POST'
    })
    .done(function(data) {
      $item.remove();
      $grid.masonry('reloadItems').masonry();
    })
    .fail(function() {
      console.log('error');
    })
    .always(function() {
     unblockUI();
    });

  }, function (dismiss) {
    // Do nothing on cancel
    // dismiss can be 'cancel', 'overlay', 'close', and 'timer'
  });
});
});
</script>