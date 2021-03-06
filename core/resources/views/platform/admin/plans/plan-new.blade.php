<div class="container">

  <div class="row m-t">
    <div class="col-sm-12">
      <nav class="navbar navbar-default card-box sub-navbar">
        <div class="container-fluid">
          <div class="navbar-header">
            <a class="navbar-brand no-link" href="javascript:void(0);">{{ trans('global.admin') }}</a>
            <a class="navbar-brand no-link" href="javascript:void(0);">\</a>
            <a class="navbar-brand link" href="#/admin/plans">{{ trans('global.plans') }}</a>
            <a class="navbar-brand no-link" href="javascript:void(0);">\</a>
            <a class="navbar-brand no-link" href="javascript:void(0);">{{ trans('global.create_plan') }}</a>
          </div>
        </div>
      </nav>
    </div>
  </div>

  <form class="ajax" id="frm" method="post" action="{{ url('platform/admin/plan/new') }}">
    <div class="row">
      {!! csrf_field() !!}
      <div class="col-md-4">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title">{{ trans('global.general') }}</h3>
          </div>
          <fieldset class="panel-body">

            <div class="form-group">
              <label for="name">{{ trans('global.name') }} <sup>*</sup></label>
              <input type="text" class="form-control" name="name" id="name" value="" required autocomplete="off">
            </div>

            <div class="form-group">
              <?php
                echo Former::select('currency')
                  ->class('select2-required form-control')
                  ->name('currency')
                  ->options($currencies)
                  ->forceValue('USD')
                  ->label(trans('global.currency'));
                ?>
            </div>

            <div class="form-group" style="margin-top:20px">
              <div class="checkbox checkbox-primary">
                <input name="default" id="default" type="checkbox" value="1">
                <label for="default"> {{ trans('global.default_plan') }} <i class="material-icons help-icon" data-container="body" data-trigger="hover" data-toggle="popover" data-placement="top" data-content="{{ trans('global.default_info') }}">&#xE887;</i></label>
              </div>
            </div>

            <div class="form-group" id="group_field" style="display:none">
              <label for="trial_days">{{ trans('global.trial_days') }} <i class="material-icons help-icon" data-container="body" data-trigger="hover" data-toggle="popover" data-placement="top" data-content="{{ trans('global.trial_days_help') }}">&#xE887;</i></label>
              <input type="number" class="form-control" name="trial_days" value="" autocomplete="off" placeholder="">
            </div>

<script>
  checkTrialPeriod();
  $('#default').on('change', checkTrialPeriod);

  function checkTrialPeriod() {
    if ($('#default').is(':checked')) {
      $('#group_field').show();
    } else {
      $('#group_field').hide();
    }
  }
</script>

            <div class="form-group" style="margin-top:20px">
              <div class="checkbox checkbox-primary">
                <input name="active" id="active" type="checkbox" value="1" checked>
                <label for="active"> {{ trans('global.active') }}</label>
              </div>
            </div>

          </fieldset>
        </div>

            <ul class="nav nav-tabs navtab-custom">
              <li class="active"><a href="#monthly" data-toggle="tab" aria-expanded="false">{{ trans('global.monthly') }}</a></li>
              <li><a href="#annual" data-toggle="tab" aria-expanded="false">{{ trans('global.annual') }} ({{ trans('global.optional') }})</a></li>
            </ul>

            <div class="tab-content" style="padding-bottom:10px">
              <div class="tab-pane active" id="monthly">

                <div class="form-group">
                  <label for="monthly_price">{{ trans('global.price') }} <sup>*</sup></label>
                  <input type="number" step="0.01" class="form-control" name="monthly_price" value="" required autocomplete="off" placeholder="">
                </div>

                <div class="form-group">
                  <label for="monthly_remote_product_id">{{ trans('global.remote_product_id') }} <i class="material-icons help-icon" data-container="body" data-trigger="hover" data-toggle="popover" data-placement="top" data-content="Here you can enter the Avangate or Stripe ID of a product or plan.">&#xE887;</i></label>
                  <input type="text" class="form-control" name="monthly_remote_product_id" value="" autocomplete="off" placeholder="">
                </div>

                <div class="form-group">
                  <label for="monthly_order_url">{{ trans('global.order_url') }} <i class="material-icons help-icon" data-container="body" data-trigger="hover" data-toggle="popover" data-placement="top" data-content="This is currently only used for Avangate. Enter the full order url of this plan.">&#xE887;</i></label>
                  <input type="text" class="form-control" name="monthly_order_url" id="monthly_order_url" value="" autocomplete="off">
                </div>
<?php /*
                <div class="form-group">
                  <label for="monthly_upgrade_url">{{ trans('global.upgrade_url') }}</label>
                  <input type="text" class="form-control" name="monthly_upgrade_url" id="monthly_upgrade_url" value="" autocomplete="off">
                </div>
*/ ?>
              </div>
              <div class="tab-pane" id="annual">
                <div class="form-group">
                  <label for="annual_price">{{ trans('global.price') }} <i class="material-icons help-icon" data-container="body" data-trigger="hover" data-toggle="popover" data-placement="top" data-content="{{ trans('global.annual_price_help') }}">&#xE887;</i></label>
                  <input type="number" step="0.01" class="form-control" name="annual_price" value="" autocomplete="off" placeholder="">
                </div>

                <div class="form-group">
                  <label for="annual_remote_product_id">{{ trans('global.remote_product_id') }} <i class="material-icons help-icon" data-container="body" data-trigger="hover" data-toggle="popover" data-placement="top" data-content="Here you can enter the Avangate or Stripe ID of a product or plan.">&#xE887;</i></label>
                  <input type="text" class="form-control" name="annual_remote_product_id" value="" autocomplete="off" placeholder="">
                </div>


                <div class="form-group">
                  <label for="annual_order_url">{{ trans('global.order_url') }} <i class="material-icons help-icon" data-container="body" data-trigger="hover" data-toggle="popover" data-placement="top" data-content="This is currently only used for Avangate. Enter the full order url of this plan.">&#xE887;</i></label>
                  <input type="text" class="form-control" name="annual_order_url" id="annual_order_url" value="" autocomplete="off">
                </div>
<?php /*
                <div class="form-group">
                  <label for="annual_upgrade_url">{{ trans('global.upgrade_url') }}</label>
                  <input type="text" class="form-control" name="annual_upgrade_url" id="annual_upgrade_url" value="" autocomplete="off">
                </div>
*/ ?>
              </div>
            </div>

      </div>

      <div class="col-md-8">
            <ul class="nav nav-tabs navtab-custom">
              <li class="active"><a href="#create" data-toggle="tab" aria-expanded="false">{{ trans('global.create') }}</a></li>
              <li><a href="#media" data-toggle="tab" aria-expanded="false">{{ trans('global.media') }}</a></li>
              <li><a href="#account" data-toggle="tab" aria-expanded="false">{{ trans('global.account') }}</a></li>
            </ul>

            <div class="tab-content" style="padding-bottom:10px">
              <div class="tab-pane active" id="create">
<?php
foreach($items as $item) {
  if ($item['creatable']) {
?>
                <fieldset class="mdl-shadow--2dp" style="padding: 10px 10px 0 10px; margin-bottom: 20px;">

                  <div class="form-group">
                    <label for="limitations_{{ $item['namespace'] }}_visible">{{ $item['name'] }} <sup>*</sup></label>
                    <div class="checkbox checkbox-primary">
                      <input type="hidden" name="limitations[{{ $item['namespace'] }}][visible]" value="0">
                      <input name="limitations[{{ $item['namespace'] }}][visible]" id="limitations_{{ $item['namespace'] }}_visible" type="checkbox" value="1">
                      <label for="limitations_{{ $item['namespace'] }}_visible">{{ trans('global.active') }}</label>
                    </div>
                  </div>
<?php if ($item['in_plan_amount']) { ?>

                  <div class="form-group">
                    <label for="limitations_{{ $item['namespace'] }}_max">{{ trans('global.maximum') }} <sup>*</sup></label>
                    <input type="number" class="form-control" name="limitations[{{ $item['namespace'] }}][max]" id="limitations_{{ $item['namespace'] }}_max" value="{{ $item['in_plan_default_amount'] }}" required autocomplete="off">
                  </div>
<?php } ?>
<?php 
if (isset($item['extra_plan_config_boolean']) && count($item['extra_plan_config_boolean']) > 0) { 
  foreach ($item['extra_plan_config_boolean'] as $config => $value) {
?>
                  <div class="form-group">
                    <label for="limitations_{{ $item['namespace'] }}_{{ $config }}">{{ trans($item['namespace'] . '::global.' . $config) }} <sup>*</sup></label>
                    <div class="checkbox checkbox-primary">
                      <input type="hidden" name="limitations[{{ $item['namespace'] }}][{{ $config }}]" value="0">
                      <input name="limitations[{{ $item['namespace'] }}][{{ $config }}]" id="limitations_{{ $item['namespace'] }}_{{ $config }}" type="checkbox" value="1">
                      <label for="limitations_{{ $item['namespace'] }}_{{ $config }}">{{ trans('global.active') }}</label>
                    </div>
                  </div>
<?php 
  }
}
?>
<?php 
if (isset($item['extra_plan_config_string']) && count($item['extra_plan_config_string']) > 0) { 
  foreach ($item['extra_plan_config_string'] as $config => $value) {
?>
                  <div class="form-group">
                    <label for="limitations_{{ $item['namespace'] }}_{{ $config }}">{{ trans($item['namespace'] . '::global.' . $config) }} <sup>*</sup></label>
                    <input type="text" class="form-control" name="limitations[{{ $item['namespace'] }}][{{ $config }}]" id="limitations_{{ $item['namespace'] }}_{{ $config }}" value="{{ $value }}" required autocomplete="off">
                  </div>
<?php 
  }
}
?>
                </fieldset>
<?php 
  } 
}
?>
              </div>

              <div class="tab-pane" id="media">
                <fieldset>

                  <div class="form-group">
                    <div class="checkbox checkbox-primary">
                      <input type="hidden" name="limitations[media][visible]" value="0">
                      <input name="limitations[media][visible]" id="limitations_media_visible" type="checkbox" value="1">
                      <label for="limitations_media_visible">{{ trans('global.media') }}</label>
                    </div>
                  </div>

                </fieldset>
              </div>
 
              <div class="tab-pane" id="account">
                <fieldset>

                  <div class="form-group">
                    <div class="checkbox checkbox-primary">
                      <input type="hidden" name="limitations[account][plan_visible]" value="0">
                      <input name="limitations[account][plan_visible]" id="limitations_account_plan_visible" type="checkbox" value="1" checked>
                      <label for="limitations_account_plan_visible">{{ trans('global.plan') }}</label>
                    </div>
                  </div>

                </fieldset>
              </div>


            </div>

      </div>
      <!-- end col -->
      
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="panel panel-inverse panel-border">
        <div class="panel-heading"></div>
        <div class="panel-body">
          <a href="#/admin/plans" class="btn btn-lg btn-default waves-effect waves-light w-md">{{ trans('global.back') }}</a>
          <button class="btn btn-lg btn-success waves-effect waves-light w-md ladda-button" type="submit" data-style="expand-right"><span class="ladda-label">{{ trans('global.create') }}</span></button>
        </div>
      </div>
    </div>
  </div>
</form>
  
</div>