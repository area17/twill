<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTwillDefaultFeaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $twillFeaturesTable = config('twill.features_table', 'twill_features');

        if (!Schema::hasTable($twillFeaturesTable)) {
            Schema::create($twillFeaturesTable, function (Blueprint $table) use ($twillFeaturesTable) {
                $table->bigIncrements('id');
                $table->string('featured_id', 36);
                $table->string('featured_type', 255);
                $table->string('bucket_key')->index();
                $table->integer('position')->unsigned();
                $table->boolean('starred')->default(false);
                $table->timestamps();
                $table->unique(['featured_id', 'featured_type', 'bucket_key'], $twillFeaturesTable . '_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $twillFeaturesTable = config('twill.features_table', 'twill_features');

        Schema::dropIfExists($twillFeaturesTable);
    }
}
