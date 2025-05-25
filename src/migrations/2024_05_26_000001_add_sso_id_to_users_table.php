<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * AddSsoIdToUsersTable Migration
 * 
 * This migration adds an SSO identifier column to the users table.
 * The SSO ID is used to link local user accounts with their corresponding
 * accounts on the SSO server.
 */
return new class extends Migration
{
    /**
     * Run the migration
     * 
     * Adds an sso_id column to the users table with the following properties:
     * - Nullable (some users might not use SSO)
     * - Unique (each SSO ID can only be linked to one user)
     * - Added after the id column for better readability
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('sso_id')->nullable()->unique()->after('id');
        });
    }

    /**
     * Reverse the migration
     * 
     * Removes the sso_id column from the users table
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('sso_id');
        });
    }
};
