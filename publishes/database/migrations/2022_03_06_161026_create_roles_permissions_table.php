<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('users', 'is_admin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean("is_admin")->nullable()->default(false);
            });
        }
        if (!Schema::hasColumn('users', 'is_super_admin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean("is_super_admin")->nullable()->default(false);
            });
        }

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string("name")->nullable();
            $table->string("label")->nullable();
            $table->timestamps();
        });
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string("name")->nullable();
            $table->string("label")->nullable();
            $table->integer("parent_id")->nullable()->default(0);
            $table->string("type")->nullable()->default("page");
            $table->timestamps();
        });

        Schema::create('roles_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('permission_id');

            //FOREIGN KEY
            //   $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            //  $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');

            //PRIMARY KEYS
            $table->primary(['role_id', 'permission_id']);
        });

        Schema::create('users_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('permission_id');

            //FOREIGN KEY
            //  $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            //  $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');

            //PRIMARY KEYS
            $table->primary(['user_id', 'permission_id']);
        });

        Schema::create('users_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('role_id');

            //FOREIGN KEY
            //    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            //   $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');

            //PRIMARY KEYS
            $table->primary(['user_id', 'role_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles_permissions');
        Schema::dropIfExists('users_permissions');
        Schema::dropIfExists('users_roles');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permissions');
    }
};
