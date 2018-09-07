<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHoursTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
        CREATE TRIGGER BI_hours BEFORE INSERT ON `hours` FOR EACH ROW
            BEGIN
                SET NEW.total = (TIMESTAMPDIFF(MINUTE, NEW.initialDate, NEW.finalDate) / 60);
            END
        ');
        DB::unprepared('
        CREATE TRIGGER BU_hours BEFORE UPDATE ON `hours` FOR EACH ROW
            BEGIN
                SET NEW.total = (TIMESTAMPDIFF(MINUTE, NEW.initialDate, NEW.finalDate) / 60);
            END
        ');
    }
// DATEDIFF(hour, NEW.initialDate, finalDate)
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP  TRIGGER IF EXISTS `BI_hours`');
        DB::unprepared('DROP  TRIGGER IF EXISTS `BU_hours`');
    }
}
