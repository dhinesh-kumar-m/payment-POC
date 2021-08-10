<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateXeroCharOfAccount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('xero_char_of_account', function (Blueprint $table) {
            $table->id();
            $table->integer('code')->nullable();
            $table->string('name')->nullable();
            $table->string('type')->nullable();
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(1);
            
            $table->timestamps();
        });
        

        DB::table('xero_char_of_account')->insert(
            array([
                'code' => 999,
                'name' => "Alba dms 1",
                'type' => "REVENUE",
                'description' => "Test1",
            ],
            [
                'code' => 998,
                'name' => "Alba dms 2",
                'type' => "LIABILITY",
                'description' => "Test1",
            ],
            [
                'code' => 997,
                'name' => "Alba dms 3",
                'type' => "EXPENSE",
                'description' => "Test1",
            ],
            [
                'code' => 996,
                'name' => "Alba dms 4",
                'type' => "EXPENSE",
                'description' => "Test1",
            ],
            [
                'code' => 995,
                'name' => "Alba dms 5",
                'type' => "LIABILITY",
                'description' => "Test1",
            ],
            [
                'code' => 994,
                'name' => "Alba dms 6",
                'type' => "REVENUE",
                'description' => "Test1",
            ]
            )
        );
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('xero_char_of_account');
    }
}
