<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('signing_documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('recipient_name');
            $table->string('recipient_email');
            $table->string('subject');
            $table->text('body');
            $table->string('original_name');
            $table->string('file_path');
            $table->string('signed_file_path')->nullable();
            $table->string('token_hash', 64)->unique();
            $table->text('token_ciphertext');
            $table->string('code_hash');
            $table->text('code_ciphertext');
            $table->unsignedInteger('max_opens')->default(1);
            $table->unsignedInteger('opens')->default(0);
            $table->unsignedInteger('access_version')->default(1);
            $table->unsignedInteger('signature_x')->default(60);
            $table->unsignedInteger('signature_y')->default(80);
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();
        });

        Schema::create('signing_document_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('signing_document_id')->constrained()->cascadeOnDelete();
            $table->string('event');
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signing_document_events');
        Schema::dropIfExists('signing_documents');
    }
};
