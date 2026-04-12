<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("
            CREATE TABLE audit_logs (
                id BIGSERIAL,
                event_id UUID NOT NULL,
                category VARCHAR(50) NOT NULL,
                action VARCHAR(100) NOT NULL,
                resource_type VARCHAR(100),
                resource_id VARCHAR(100),
                user_id BIGINT,
                user_email VARCHAR(255),
                changes JSONB,
                metadata JSONB,
                ip_address INET,
                user_agent TEXT,
                occurred_at TIMESTAMP NOT NULL,
                created_at TIMESTAMP,
                updated_at TIMESTAMP
            ) PARTITION BY RANGE (occurred_at);
        ");

        DB::statement("
            CREATE INDEX idx_audit_user ON audit_logs(user_id);
        ");

        DB::statement("
            CREATE INDEX idx_audit_resource
            ON audit_logs(resource_type, resource_id);
        ");

        DB::statement("
            CREATE INDEX idx_audit_date
            ON audit_logs(occurred_at);
        ");

        DB::statement("
    CREATE OR REPLACE FUNCTION prevent_audit_modifications()
    RETURNS trigger AS $$
    BEGIN
        RAISE EXCEPTION 'Audit logs are append-only';
    END;
    $$ LANGUAGE plpgsql;
");

        DB::statement("
    CREATE TRIGGER no_update_delete
    BEFORE UPDATE OR DELETE ON audit_logs
    FOR EACH ROW
    EXECUTE FUNCTION prevent_audit_modifications();
");

    }

    public function down(): void
    {
        DB::statement("DROP TABLE IF EXISTS audit_logs CASCADE;");
    }
};

