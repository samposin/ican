<?php namespace Platform\Controllers\App;

use \Platform\Controllers\Core;
use Illuminate\Http\Request;
use App\Notifications\PasswordUpdated;
use App\Notifications\UserCreated;
use Illuminate\Support\Facades\Schema;
use App\Notifications\SendEmail;

class UserController extends \App\Http\Controllers\Controller {

  /*
   |--------------------------------------------------------------------------
   | User Controller
   |--------------------------------------------------------------------------
   |
   | User related logic
   |--------------------------------------------------------------------------
   */

  /**
   * Check for accounts that have been expired
   */
  public static function checkExpiredAccounts()
  {
    $now = \Carbon\Carbon::now()->tz('UTC')->format('Y-m-d H:i:s');
    $yesterday = \Carbon\Carbon::now()->addDays(-1)->tz('UTC')->format('Y-m-d H:i:s');
    $two_days_ago = \Carbon\Carbon::now()->addDays(-2)->tz('UTC')->format('Y-m-d H:i:s');
    $three_days_ago = \Carbon\Carbon::now()->addDays(-3)->tz('UTC')->format('Y-m-d H:i:s');
    $three_days_before_expiration = \Carbon\Carbon::now()->addDays(-12)->tz('UTC')->format('Y-m-d H:i:s');
    $two_weeks_ago = \Carbon\Carbon::now()->addDays(-15)->tz('UTC')->format('Y-m-d H:i:s'); // Two weeks + one day

    // Account expired yesterday
    $users = \App\User::where('active', true)
      ->whereNull('is_reseller_id')
      ->where('expires', '<', $yesterday)
      ->where('expires', '>', $two_days_ago)
      ->where('expires_reminders_sent', 0)
      ->get();

    foreach ($users as $user) {
      $user->expires_reminders_sent = 1;
      $user->save();
      //echo 'Your account expired yesterday';

      // Set language
      app()->setLocale($user->language);

      // Get reseller
      $reseller = Core\Reseller::get($user->reseller_id);

      // Set url root
      \URL::forceScheme('https');
      if ($reseller->domain == '*') {
        if (config('app.url') != 'http://localhost') \URL::forceRootUrl(config('app.url')); 
      } else {
        \URL::forceRootUrl('https://' . $reseller->domain);
      }

      $mail_from = $reseller->mail_from_address;
      $mail_from_name = $reseller->mail_from_name;
      $subject = trans('global.account_expired_yesterday_subject', ['product_name' => $reseller->name]);
      $body_line1 = trans('global.account_expired_yesterday_mail_line1', ['product_name' => $reseller->name]);
      $body_line2 = trans('global.account_expired_yesterday_mail_line2', ['product_name' => $reseller->name]);
      $body_cta = trans('global.account_expired_yesterday_cta');
      $body_cta_link = url('login');

      $user->notify(new SendEmail($mail_from, $mail_from_name, $subject, $body_line1, $body_line2, $body_cta, $body_cta_link));
    }

    // Account deleted in 3 days
    $users = \App\User::where('active', true)
      ->whereNull('is_reseller_id')
      ->where('expires', '<', $three_days_before_expiration)
      ->where('expires', '>', $two_weeks_ago)
      ->where('expires_reminders_sent', 1)
      ->get();

    foreach ($users as $user) {
      $user->expires_reminders_sent = 2;
      $user->save();
      //echo 'Your account is deleted in 3 days';

      // Set language
      app()->setLocale($user->language);

      // Get reseller
      $reseller = Core\Reseller::get($user->reseller_id);

      // Set url root
      \URL::forceScheme('https');
      if ($reseller->domain == '*') {
        if (config('app.url') != 'http://localhost') \URL::forceRootUrl(config('app.url')); 
      } else {
        \URL::forceRootUrl('https://' . $reseller->domain);
      }

      $mail_from = $reseller->mail_from_address;
      $mail_from_name = $reseller->mail_from_name;
      $subject = trans('global.account_deleted_in_3_days_subject', ['product_name' => $reseller->name]);
      $body_line1 = trans('global.account_deleted_in_3_days_mail_line1', ['product_name' => $reseller->name]);
      $body_line2 = trans('global.account_deleted_in_3_days_mail_line2', ['product_name' => $reseller->name]);
      $body_cta = trans('global.account_deleted_in_3_days_cta');
      $body_cta_link = url('login');

      $user->notify(new SendEmail($mail_from, $mail_from_name, $subject, $body_line1, $body_line2, $body_cta, $body_cta_link));
    }

    // Account deleted
    $users = \App\User::where('active', true)
      ->whereNull('is_reseller_id')
      ->where('expires', '<', $two_weeks_ago)
      ->where('expires_reminders_sent', 2)
      ->get();

    foreach ($users as $user) {

      // User hash
      $user_hash = Core\Secure::staticHash($user->id);

      // Delete uploads
      $user_upload_dir = public_path() . '/public/uploads/' . $user_hash;
      \File::deleteDirectory($user_upload_dir);

      // Delete landing page files
      \Storage::disk('public')->deleteDirectory('/landingpages/site/' . $user_hash);

      // Delete form files
      \Storage::disk('public')->deleteDirectory('/forms/form/' . $user_hash);

      // Delete email files
      \Storage::disk('public')->deleteDirectory('/emails/email/' . $user_hash);

      // Delete Eddystones
      $eddystones = \Modules\Eddystones\Http\Controllers\Eddystone::listBeacons($user->id);

      foreach($eddystones['beacons'] as $eddystone) {
        $beaconName = $eddystone->getBeaconName();
        $response = \Modules\Eddystones\Http\Controllers\Eddystone::deleteBeacon($beaconName);
      }

      //echo 'Your account has been deleted';

      // Set language
      app()->setLocale($user->language);

      // Get reseller
      $reseller = Core\Reseller::get($user->reseller_id);

      // Set url root
      \URL::forceScheme('https');
      if ($reseller->domain == '*') {
        if (config('app.url') != 'http://localhost') \URL::forceRootUrl(config('app.url')); 
      } else {
        \URL::forceRootUrl('https://' . $reseller->domain);
      }

      $mail_from = $reseller->mail_from_address;
      $mail_from_name = $reseller->mail_from_name;
      $subject = trans('global.account_deleted_subject', ['product_name' => $reseller->name]);
      $body_line1 = trans('global.account_deleted_mail_line1', ['product_name' => $reseller->name]);
      $body_line2 = trans('global.account_deleted_mail_line2', ['product_name' => $reseller->name]);
      $body_cta = trans('global.account_deleted_cta');
      $body_cta_link = url('register');

      $user->notify(new SendEmail($mail_from, $mail_from_name, $subject, $body_line1, $body_line2, $body_cta, $body_cta_link));

      // Delete user
      $user->forceDelete();
    }
  }

  /**
   * Check if there are trials expiring and/or ending
   */
  public static function checkExpiringTrials()
  {
    $now = \Carbon\Carbon::now()->tz('UTC')->format('Y-m-d H:i:s');
    $tomorrow = \Carbon\Carbon::now()->addDays(1)->tz('UTC')->format('Y-m-d H:i:s');
    $in_two_days = \Carbon\Carbon::now()->addDays(2)->tz('UTC')->format('Y-m-d H:i:s');
    $in_three_days = \Carbon\Carbon::now()->addDays(3)->tz('UTC')->format('Y-m-d H:i:s');

    // Trial ends in 3 days
    $users = \App\User::where('active', true)
      ->whereNull('is_reseller_id')
      ->where('trial_ends_at', '>', $in_two_days)
      ->where('trial_ends_at', '<', $in_three_days)
      ->where('trial_ends_reminders_sent', 0)
      ->get();

    foreach ($users as $user) {
      $user->trial_ends_reminders_sent = 1;
      $user->save();
      //echo 'Your trial ends in 3 days';

      // Set language
      app()->setLocale($user->language);

      // Get reseller
      $reseller = Core\Reseller::get($user->reseller_id);

      // Set url root
      \URL::forceScheme('https');
      if ($reseller->domain == '*') {
        if (config('app.url') != 'http://localhost') \URL::forceRootUrl(config('app.url')); 
      } else {
        \URL::forceRootUrl('https://' . $reseller->domain);
      }

      $mail_from = $reseller->mail_from_address;
      $mail_from_name = $reseller->mail_from_name;
      $subject = trans('global.trial_ends_in_3_days_subject', ['product_name' => $reseller->name]);
      $body_line1 = trans('global.trial_ends_in_3_days_mail_line1', ['product_name' => $reseller->name]);
      $body_line2 = trans('global.trial_ends_in_3_days_mail_line2', ['product_name' => $reseller->name]);
      $body_cta = trans('global.trial_ends_in_3_days_cta');
      $body_cta_link = url('login');

      $user->notify(new SendEmail($mail_from, $mail_from_name, $subject, $body_line1, $body_line2, $body_cta, $body_cta_link));
    }

    // Trial ends in 1 day
    $users = \App\User::where('active', true)
      ->whereNull('is_reseller_id')
      ->where('trial_ends_at', '>', $now)
      ->where('trial_ends_at', '<', $tomorrow)
      ->where('trial_ends_reminders_sent', 1)
      ->get();

    foreach ($users as $user) {
      $user->trial_ends_reminders_sent = 2;
      $user->save();
      //echo 'Your trial ends tomorrow';

      // Set language
      app()->setLocale($user->language);

      // Get reseller
      $reseller = Core\Reseller::get($user->reseller_id);

      // Set url root
      \URL::forceScheme('https');
      if ($reseller->domain == '*') {
        if (config('app.url') != 'http://localhost') \URL::forceRootUrl(config('app.url')); 
      } else {
        \URL::forceRootUrl('https://' . $reseller->domain);
      }

      $mail_from = $reseller->mail_from_address;
      $mail_from_name = $reseller->mail_from_name;
      $subject = trans('global.trial_ends_tomorrow_subject', ['product_name' => $reseller->name]);
      $body_line1 = trans('global.trial_ends_tomorrow_mail_line1', ['product_name' => $reseller->name]);
      $body_line2 = trans('global.trial_ends_tomorrow_mail_line2', ['product_name' => $reseller->name]);
      $body_cta = trans('global.trial_ends_tomorrow_cta');
      $body_cta_link = url('login');

      $user->notify(new SendEmail($mail_from, $mail_from_name, $subject, $body_line1, $body_line2, $body_cta, $body_cta_link));
    }

    // Trial has ended
    $users = \App\User::where('active', true)
      ->whereNull('is_reseller_id')
      ->where('trial_ends_at', '<', $now)
      ->where('trial_ends_reminders_sent', 2)
      ->get();

    foreach ($users as $user) {

      // User hash
      $user_hash = Core\Secure::staticHash($user->id);

      // Delete uploads
      $user_upload_dir = public_path() . '/public/uploads/' . $user_hash;
      \File::deleteDirectory($user_upload_dir);

      // Delete landing page files
      \Storage::disk('public')->deleteDirectory('/landingpages/site/' . $user_hash);

      // Delete form files
      \Storage::disk('public')->deleteDirectory('/forms/form/' . $user_hash);

      // Delete email files
      \Storage::disk('public')->deleteDirectory('/emails/email/' . $user_hash);

      // Delete Eddystones
      $eddystones = \Modules\Eddystones\Http\Controllers\Eddystone::listBeacons($user->id);

      foreach($eddystones['beacons'] as $eddystone) {
        $beaconName = $eddystone->getBeaconName();
        $response = \Modules\Eddystones\Http\Controllers\Eddystone::deleteBeacon($beaconName);
      }

      // Set language
      app()->setLocale($user->language);

      // Get reseller
      $reseller = Core\Reseller::get($user->reseller_id);

      // Set url root
      \URL::forceScheme('https');
      if ($reseller->domain == '*') {
        if (config('app.url') != 'http://localhost') \URL::forceRootUrl(config('app.url')); 
      } else {
        \URL::forceRootUrl('https://' . $reseller->domain);
      }

      $mail_from = $reseller->mail_from_address;
      $mail_from_name = $reseller->mail_from_name;
      $subject = trans('global.trial_has_ended_subject', ['product_name' => $reseller->name]);
      $body_line1 = trans('global.trial_has_ended_mail_line1', ['product_name' => $reseller->name]);
      $body_line2 = trans('global.trial_has_ended_mail_line2', ['product_name' => $reseller->name]);
      $body_cta = trans('global.trial_has_ended_cta');
      $body_cta_link = url('register');

      $user->notify(new SendEmail($mail_from, $mail_from_name, $subject, $body_line1, $body_line2, $body_cta, $body_cta_link));

      // Delete user
      $user->forceDelete();

      //echo 'Sad to see you go!';
    }

  }

  /**
   * User management
   */
  public function showUsers()
  {
    $users = \App\User::orderBy('name')->get();

    return view('platform.admin.users.users', compact('users'));
  }

  /**
   * New user
   */
	public function showNewUser()
    {
        if (\Gate::allows('owner-management'))
        {
            $resellers = \App\Reseller::orderBy('name')->get();
            //$admins=\App\User::where('role','=','admin')->orderBy('name')->get();
            $reseller_id = Core\Reseller::get()->id;
            $admins=\App\User::where('reseller_id',$reseller_id)->where('role','=','admin')->orderBy('name')->get();
        }
        else
        {
            $resellers = null;
            $reseller_id = Core\Reseller::get()->id;
            $admins=\App\User::where('reseller_id',$reseller_id)->where('role','=','admin')->orderBy('name')->get();
        }

    if (\Gate::allows('admin-management')) {
      $plans = \App\Plan::select([\DB::raw('CONCAT(resellers.name, " - ", plans.name) AS name'), 'plans.id', 'resellers.active as reseller_active', 'plans.active as plan_active'])->leftjoin('resellers', 'resellers.id', '=', 'plans.reseller_id')->orderBy('resellers.name', 'ASC')->orderBy('plans.order', 'ASC')->get();

      $plans_list = [];
      if (count($plans) > 0) {
        foreach ($plans as $plan) {
          $suffix = '';
          if ($plan->plan_active != 1 && $plan->id > 1) $suffix .= ' [inactive]';
          if ($plan->reseller_active != 1) $suffix .= ' [inactive reseller]';
          $plans_list[$plan->id] = $plan->name . $suffix;
        }
      }
    } else {
      $plans = null;
    }

    $default_plan = \App\Plan::where('active', 1)->where('default', 1)->first();

    return view('platform.admin.users.user-new', compact('resellers', 'plans', 'plans_list', 'default_plan','admins'));
  }

  /**
   * Edit user
   */
  public function showEditUser()
  {
    $sl = request()->input('sl', '');

    if($sl != '') {
      $qs = Core\Secure::string2array($sl);
      $user = \App\User::where('id', $qs['user_id'])->first();

			if (\Gate::allows('owner-management'))
			{
                $resellers = \App\Reseller::orderBy('name')->get();
                //$admins=\App\User::where('role','=','admin')->orderBy('name')->get();
                //$reseller_id = Core\Reseller::get()->id;
                $reseller_id=$user->reseller_id;
                $admins=\App\User::where('reseller_id',$reseller_id)->where('role','=','admin')->orderBy('name')->get();
            }
            else
            {
                $resellers = null;
                $reseller_id = Core\Reseller::get()->id;
                $admins=\App\User::where('reseller_id',$reseller_id)->where('role','=','admin')->orderBy('name')->get();
            }

      if (\Gate::allows('admin-management')) {
        $plans = \App\Plan::select([\DB::raw('CONCAT(resellers.name, " - ", plans.name) AS name'), 'plans.id', 'resellers.active as reseller_active', 'plans.active as plan_active'])->leftjoin('resellers', 'resellers.id', '=', 'plans.reseller_id')->orderBy('resellers.name', 'ASC')->orderBy('plans.order', 'ASC')->get();

        $plans_list = [];
        if (count($plans) > 0) {
          foreach ($plans as $plan) {
            $suffix = '';
            if ($plan->plan_active != 1 && $plan->id > 1) $suffix .= ' [inactive]';
            if ($plan->reseller_active != 1) $suffix .= ' [inactive reseller]';
            $plans_list[$plan->id] = $plan->name . $suffix;
          }
        }
      } else {
        $plans = null;
      }

      return view('platform.admin.users.user-edit', compact('sl', 'user', 'resellers', 'plans', 'plans_list','admins'));
    }
  }

  /**
   * Upload avatar
   */
  public function postAvatar() {
    $input = array(
      'file' => \Request::file('file'),
      'extension'  => strtolower(\Request::file('file')->getClientOriginalExtension())
    );

    $rules = array(
      'file' => 'mimes:jpeg,gif,png',
      'extension'  => 'required|in:jpg,jpeg,png,gif'
    );

    $validator = \Validator::make($input, $rules);

    if($validator->fails()) {
       echo $validator->messages()->first();
       die();
    } else {
      $sl = request()->input('sl', null);
  
      if($sl != null) {
        $data = Core\Secure::string2array($sl);
        $user_id = $data['user_id'];
      } else {
        $user_id = \Auth::user()->id;
      }

      $user = \App\User::find($user_id);
      $user->avatar = $input['file'];
      $user->save();

      echo $user->avatar->url('default');
    }
  }

  /**
   * Delete avatar
   */
  public function postDeleteAvatar() {
    $sl = request()->input('sl', null);

    if($sl != null) {
      $data = Core\Secure::string2array($sl);
      $user_id = $data['user_id'];
    } else {
      $user_id = \Auth::user()->id;
    }

    $user = \App\User::find($user_id);
    $user->avatar = STAPLER_null;
    $user->save();

    return response()->json(['src' => $user->getAvatar()]);
  }

  /**
   * Add new user
   */
  public function postNewUser()
  {
    $input = array(
      'timezone' => request()->input('timezone'),
      'language' => request()->input('language'),
      'email' => request()->input('email'),
      'name' => request()->input('name'),
      'password' => request()->input('password'),
      'mail_login' => (bool) request()->input('mail_login', false),
      'active' => (bool) request()->input('active', false),
      'role' =>request()->input('role'),
      'plan_id' =>request()->input('plan_id', null),
      'reseller_id' =>request()->input('reseller_id', Core\Reseller::get()->id),
      'trial_ends_at' =>request()->input('trial_ends_at', null),
      'expires' =>request()->input('expires', null),
      'metatag' =>request()->input('metatag', null),
      'business_name' =>request()->input('business_name', null),
      'manager_name' =>request()->input('manager_name', null),
      'store_number' =>request()->input('store_number', null),
      'address' =>request()->input('address', null),
      'business_type' =>request()->input('business_type', null),
      'phone' =>request()->input('phone', null),
      'phone2' =>request()->input('phone2', null),
      'phone3' =>request()->input('phone3', null),
      'email_2' =>request()->input('email_2', null),
      'email_3' =>request()->input('email_3', null),
      'website_1' =>request()->input('website_1', null),
      'website_2' =>request()->input('website_2', null),
      'facebook_url' =>request()->input('facebook_url', null),
      'instagram_url' =>request()->input('instagram_url', null),
      'linked_in_url' =>request()->input('linked_in_url', null),
      'youtube_url' =>request()->input('youtube_url', null),
	  'notes' =>request()->input('notes', null),
	  'admin_id' =>request()->input('admin_id', null),




    );

    $rules = array(
      'email' => 'required|email|max:155|unique:users',
      'name' => 'required|max:64',
      'password' => 'required|min:6|max:32'
    );

    $validator = \Validator::make($input, $rules);

    if($validator->fails())
    {
      $response = array(
        'type' => 'error', 
        'reset' => false, 
        'msg' => $validator->messages()->first()
      );
    }
    else
    {
      $user = new \App\User;

      $user->plan_id = (is_numeric($input['plan_id'])) ? $input['plan_id'] : null;
      $user->name = $input['name'];
      $user->email = $input['email'];
      $user->api_token = str_random(60);
      $user->language = $input['language'];
      $user->timezone = $input['timezone'];
      $user->active = $input['active'];
      $user->role = $input['role'];
      $user->password = bcrypt($input['password']);


	  $user->metatag = $input['metatag'];
      $user->business_name = $input['business_name'];
      $user->manager_name = $input['manager_name'];
      $user->store_number = $input['store_number'];
      $user->address = $input['address'];
      $user->business_type = $input['business_type'];
      $user->phone = $input['phone'];
      $user->phone2 = $input['phone2'];
      $user->phone3 = $input['phone3'];
      $user->email_2 = $input['email_2'];
      $user->email_3 = $input['email_3'];
      $user->website_1 = $input['website_1'];
      $user->website_2 = $input['website_2'];
      $user->facebook_url = $input['facebook_url'];
      $user->instagram_url = $input['instagram_url'];
      $user->linked_in_url = $input['linked_in_url'];
      $user->youtube_url = $input['youtube_url'];
      $user->notes = $input['notes'];

      $user->admin_id =NULL;
      if($input['role']=='user')
      {
        if($input['admin_id']!=null)
        {
            $user->admin_id =$input['admin_id'];
        }
      }


      //if (\Gate::allows('owner-management')) {
      if (\Gate::allows('reseller-management')) {
        $user->reseller_id = $input['reseller_id'];
        $user->trial_ends_at = ($input['trial_ends_at'] != null) ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $input['trial_ends_at'], \Auth::user()->timezone)->tz('UTC') : null;
        $user->expires = ($input['expires'] != null) ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $input['expires'], \Auth::user()->timezone)->tz('UTC') : null;
      } else {
        $user->reseller_id = Core\Reseller::get()->id;
      }

      if($input['mail_login'])
      {
        // Send mail with credentials
        $reseller = Core\Reseller::get();

        $user->notify(new UserCreated($input['password'], $reseller->url));

      }

      if($user->save())
      {
        $response = array(
          'type' => 'success',
          'redir' => '#/admin/users'
        );
      }
      else
      {
        $response = array(
          'type' => 'error',
          'reset' => false, 
          'msg' => $user->errors()->first()
        );
      }
    }
    return response()->json($response);
  }

  /**
   * Save user changes
   */
  public function postUser()
  {
    $sl = request()->input('sl', '');

    if($sl != '')
    {
      $qs = Core\Secure::string2array($sl);

      if (config('app.demo') && $qs['user_id'] == 1) {
        return response()->json([
          'type' => 'error',
          'reset' => false, 
          'msg' => "This is disabled in the demo"
        ]);
      }

      $user = \App\User::find($qs['user_id']);

      $input = array(
        'timezone' => request()->input('timezone'),
        'language' => request()->input('language'),
        'email' => request()->input('email'),
        'name' => request()->input('name'),
        'new_password' => request()->input('new_password'),
        'active' => (bool) request()->input('active', false),
        'mail_login' => (bool) request()->input('mail_login', false),
        'role' =>request()->input('role', null),
        'plan_id' =>request()->input('plan_id', null),
        'reseller_id' =>request()->input('reseller_id', null),
        'trial_ends_at' =>request()->input('trial_ends_at', null),
        'expires' =>request()->input('expires', null),
		'metatag' =>request()->input('metatag', null),
        'business_name' =>request()->input('business_name', null),
        'manager_name' =>request()->input('manager_name', null),
        'store_number' =>request()->input('store_number', null),
        'address' =>request()->input('address', null),
        'business_type' =>request()->input('business_type', null),
        'phone' =>request()->input('phone', null),
        'phone2' =>request()->input('phone2', null),
        'phone3' =>request()->input('phone3', null),
        'email_2' =>request()->input('email_2', null),
        'email_3' =>request()->input('email_3', null),
        'website_1' =>request()->input('website_1', null),
        'website_2' =>request()->input('website_2', null),
        'facebook_url' =>request()->input('facebook_url', null),
        'instagram_url' =>request()->input('instagram_url', null),
        'linked_in_url' =>request()->input('linked_in_url', null),
        'youtube_url' =>request()->input('youtube_url', null),
	    'notes' =>request()->input('notes', null),
	    'admin_id' =>request()->input('admin_id', null),
      );

      $rules = array(
        'email' => 'required|email|unique:users,email,' . $qs['user_id'],
        'new_password' => 'nullable|min:5|max:32',
        'name' => 'required|max:64',
        'timezone' => 'required'
      );

      $validator = \Validator::make($input, $rules);

      if($validator->fails())
      {
        $response = array(
          'type' => 'error', 
          'reset' => false, 
          'msg' => $validator->messages()->first()
        );
      }
      else
      {
        $user->name = $input['name'];
        $user->email = $input['email'];
        $user->timezone = $input['timezone'];
        $user->language = $input['language'];


		$user->metatag = $input['metatag'];
        $user->business_name = $input['business_name'];
        $user->manager_name = $input['manager_name'];
        $user->store_number = $input['store_number'];
        $user->address = $input['address'];
        $user->business_type = $input['business_type'];
        $user->phone = $input['phone'];
        $user->phone2 = $input['phone2'];
        $user->phone3 = $input['phone3'];
        $user->email_2 = $input['email_2'];
        $user->email_3 = $input['email_3'];
        $user->website_1 = $input['website_1'];
        $user->website_2 = $input['website_2'];
        $user->facebook_url = $input['facebook_url'];
        $user->instagram_url = $input['instagram_url'];
        $user->linked_in_url = $input['linked_in_url'];
        $user->youtube_url = $input['youtube_url'];
        $user->notes = $input['notes'];

        $user->admin_id =NULL;
        if($input['role']=='user')
        {
            if($input['admin_id']!=null)
            {
                $user->admin_id =$input['admin_id'];
            }
        }


        //if ($qs['user_id'] > 1 && \Gate::allows('owner-management'))
        if ($qs['user_id'] > 1 && \Gate::allows('reseller-management'))
        {
          $user->plan_id = (is_numeric($input['plan_id'])) ? $input['plan_id'] : null;
          $user->active = $input['active'];
          if ($input['role'] != null) $user->role = $input['role'];

          //if (\Gate::allows('owner-management')) {
          if (\Gate::allows('reseller-management')) {
            if ($input['reseller_id'] != null) $user->reseller_id = $input['reseller_id'];
            $user->trial_ends_at = ($input['trial_ends_at'] != null) ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $input['trial_ends_at'], \Auth::user()->timezone)->tz('UTC') : null;
            $user->expires = ($input['expires'] != null) ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $input['expires'], \Auth::user()->timezone)->tz('UTC') : null;
          }
        }

        if($input['new_password'] != '')
        {
          $user->password = bcrypt($input['new_password']);

          if($input['mail_login'])
          {
            // Send mail with credentials
            $reseller = Core\Reseller::get();

            $user->notify(new PasswordUpdated($input['new_password']));
          }
        }

        if($user->save())
        {
          $response = array(
            'type' => 'success',
            'redir' => '#/admin/users'/*
            'type' => 'success',
            'reset' => false, 
            'msg' => trans('global.changes_saved')*/
          );
        }
        else
        {
          $response = array(
            'type' => 'error',
            'reset' => false, 
            'msg' => $user->errors()->first()
          );
        }
      }
      return response()->json($response);
    }
  }

  /**
   * Login as user
   */
  public function getLoginAs($sl)
  {
    if($sl != '')
    {
      $qs = Core\Secure::string2array($sl);
      $user = \App\User::find($qs['user_id']);

      if ($user->reseller_id != null)
      {
        // Set session to redirect to in case of logout
        $logout = Core\Secure::array2string(['user_id' => \Auth::user()->id]);
        \Session::put('logout', $logout);

        \Auth::loginUsingId($qs['user_id']);

        return redirect('platform');
      }
    }
  }

  /**
   * Delete user
   */
  public function postUserDelete()
  {
    //if (! \Gate::allows('owner-management')) return;
    if (! \Gate::allows('reseller-management')) return;

    $sl = request()->input('sl', '');

    if($sl != '')
    {
      $qs = Core\Secure::string2array($sl);
      $response = array('result' => 'success');

      if (config('app.demo') && $qs['user_id'] == 1) {
        return response()->json([
          'type' => 'error',
          'reset' => false, 
          'msg' => "This is disabled in the demo"
        ]);
      }

      $user = \App\User::where('id', '>',  1)->whereNull('is_reseller_id')->where('id', '=',  $qs['user_id'])->first();

      if(! empty($user))
      {
        $user_role=$user->role;

        $user = \App\User::where('id', '=',  $qs['user_id'])->forceDelete();

        $user_id_hash = Core\Secure::staticHash($qs['user_id']);

        // Delete user uploads
        $user_dir = public_path() . '/uploads/' . $user_id_hash;
        \File::deleteDirectory($user_dir);

        // Delete user public storate
        \Storage::disk('public')->delete('landingpages/site/' . $user_id_hash);

        $user_dir = public_path() . '/uploads/' . $user_id_hash;
        \File::deleteDirectory($user_dir);

        // Delete user landing stats table if exist
        $tbl_name = 'x_landing_stats_' . $qs['user_id'];
        Schema::dropIfExists($tbl_name);

        // Delete user form entries table if exist
        $tbl_name = 'x_form_entries_' . $qs['user_id'];
        Schema::dropIfExists($tbl_name);

        if($user_role=='admin')
        {
	        //If this user assigned as admin then removes from all users
	        $update = \App\User::where('admin_id', $qs['user_id'])->update(['admin_id' => null]);
        }

      }
      else
      {
        $response = array('msg' => trans('global.cant_delete_owner'));
      }
    }
    return response()->json($response);
  }

  /**
   * Get user data
   */
  public function getUserData(Request $request)
  {
    $sql_reseller = "1=1";
    $sql_role = "1=1";

    if (! \Gate::allows('owner-management')) {
      $reseller_id = Core\Reseller::get()->id;
      $sql_reseller = "reseller_id = " . $reseller_id;
    }

    if (\Auth::user()->role == 'admin')
    {
      //$sql_role = "role <> 'admin' AND role <> 'owner'";
      $sql_role = "admin_id=".auth()->user()->id;

    }

    $order_by = $request->input('order.0.column', 0);
    $order = $request->input('order.0.dir', 'asc');
    $search = $request->input('search.regex', '');
    $q = $request->input('search.value', '');
    $start = $request->input('start', 0);
    $draw = $request->input('draw', 1);
    $length = $request->input('length', 10);
    $data = array();

    if (\Gate::allows('owner-management')) {
      $aColumn = array('reseller_name', 'name', 'email', 'role', 'logins', 'last_login', 'users.trial_ends_at', 'plan', 'users.expires', 'users.created_at', 'users.active','metatag');
    } else {      
      $aColumn = array('name', 'email', 'role', 'logins', 'last_login', 'users.trial_ends_at', 'plan', 'users.expires', 'users.created_at', 'users.active','metatag',);
    }

    if($q != '')
    {
      $count = \App\User::leftJoin('resellers as r', 'r.id', '=', 'reseller_id')
        ->select(array('users.*', 'r.name as reseller_name', 'r.favicon as favicon'))
        ->whereRaw($sql_reseller)->whereRaw($sql_role)
        ->where('parent_id', '=', null)
        ->where(function ($query) use($q) {
          $query->orWhere('email', 'like', '%' . $q . '%')
          ->orWhere('role', 'like', '%' . $q . '%')
          ->orWhere('r.name', 'like', '%' . $q . '%')
          ->orWhere('users.name', 'like', '%' . $q . '%')
          ->orWhere('users.metatag', 'like', '%' . $q . '%');
        })
        ->count();

      $oData = \App\User::orderBy($aColumn[$order_by], $order)
        ->leftJoin('resellers as r', 'r.id', '=', 'reseller_id')
        ->select(array('users.*', 'r.name as reseller_name', 'r.favicon as favicon'))
        ->whereRaw($sql_reseller)->whereRaw($sql_role)
        ->where('parent_id', '=', null)
        ->where(function ($query) use($q) {
          $query->orWhere('email', 'like', '%' . $q . '%')
          ->orWhere('role', 'like', '%' . $q . '%')
          ->orWhere('r.name', 'like', '%' . $q . '%')
          ->orWhere('users.name', 'like', '%' . $q . '%')
          ->orWhere('users.metatag', 'like', '%' . $q . '%');
        })
        ->take($length)->skip($start)->get();
    }
    else
    {
      $count = \App\User::leftJoin('resellers as r', 'r.id', '=', 'reseller_id')->whereRaw($sql_reseller)->whereRaw($sql_role)->where('parent_id', '=', null)->select(array('users.*', 'r.name as reseller_name', 'r.favicon as favicon'))->count();
      $oData = \App\User::with('admin')->orderBy($aColumn[$order_by], $order)->leftJoin('resellers as r', 'r.id', '=', 'reseller_id')->whereRaw($sql_reseller)->whereRaw($sql_role)->where('parent_id', '=', null)->select(array('users.*', 'r.name as reseller_name', 'r.favicon as favicon'))->take($length)->skip($start)->get();
    }


    //echo '<pre>';
    //print_r($oData->toArray());
    //die();

    if($length == -1) $length = $count;

    $recordsTotal = $count;
    $recordsFiltered = $count;

    foreach($oData as $row) {
      $expires = ($row->expires == null) ? '-' : $row->expires->format('Y-m-d');
      $last_login = ($row->last_login == null) ? '' : $row->last_login->timezone(\Auth::user()->timezone)->format('Y-m-d H:i:s');
      $trial_ends_at = ($row->trial_ends_at == null) ? '-' : $row->trial_ends_at->timezone(\Auth::user()->timezone)->format('Y-m-d H:i:s');
      $expires = ($row->expires == null) ? '-' : $row->expires->timezone(\Auth::user()->timezone)->format('Y-m-d H:i:s');
      $undeletable = ($row->id == 1) ? 1 : 0;

      $plan = ($row->plan == null) ? trans('global.free') : $row->plan->name;
      if ($row->plan_id == 1) $plan .= ' <i class="fa fa-lock" aria-hidden="true"></i>';

      if (\Gate::allows('owner-management')) {
        $favicon = ($row->favicon == null) ? url('favicon.ico') : $row->favicon;
        $reseller = ($row->reseller_name == '') ? '-' : $row->reseller_name;
      } else {
        $favicon = '-';
        $reseller = '-';
      }

      $admin_name='';
      if(isset($row->admin->name))
        $admin_name=$row->admin->name;

      $data[] = array(
        'DT_RowId' => 'row_' . $row->id,
        'reseller' => $reseller,
        'favicon' => $favicon,
        'name' => $row->name,
        'plan' => $plan,
        'email' => $row->email,
        'role_name' => $row->role,
        'role' => trans('global.roles.' . $row->role),
        'metatag' => $row->metatag,
        'admin'=>$admin_name,
        'logins' => $row->logins,
        'last_login' => $last_login,
        'trial_ends_at' => $trial_ends_at,
        'expires' => $expires,
        'active' => $row->active,
        'created_at' => $row->created_at->timezone(\Auth::user()->timezone)->format('Y-m-d H:i:s'),
        'sl' => Core\Secure::array2string(array('user_id' => $row->id)),
        'undeletable' => $undeletable
      );
    }

    $response = array(
      'draw' => $draw,
      'recordsTotal' => $recordsTotal,
      'recordsFiltered' => $recordsFiltered,
      'data' => $data
    );

    echo json_encode($response);
  }

  /**
   * Get user data
   */
	public function getResellersAdmins($reseller_id,Request $request)
    {

	    if (\Gate::allows('owner-management')) {
	        $admins=\App\User::where('reseller_id',$reseller_id)->where('role','=','admin')->orderBy('name')->get();
	    } else {
	        $resellers = null;
	        $reseller_id = Core\Reseller::get()->id;
	        $admins=\App\User::where('reseller_id',$reseller_id)->where('role','=','admin')->orderBy('name')->get();
	    }

	    $data=array();

	    if(count($admins)>0)
	    {
	        foreach($admins as $admin)
	        {
		        $data[] = array('id' =>$admin->id,'name'=>$admin->name);
	        }
	    }

	    $admin_id=0;
	    $user_reseller_id=0;

	    if($user_id=$request->input('user_id', 0))
	    {
	        $user = \App\User::where('id', $user_id)->first();

	        $admin_id=$user->admin_id;
	        $user_reseller_id=$user->reseller_id;
	    }

	    $response = array(
	      'success' => true,
	      'info' => array('data'=>$data,'admin_id'=>$admin_id,'user_reseller_id'=>$user_reseller_id)
	    );

	    echo json_encode($response);

    }
}