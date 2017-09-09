<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsChangesOneToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {

             $table->string('business_name', 100)->after('remember_token')->nullable();
             $table->string('manager_name', 100)->after('business_name')->nullable();
             $table->string('store_number', 100)->after('manager_name')->nullable();
             $table->string('address', 100)->after('store_number')->nullable();
             $table->string('business_type', 100)->after('address')->nullable();
             $table->string('phone2', 100)->after('business_type')->nullable();
             $table->string('phone3', 100)->after('phone2')->nullable();
             $table->string('email_2', 100)->after('phone3')->nullable();
             $table->string('email_3', 100)->after('email_2')->nullable();
             $table->string('website_1', 255)->after('email_3')->nullable();
             $table->string('website_2', 255)->after('website_1')->nullable();
             $table->string('facebook_url',255)->after('website_2')->nullable();
             $table->string('instagram_url',255)->after('facebook_url')->nullable();
             $table->string('linked_in_url',255)->after('instagram_url')->nullable();
             $table->string('youtube_url',255)->after('linked_in_url')->nullable();
             $table->string('metatag',255)->after('youtube_url')->nullable();
             $table->text('notes')->after('metatag')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
             $table->dropColumn('business_name');
             $table->dropColumn('manager_name');
             $table->dropColumn('store_number');
             $table->dropColumn('address');
             $table->dropColumn('business_type');
             $table->dropColumn('phone2');
             $table->dropColumn('phone3');
             $table->dropColumn('email_2');
             $table->dropColumn('email_3');
             $table->dropColumn('website_1');
             $table->dropColumn('website_2');
             $table->dropColumn('facebook_url');
             $table->dropColumn('instagram_url');
             $table->dropColumn('linked_in_url');
             $table->dropColumn('youtube_url');
             $table->dropColumn('metatag');
             $table->dropColumn('notes');
        });
    }
}
