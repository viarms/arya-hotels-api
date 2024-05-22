<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->string('time');
            $table->string('email');
            $table->boolean('is_done')->default(false);
            $table->timestamps();
        });

        Schema::create('destinations', function (Blueprint $table) {
            $table->id();
            $table->boolean('show')->default(false);
            $table->string('slug')->unique();

            $table->json('general');
            $table->json('hero');
            $table->json('intro');
            $table->json('hotels');
            $table->json('about');
            $table->json('benefits');
            $table->json('gallery');
            $table->json('services');
            $table->json('faq');
            $table->json('catalogue');
            $table->json('adventures');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->boolean('show')->default(false);
            $table->string('slug')->unique();
            $table->unsignedBigInteger('destination_id');

            $table->json('general');
            $table->json('hero');
            $table->json('overview');
            $table->json('location');
            $table->json('video');
            $table->json('suitesAndRooms');
            $table->json('gallery');
            $table->json('activities');
            $table->json('dining');
            $table->json('fitness');
            $table->json('wedding');
            $table->json('offers');
            $table->json('discover');
            $table->json('complexGallery');
            $table->json('awards');
            $table->json('faq');
            $table->json('catalogue');
            $table->json('map');

            $table->foreign('destination_id')->references('id')->on('destinations');

            $table->timestamps();
            $table->softDeletes();
        });

        /* Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->boolean('show')->default(false);
            $table->string('slug'); //->unique();

            $table->json('general');
            $table->json('hero');

            $table->json('gallery');
            $table->json('benefits');
            $table->json('catalogue');

            $table->timestamps();
            $table->softDeletes();
        }); */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
