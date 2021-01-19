<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestSendMailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_send_mails', function (Blueprint $table) {
            $table->id();
            $table->text('subject')->comment('Tiêu đề');
            $table->text('content')->comment('Nội dung mail');
            $table->string('from_email', 255)->comment('Gửi từ email');
            $table->string('from_name', 255)->nullable();
            $table->text('to_emails')->comment('Danh sách email');
            $table->text('cc')->nullable()->comment('CC đến những ai để nhận phản hổi');
            $table->text('attach_files')->nullable()->comment('Danh sách file đính kèm');
            $table->datetime('send_at')->index()->comment('Thời gian cho phép gửi mail');
            $table->text('email_fails')->nullable()->comment('Các email không gửi được');
            $table->integer('status')->comment('Trạng thái(1:Chưa xử lý,2:Đang xử lý,3:Đã xử lý)');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('request_send_mails');
    }
}
