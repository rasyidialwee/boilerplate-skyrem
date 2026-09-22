<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            if (! Schema::hasColumn('activity_log', 'attribute_changes')) {
                $table->json('attribute_changes')->nullable()->after('properties');
            }

            if (Schema::hasColumn('activity_log', 'batch_uuid')) {
                $table->dropColumn('batch_uuid');
            }
        });

        DB::table('activity_log')
            ->whereNotNull('properties')
            ->orderBy('id')
            ->chunkById(100, function ($rows): void {
                foreach ($rows as $row) {
                    $properties = json_decode($row->properties, true);

                    if (! is_array($properties)) {
                        continue;
                    }

                    $changes = array_intersect_key($properties, array_flip(['attributes', 'old']));
                    $remaining = array_diff_key($properties, array_flip(['attributes', 'old']));

                    DB::table('activity_log')->where('id', $row->id)->update([
                        'attribute_changes' => $changes === [] ? null : json_encode($changes),
                        'properties' => $remaining === [] ? null : json_encode($remaining),
                    ]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('activity_log')
            ->whereNotNull('attribute_changes')
            ->orderBy('id')
            ->chunkById(100, function ($rows): void {
                foreach ($rows as $row) {
                    $properties = json_decode($row->properties ?? '[]', true) ?: [];
                    $changes = json_decode($row->attribute_changes, true) ?: [];

                    DB::table('activity_log')->where('id', $row->id)->update([
                        'properties' => json_encode(array_merge($properties, $changes)),
                    ]);
                }
            });

        Schema::table('activity_log', function (Blueprint $table) {
            if (! Schema::hasColumn('activity_log', 'batch_uuid')) {
                $table->uuid('batch_uuid')->nullable()->after('properties');
            }

            if (Schema::hasColumn('activity_log', 'attribute_changes')) {
                $table->dropColumn('attribute_changes');
            }
        });
    }
};
