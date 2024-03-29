<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //mysqlimport  --ignore-lines=0 --fields-terminated-by=, --columns='zoom_id, last_name, first_name, position, phone, email, lead_street, lead_city, lead_state, lead_zip, country, company_name, company_url, company_phone, company_address, company_city, company_state, company_zip, company_country, revenue, revenue_range, employees, employees_range, sic1, sic2, naics1, naics2' -h cosstdbms-cluster-1.cluster-cylt9f2k0zbl.us-east-1.rds.amazonaws.com --local -u cosst -pdb.cosst.1517 jobtarget /home/centos/leads.csv
        //LOAD DATA LOCAL INFILE "/var/lib/mysql-files/july06.csv" into table leads FIELDS TERMINATED BY ',' ENCLOSED BY '"' (zoom_id, last_name, first_name, position, phone, email, lead_street, lead_city, lead_state, lead_zip, country, company_name, company_url, company_phone, Company Street address, company_city, company_state, company_zip, company_country, revenue, revenue_range, employees, employees_range, sic1, sic2, naics1, naics2);
        Schema::create('leads', function (Blueprint $table) {
            $table->increments('id');
            $table->string('zoom_id')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('position')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('lead_city')->nullable();
            $table->string('lead_state')->nullable();
            $table->string('lead_zip')->nullable();
            $table->string('country')->nullable();
            $table->integer('zoom_company_id')->nullable();
            $table->string('company_name')->nullable();
            $table->string('company_url')->nullable();
            $table->string('company_phone')->nullable();
            $table->string('company_address')->nullable();
            $table->string('company_city')->nullable();
            $table->string('company_state')->nullable();
            $table->string('company_zip')->nullable();
            $table->string('company_country')->nullable();
            $table->string('revenue')->nullable();
            $table->string('revenue_range')->nullable();
            $table->integer('employees')->nullable();
            $table->string('employees_range')->nullable();
            $table->integer('sic1')->nullable();
            $table->integer('sic2')->nullable();
            $table->integer('naics1')->nullable();
            $table->integer('naics2')->nullable();
            $table->integer('status')->default(1);//1 = New opportunity, 2=> Exixting Opportunity
            $table->integer('type')->default(1);//1 = Zoom source, 2 = Leads outreach, 3 = Account outreach
            $table->integer('setup')->default(0)->nullable();
            $table->timestamps();
            $table->index(['zoom_id', 'created_at']);
         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leads');
    }
}
