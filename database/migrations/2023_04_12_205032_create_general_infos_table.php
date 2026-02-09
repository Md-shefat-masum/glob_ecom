<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeneralInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_infos', function (Blueprint $table) {
            $table->id();
            $table->string('logo')->nullable();
            $table->string('logo_dark')->nullable();
            $table->string('fav_icon')->nullable();
            $table->string('tab_title')->nullable();
            $table->string('company_name')->nullable();
            $table->longText('short_description')->nullable();
            $table->string('contact')->nullable();
            $table->string('email')->nullable();
            $table->longText('address')->nullable();
            $table->longText('google_map_link')->nullable();
            $table->string('footer_copyright_text')->nullable();
            $table->string('payment_banner')->nullable();
            $table->string('play_store_link')->nullable();
            $table->string('app_store_link')->nullable();

            $table->longText('custom_css')->nullable();
            $table->longText('custom_js')->nullable();
            $table->longText('header_script')->nullable();
            $table->longText('footer_script')->nullable();

            // project color
            $table->string('primary_color')->nullable();
            $table->string('secondary_color')->nullable();
            $table->string('tertiary_color')->nullable();
            $table->string('title_color')->nullable();
            $table->string('paragraph_color')->nullable();
            $table->string('border_color')->nullable();

            $table->string('meta_title')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->longText('meta_description')->nullable();

            $table->string('meta_og_title')->nullable();
            $table->string('meta_og_image')->nullable();
            $table->longText('meta_og_description')->nullable();

            // social media links
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('twitter')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('youtube')->nullable();
            $table->string('messenger')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('telegram')->nullable();
            $table->string('tiktok')->nullable();
            $table->string('pinterest')->nullable();
            $table->string('viber')->nullable();

            // google analytics
            $table->tinyInteger('google_analytic_status')->default(0)->comment("1=>Active; 0=>Inactive");
            $table->string('google_analytic_tracking_id')->nullable();

            // google tag manager
            $table->tinyInteger('google_tag_manager_status')->default(0)->comment("1=>Active; 0=>Inactive");
            $table->string('google_tag_manager_id')->nullable();

            // facebook pixel
            $table->tinyInteger('fb_pixel_status')->default(0)->comment("1=>Active; 0=>Inactive");
            $table->string('fb_pixel_app_id')->nullable();

            // Tawk.to
            $table->tinyInteger('tawk_chat_status')->default(0)->comment("1=>Active; 0=>Inactive");
            $table->string('tawk_chat_link')->nullable();

            // Crisp Chat
            $table->tinyInteger('crisp_chat_status')->default(0)->comment("1=>Active; 0=>Inactive");
            $table->string('crisp_website_id')->nullable();

            $table->longText('about_us')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('general_infos');
    }
}
