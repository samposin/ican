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
            <a class="navbar-brand link" href="#/landingpages">{{ trans('landingpages::global.module_name_plural') }}</a>
            <a class="navbar-brand no-link" href="javascript:void(0);">\</a>
            <a class="navbar-brand no-link" href="javascript:void(0);">{{ trans('global.analytics') }}</a>
            <a class="navbar-brand no-link" href="javascript:void(0);">\</a>
            <ul class="nav navbar-nav">
              <li class="dropdown">
                <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ $this_page->name }} <span class="caret"></span></a>
                <ul class="dropdown-menu">
<?php
foreach($sites as $site) {
  $sl_landing_page = \Platform\Controllers\Core\Secure::array2string(['landing_page_id' => $site->pages->first()->id]);
  $selected = ($site->id == $site_id) ? ' active' : '';
  echo '<li class="' . $selected . '"><a href="#/landingpages/analytics/' . $sl_landing_page . '">' . $site->name . '</a></li>';
}
?>
                </ul>
              </li>
            </ul>
          </div>

          <div class="collapse navbar-collapse" id="bs-title-navbar">
<?php /*
            <div class="navbar-form navbar-right m-l-15">
              <a href="#/landingpages/analytics/new" class="btn btn-success"><i class="fa fa-plus" aria-hidden="true"></i> {{ trans('global.create_entry') }}</a>
            </div>
*/ ?>
            <div class="navbar-form navbar-right m-l-15">
              <div class="form-control" id="reportrange" style="cursor:pointer;padding:5px 10px; display:table">
                <i class="mi date_range" style="margin:0 5px 0 0"></i> <span></span>
              </div>
            </div>
<?php /*
            <div class="navbar-form navbar-right" style="min-width:240px">

<select id="sites" class="select2-required">
<?php
foreach($sites as $site) {
$sl_landing_page = \Platform\Controllers\Core\Secure::array2string(['landing_page_id' => $site->pages->first()->id]);
$selected = ($site->id == $site_id) ? ' selected' : '';
echo '<option value="' . $sl_landing_page . '"' . $selected . '>' . $site->name . '</option>';
}
?>
</select>

<script>
$('#sites').on('change', function() {
  document.location = '#/landingpages/analytics/' + $(this).val();
});
</script>
              </div>
*/ ?>
<?php if ($data_found) { ?><?php /*
            <ul class="nav navbar-nav navbar-right">
              <li class="dropdown">
                <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ trans('global.export') }} <span class="caret"></span></a>
                <ul class="dropdown-menu">
                  <li><a href="{{ url('landingpages/entries/export?type=xls') }}">Excel5 (xls)</a></li>
                  <li><a href="{{ url('landingpages/entries/export?type=xlsx') }}">Excel2007 (xlsx)</a></li>
                  <li><a href="{{ url('landingpages/entries/export?type=csv') }}">CSV</a></li>
                </ul>
              </li>
            </ul>*/ ?>
<?php } // $data_found ?>
          </div>
        </div>
      </nav>
     
    </div>
  </div>

  <div class="row">
    <div class="col-sm-12">
<?php if ($data_found) { ?>

      <div class="card-box">
        <div id="stats_line_chart">

          <div style="height:244px">
            <div class="loader loader-xs"></div>
          </div>
          
        </div>
      </div>

      <div class="card-box table-responsive">
        <table class="table table-striped table-hover" id="dt-table-analytics" style="width:100%; border: 1px solid #ddd;">
          <thead>
            <tr>
              <th>{{ trans('global.language') }}</th>
              <th>{{ trans('global.browser') }}</th>
              <th>{{ trans('global.version') }}</th>
              <th>{{ trans('global.os') }}</th>
              <th>{{ trans('global.version') }}</th>
              <th>{{ trans('global.brand') }}</th>
              <th>{{ trans('global.model') }}</th>
              <th>{{ trans('global.created') }}</th>
            </tr>
          </thead>
        </table>
      </div>

<?php } else { // $data_found ?>

<div class="panel panel-border panel-inverse">
  <div class="panel-heading">
    <h3 class="panel-title">{{ trans('landingpages::global.no_data_found') }}</h3>
  </div>
  <div class="panel-body">
    <p>{!! trans('landingpages::global.no_data_found_text', ['link' => $this_page->url()]) !!}</p>
  </div>
</div>

<?php } // $data_found ?>
    </div>
  </div>

</div>

<script>
<?php if ($data_found) { ?>

var date_start = '{{ $date_start }}';
var date_end = '{{ $date_end }}';

/* 
 * Google Charts
 */

google.charts.setOnLoadCallback(drawChart);

var chartRangeInstance;
var chartRangeData;
var chartRangeOpts;

 function floorDate(datetime) {
   var newDate = new Date(datetime);
   newDate.setHours(0);
   newDate.setMinutes(0);
   newDate.setSeconds(0);
   return newDate;
 }

function drawChart() {
  var jsonData = $.ajax({
    url: '{{ url('landingpages/analytics/stats/range') }}',
    data: {
      date_start: date_start,
      date_end: date_end,
      _token: '<?= csrf_token() ?>',
      sl: '{{ $sl }}'
    },
    method: 'POST',
    dataType: 'json',
    async: false
  }).responseText;

  if (date_start == date_end) {
    jsonData = JSON.parse(jsonData);
    var visitors = jsonData.rows[0].c[1].v;
    $('#stats_line_chart').html('<h1 class="text-center">' + visitors + ' {{ trans('global.visit_s_') }}</h1>');
  } else {

    chartRangeData = new google.visualization.DataTable(jsonData);

    // load custom ticks
    var ticks = [];
    for (var i = 0; i < chartRangeData.getNumberOfRows(); i++) {
      ticks.push(chartRangeData.getValue(i, 0));
    }

    chartRangeOpts = {
      width: '100%',
      height: 220,
      legend: {position: 'none'},
      pointSize: 4,
      axes: {
        x: {
          0: {side: 'top'}
        }
      },
      vAxis: {
        minValue: 0,
        viewWindow: {min: -0.15, max: parseInt(JSON.parse(jsonData).vars.max) + 1},
        format: '0',
      },
      hAxis: {
        gridlines: {count: 30},
        ticks: ticks,
        format: 'M/d/yy'
      }
    };

    chartRangeInstance = new google.charts.Line(document.getElementById('stats_line_chart'));

    chartRangeInstance.draw(chartRangeData, google.charts.Line.convertOptions(chartRangeOpts));
  }
}

function redrawChart() {
  if (date_start != date_end) {
    chartRangeInstance.draw(chartRangeData, google.charts.Line.convertOptions(chartRangeOpts));
  }
}

$(window).resize($.debounce(100, redrawChart));

/* 
 * DataTable
 */

var analytics_table = $('#dt-table-analytics').DataTable({
  ajax: { 
    url: '{{ url('landingpages/analytics/stats/data') }}',
    type: 'POST',
    data: function(d) {
      d.date_start = date_start;
      d.date_end = date_end;
      d._token = '<?= csrf_token() ?>';
      d.sl = '{{ $sl }}';
    }
  },
  order: [ [7, "desc"] ],
  dom: "<'row'<'col-sm-12 dt-header'<'pull-left'lr><'pull-right'><'pull-right hidden-sm hidden-xs'T><'clearfix'>>>t<'row'<'col-sm-12 dt-footer'<'pull-left'i><'pull-right'p><'clearfix'>>>",
  processing: true,
  serverSide: true,
  stateSave: true,
  responsive: true,
  stripeClasses: [],
  lengthMenu: [
    [10, 25, 50, 75, 100],
    [10, 25, 50, 75, 100]
  ],
  columns: [
    {	data: "language" }, 
    {	data: "client_name" }, 
    {	data: "client_version" }, 
    {	data: "os_name" }, 
    {	data: "os_version" }, 
    {	data: "brand" }, 
    {	data: "model" }, 
    { data: "created_at", width: 120 }
  ],
  fnDrawCallback: function() {
    onDataTableLoad();
  },
  columnDefs: [
    {
      render: function (data, type, row) {
        return '<div data-moment="fromNowDateTime">' + data + '</div>';
      },
      targets: [7]
    }
  ],
  language: {
    search: "",
    emptyTable: "{{ trans('global.empty_table') }}",
    info: "{{ trans('global.dt_info') }}",
    infoEmpty: "",
    infoFiltered: "(filtered from _MAX_ total entries)",
    thousands: "{{ trans('i18n.thousands_sep') }}",
    lengthMenu: "{{ trans('global.show_records') }}",
    processing: '<i class="fa fa-circle-o-notch fa-spin"></i>',
    paginate: {
      first: '<i class="fa fa-fast-backward"></i>',
      last: '<i class="fa fa-fast-forward"></i>',
      next: '<i class="fa fa-caret-right"></i>',
      previous: '<i class="fa fa-caret-left"></i>'
    }
  }
});

<?php } else { // $data_found ?>

<?php } // $data_found ?>

/* 
 * Date Range Picker
 */

$('#reportrange span').html(moment('<?php echo $date_start ?>').format('MMMM D, YYYY') + ' - ' + moment('<?php echo $date_end ?>').format('MMMM D, YYYY'));

daterangepicker_opts.ranges = {
 [ _lang['today'] ]: [ moment(), moment() ],
 [ _lang['yesterday'] ]: [ moment().subtract(1, 'days'), moment().subtract(1, 'days') ],
 [ _lang['last_7_days'] ]: [ moment().subtract(6, 'days'), moment() ],
 [ _lang['last_30_days'] ]: [ moment().subtract(29, 'days'), moment() ],
 [ _lang['this_month'] ]: [ moment().startOf('month'), moment().endOf('month') ],
 [ _lang['last_month'] ]: [ moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month') ]
};
  
daterangepicker_opts.startDate = moment('<?php echo $date_start ?>').format('MM-D-YYYY');
daterangepicker_opts.endDate = moment('<?php echo $date_end ?>').format('MM-D-YYYY');
daterangepicker_opts.minDate = moment('<?php echo $earliest_date ?>').format('MM-D-YYYY');
daterangepicker_opts.maxDate = '<?php echo date('m/d/Y') ?>';

$('#reportrange').daterangepicker(daterangepicker_opts, 
  function(start, end) {
    $('#reportrange span').html(start.format('D MMMM YYYY') + ' - ' + end.format('D MMMM YYYY'));
    startDate = start;
    endDate = end;
});

$('#reportrange').on('apply.daterangepicker', function(ev, picker) {
  $('#reportrange span').html(picker.startDate.format('MMMM D, YYYY') + ' - ' + picker.endDate.format('MMMM D, YYYY'));
  date_start = picker.startDate.format('YYYY-MM-DD');
  date_end = picker.endDate.format('YYYY-MM-DD');
<?php if ($data_found) { ?>
  analytics_table.ajax.reload();
  drawChart();
<?php } $data_found ?>
});

</script>