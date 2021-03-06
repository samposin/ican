<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailCampaignsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('email_campaigns', function ($table) {
      $table->bigIncrements('id');
      $table->integer('user_id')->unsigned()->nullable();
      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
      $table->bigInteger('funnel_id')->unsigned()->nullable();
      $table->foreign('funnel_id')->references('id')->on('funnels')->onDelete('cascade');
      $table->string('type', 32)->nullable();
      $table->boolean('active')->default(true);
      $table->string('name', 64)->nullable();
      $table->string('language', 5)->default('en');
      $table->string('timezone', 32)->default('UTC');
      $table->integer('opens')->unsigned()->default(0);
      $table->integer('clicks')->unsigned()->default(0);
      $table->integer('drops')->unsigned()->default(0);
      $table->integer('bounces')->unsigned()->default(0);
      $table->integer('sent')->unsigned()->default(0);
      $table->string('mail_from', 64)->nullable();
      $table->string('mail_from_name', 64)->nullable();
      $table->json('meta')->nullable();
      $table->timestamps();
    });

    Schema::create('emails', function ($table) {
      $table->bigIncrements('id');
      $table->bigInteger('parent_id')->unsigned()->nullable();
      $table->bigInteger('_lft')->unsigned()->default(0);
      $table->bigInteger('_rgt')->unsigned()->default(0);
      $table->integer('user_id')->unsigned();
      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
      $table->bigInteger('email_campaign_id')->unsigned();
      $table->foreign('email_campaign_id')->references('id')->on('email_campaigns')->onDelete('cascade');
      $table->string('template', 48)->nullable();
      $table->string('local_domain', 64)->nullable();
      $table->tinyInteger('variant')->unsigned()->default(1);
      $table->boolean('active')->default(true);
      $table->string('name', 64)->nullable();
      $table->string('subject', 200)->nullable();
      $table->integer('opens')->unsigned()->default(0);
      $table->integer('clicks')->unsigned()->default(0);
      $table->integer('drops')->unsigned()->default(0);
      $table->integer('bounces')->unsigned()->default(0);
      $table->integer('sent')->unsigned()->default(0);
      $table->dateTime('last_sent')->nullable();
      $table->integer('tests')->unsigned()->default(0);
      $table->dateTime('last_test')->nullable();
      $table->text('last_test_email')->nullable();
      $table->integer('delay_hours')->nullable();
      $table->integer('delay_months')->nullable();
      $table->time('send_time')->nullable();
      $table->dateTime('scheduled_at')->nullable();
      $table->boolean('only_send_when_opened')->default(false);
      $table->boolean('only_send_when_clicked')->default(false);
      $table->json('meta')->nullable();
      $table->timestamps();
    });

    Schema::create('email_forms', function(Blueprint $table)
    {
      $table->bigIncrements('id');
      $table->bigInteger('email_id')->unsigned();
      $table->foreign('email_id')->references('id')->on('emails')->onDelete('cascade');
      $table->bigInteger('form_id')->unsigned();
      $table->foreign('form_id')->references('id')->on('forms')->onDelete('cascade');
    });

    Schema::create('email_mailings', function(Blueprint $table) {
      $table->bigIncrements('id');
      $table->bigInteger('email_campaign_id')->unsigned();
      $table->foreign('email_campaign_id')->references('id')->on('email_campaigns')->onDelete('cascade');
      $table->bigInteger('email_id')->unsigned();
      $table->foreign('email_id')->references('id')->on('emails')->onDelete('cascade');
      $table->integer('recepients')->unsigned()->default(1);
      $table->integer('clicks')->unsigned()->default(0);
      $table->integer('opens')->unsigned()->default(0);
      $table->dateTime('schedule')->nullable();
      $table->json('meta')->nullable();
      $table->dateTime('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
    });

    Schema::create('email_sequence', function(Blueprint $table) {
      $table->bigIncrements('id');
      $table->bigInteger('email_id')->unsigned();
      $table->foreign('email_id')->references('id')->on('emails')->onDelete('cascade');
      $table->string('email', 96);
      $table->integer('clicks')->unsigned()->default(0);
      $table->integer('opens')->unsigned()->default(0);
      $table->json('meta')->nullable();
      $table->dateTime('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
    });

    Schema::create('email_events', function(Blueprint $table) {
      $table->bigIncrements('id');
      $table->bigInteger('form_id')->unsigned();
      $table->foreign('form_id')->references('id')->on('forms')->onDelete('cascade');
      $table->bigInteger('email_id')->unsigned();
      $table->foreign('email_id')->references('id')->on('emails')->onDelete('cascade');
      $table->bigInteger('entry_id')->unsigned();
      $table->text('message_id');
      $table->string('event', 64);
      $table->text('link')->nullable();
      $table->string('recipient', 96)->nullable();
      $table->json('meta')->nullable();
      $table->dateTime('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
    });

  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::drop('email_events');
    Schema::drop('email_sequence');
    Schema::drop('email_mailings');
    Schema::drop('email_forms');
    Schema::drop('emails');
    Schema::drop('email_campaigns');
  }
}
