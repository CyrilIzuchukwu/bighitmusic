<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Existing codes were stored with password hashes, which cannot be
        // compared for uniqueness. Convert them to keyed, deterministic hashes.
        $seen = [];
        DB::table('signing_documents')->select('id', 'code_ciphertext')->orderBy('id')->chunk(500, function ($documents) use (&$seen) {
            foreach ($documents as $document) {
                $code = Crypt::decryptString($document->code_ciphertext);
                $digest = hash_hmac('sha256', 'signing-code:' . $code, config('app.key'));
                if (isset($seen[$digest])) {
                    throw new RuntimeException('Duplicate signing codes exist. Resolve them before running this migration.');
                }
                $seen[$digest] = true;
            }
        });

        DB::transaction(function () {
            DB::table('signing_documents')->select('id', 'code_ciphertext')->orderBy('id')->chunk(500, function ($documents) {
                foreach ($documents as $document) {
                    $code = Crypt::decryptString($document->code_ciphertext);
                    DB::table('signing_documents')->where('id', $document->id)->update([
                        'code_hash' => hash_hmac('sha256', 'signing-code:' . $code, config('app.key')),
                    ]);
                }
            });
        });

        Schema::table('signing_documents', function (Blueprint $table) {
            $table->unique('code_hash');
        });
    }

    public function down(): void
    {
        Schema::table('signing_documents', function (Blueprint $table) {
            $table->dropUnique(['code_hash']);
        });
    }
};
