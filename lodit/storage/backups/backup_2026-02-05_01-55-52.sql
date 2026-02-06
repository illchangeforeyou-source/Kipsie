-- SQLite Database Dump
-- Generated: 2026-02-05T01:55:52+00:00

CREATE TABLE "app_settings" ("id" integer primary key autoincrement not null, "setting_key" varchar not null, "setting_value" text, "created_at" datetime, "updated_at" datetime);

CREATE TABLE "cache" ("key" varchar not null, "value" text not null, "expiration" integer not null, primary key ("key"));

CREATE TABLE "cache_locks" ("key" varchar not null, "owner" varchar not null, "expiration" integer not null, primary key ("key"));

CREATE TABLE "categories" ("id" integer primary key autoincrement not null, "name" varchar not null, "slug" varchar not null, "description" text, "parent_id" integer, "created_at" datetime, "updated_at" datetime, foreign key("parent_id") references "categories"("id") on delete cascade);

CREATE TABLE "consultations" ("id" integer primary key autoincrement not null, "user_id" integer not null, "consultant_id" integer, "question" text not null, "response" text, "status" varchar check ("status" in ('pending', 'answered', 'closed')) not null default 'pending', "medicine_id" integer, "answered_at" datetime, "created_at" datetime, "updated_at" datetime, foreign key("user_id") references "login"("id") on delete cascade, foreign key("consultant_id") references "login"("id") on delete set null, foreign key("medicine_id") references "medicines"("id") on delete set null);

CREATE TABLE "dok" ("id" integer primary key autoincrement not null, "nama" varchar not null, "foto" varchar not null, "created_at" datetime, "updated_at" datetime);

CREATE TABLE "failed_jobs" ("id" integer primary key autoincrement not null, "uuid" varchar not null, "connection" text not null, "queue" text not null, "payload" text not null, "exception" text not null, "failed_at" datetime not null default CURRENT_TIMESTAMP);

CREATE TABLE "job_batches" ("id" varchar not null, "name" varchar not null, "total_jobs" integer not null, "pending_jobs" integer not null, "failed_jobs" integer not null, "failed_job_ids" text not null, "options" text, "cancelled_at" integer, "created_at" integer not null, "finished_at" integer, primary key ("id"));

CREATE TABLE "jobs" ("id" integer primary key autoincrement not null, "queue" varchar not null, "payload" text not null, "attempts" integer not null, "reserved_at" integer, "available_at" integer not null, "created_at" integer not null);

CREATE TABLE "kli" ("id" integer primary key autoincrement not null, "nama" varchar not null, "no_hp" varchar not null, "alamat" varchar not null, "foto" varchar, "created_at" datetime, "updated_at" datetime);

CREATE TABLE "level" ("lvlnumber" integer primary key autoincrement not null, "beingas" varchar not null);

CREATE TABLE "login" ("id" integer primary key autoincrement not null, "username" varchar not null, "email" varchar, "password" varchar not null, "name" varchar, "level" integer not null default '4', "phone" varchar, "profile_picture" text, "created_at" datetime not null default CURRENT_TIMESTAMP, "updated_at" datetime not null default CURRENT_TIMESTAMP, "hidden" tinyint(1) not null default '0');

CREATE TABLE "medicines" ("id" integer primary key autoincrement not null, "name" varchar not null, "price" numeric not null, "stock" integer not null, "created_at" datetime, "updated_at" datetime, "image" varchar, "description" text, "images" text, "category_id" integer, "age_restriction" varchar, "expiry_date" date, foreign key("category_id") references "categories"("id") on delete set null);

CREATE TABLE "migrations" ("id" integer primary key autoincrement not null, "migration" varchar not null, "batch" integer not null);

CREATE TABLE "notifications" ("id" integer primary key autoincrement not null, "user_id" integer, "title" varchar not null, "message" text not null, "type" varchar not null default 'info', "order_id" integer, "read" tinyint(1) not null default '0', "read_at" datetime, "created_at" datetime, "updated_at" datetime, foreign key("user_id") references "login"("id") on delete cascade, foreign key("order_id") references "orders"("id") on delete cascade);

CREATE TABLE "orders" ("id" integer primary key autoincrement not null, "customer_name" varchar, "items" text, "total" numeric not null default '0', "created_at" datetime, "updated_at" datetime, "status" varchar not null default 'pending', "delivery_status" varchar check ("delivery_status" in ('pending', 'processing', 'shipped', 'delivered', 'cancelled')) not null default 'pending', "shipped_at" datetime, "delivered_at" datetime, "delivery_notes" text);

CREATE TABLE "password_reset_tokens" ("email" varchar not null, "token" varchar not null, "created_at" datetime, primary key ("email"));

CREATE TABLE "payment_confirmations" ("id" integer primary key autoincrement not null, "order_id" integer not null, "user_id" integer not null, "cashier_id" integer, "amount" numeric not null, "payment_method" varchar not null, "status" varchar check ("status" in ('pending', 'confirmed', 'rejected')) not null default 'pending', "notes" text, "confirmed_at" datetime, "created_at" datetime, "updated_at" datetime, foreign key("order_id") references "orders"("id") on delete cascade, foreign key("user_id") references "login"("id") on delete cascade, foreign key("cashier_id") references "login"("id") on delete set null);

CREATE TABLE "pending_admin_changes" ("id" integer primary key autoincrement not null, "action_type" varchar not null, "target_user_id" integer not null, "admin_id" integer not null, "old_data" text, "new_data" text, "status" varchar not null default 'pending', "approved_by" integer, "approved_at" datetime, "created_at" datetime, "updated_at" datetime);

CREATE TABLE "prescriptions" ("id" integer primary key autoincrement not null, "order_id" integer not null, "user_id" integer not null, "medicine_id" integer not null, "pharmacist_id" integer, "file_path" varchar not null, "status" varchar check ("status" in ('pending', 'approved', 'rejected')) not null default 'pending', "pharmacist_notes" text, "validated_at" datetime, "created_at" datetime, "updated_at" datetime, foreign key("order_id") references "orders"("id") on delete cascade, foreign key("user_id") references "login"("id") on delete cascade, foreign key("medicine_id") references "medicines"("id") on delete cascade, foreign key("pharmacist_id") references "login"("id") on delete set null);

CREATE TABLE "sessions" ("id" varchar not null, "user_id" integer, "ip_address" varchar, "user_agent" text, "payload" text not null, "last_activity" integer not null, primary key ("id"));

CREATE TABLE "transaction_deletions" ("id" integer primary key autoincrement not null, "transaction_id" integer not null, "deleted_by_user_id" integer not null, "action" varchar not null default 'soft_delete', "ip_address" varchar, "user_agent" varchar, "transaction_data" text, "deleted_at" datetime not null, "created_at" datetime, "updated_at" datetime);

CREATE TABLE "transactions" ("id" integer primary key autoincrement not null, "type" varchar, "description" text, "amount" numeric not null default '0', "balance" numeric not null default '0', "reference_id" varchar, "created_at" datetime, "updated_at" datetime, "medicine_id" integer, "quantity" integer, "deleted_at" datetime);

CREATE TABLE "user_permissions" ("id" integer primary key autoincrement not null, "user_id" integer not null, "permission_key" varchar not null, "can_access" tinyint(1) not null default '1', "notes" text, "created_at" datetime, "updated_at" datetime, foreign key("user_id") references "login"("id") on delete cascade);

CREATE TABLE "user_preferences" ("id" integer primary key autoincrement not null, "user_id" integer not null, "theme" varchar not null default 'light', "notifications_enabled" tinyint(1) not null default '1', "notification_sound" varchar not null default 'default', "created_at" datetime, "updated_at" datetime, foreign key("user_id") references "login"("id") on delete cascade);

CREATE TABLE "users" ("id" integer primary key autoincrement not null, "name" varchar not null, "email" varchar not null, "email_verified_at" datetime, "password" varchar not null, "remember_token" varchar, "created_at" datetime, "updated_at" datetime, "profile_picture" varchar, "phone" varchar);

CREATE UNIQUE INDEX "app_settings_setting_key_unique" on "app_settings" ("setting_key");

CREATE UNIQUE INDEX "categories_slug_unique" on "categories" ("slug");

CREATE UNIQUE INDEX "failed_jobs_uuid_unique" on "failed_jobs" ("uuid");

CREATE INDEX "jobs_queue_index" on "jobs" ("queue");

CREATE UNIQUE INDEX "level_beingas_unique" on "level" ("beingas");

CREATE UNIQUE INDEX "login_email_unique" on "login" ("email");

CREATE UNIQUE INDEX "login_username_unique" on "login" ("username");

CREATE INDEX "sessions_last_activity_index" on "sessions" ("last_activity");

CREATE INDEX "sessions_user_id_index" on "sessions" ("user_id");

CREATE UNIQUE INDEX "user_permissions_user_id_permission_key_unique" on "user_permissions" ("user_id", "permission_key");

CREATE UNIQUE INDEX "user_preferences_user_id_unique" on "user_preferences" ("user_id");

CREATE UNIQUE INDEX "users_email_unique" on "users" ("email");

INSERT INTO "level" ("lvlnumber", "beingas") VALUES (1, 'Super Admin');
INSERT INTO "level" ("lvlnumber", "beingas") VALUES (2, 'Admin');
INSERT INTO "level" ("lvlnumber", "beingas") VALUES (3, 'Doctor');
INSERT INTO "level" ("lvlnumber", "beingas") VALUES (4, 'User');
INSERT INTO "level" ("lvlnumber", "beingas") VALUES (5, 'Cashier');
INSERT INTO "level" ("lvlnumber", "beingas") VALUES (6, 'User Manager');
INSERT INTO "level" ("lvlnumber", "beingas") VALUES (7, 'Pharmacist');

INSERT INTO "app_settings" ("id", "setting_key", "setting_value", "created_at", "updated_at") VALUES (1, 'app_name', 'KIPS', '2026-01-31 08:46:31', '2026-01-31 08:46:31');
INSERT INTO "app_settings" ("id", "setting_key", "setting_value", "created_at", "updated_at") VALUES (2, 'app_logo_path', 'foto/logo.jpg', '2026-01-31 08:46:31', '2026-01-31 08:46:31');

INSERT INTO "migrations" ("id", "migration", "batch") VALUES (1, '2026_01_31_fix_tablespace', 1);

INSERT INTO "login" ("id", "username", "email", "password", "name", "level", "phone", "profile_picture", "created_at", "updated_at", "hidden") VALUES (1, 'y', NULL, '$2y$10$6CiIMEvrsS2UAVK00QEivOzShrH424kHSnvhLprsSUtznImBAPR9S', NULL, 4, NULL, NULL, '2026-01-31 09:16:51', '2026-01-31 09:16:51', 0);
INSERT INTO "login" ("id", "username", "email", "password", "name", "level", "phone", "profile_picture", "created_at", "updated_at", "hidden") VALUES (2, 'admin', NULL, '$2y$10$ZuQoJlOMEMPp9a2jOYAgQuwhf2NBsYpgyFb4pfpHk.wEEWlU0OgsK', NULL, 4, NULL, NULL, '2026-01-31 09:16:51', '2026-01-31 09:16:51', 0);

