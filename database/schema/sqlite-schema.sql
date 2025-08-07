CREATE TABLE IF NOT EXISTS "migrations"(
  "id" integer primary key autoincrement not null,
  "migration" varchar not null,
  "batch" integer not null
);
CREATE TABLE IF NOT EXISTS "users"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "email" varchar not null,
  "email_verified_at" datetime,
  "password" varchar,
  "avatar_url" varchar,
  "remember_token" varchar,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "users_email_unique" on "users"("email");
CREATE TABLE IF NOT EXISTS "password_reset_tokens"(
  "email" varchar not null,
  "token" varchar not null,
  "created_at" datetime,
  primary key("email")
);
CREATE TABLE IF NOT EXISTS "sessions"(
  "id" varchar not null,
  "user_id" integer,
  "ip_address" varchar,
  "user_agent" text,
  "payload" text not null,
  "last_activity" integer not null,
  primary key("id")
);
CREATE INDEX "sessions_user_id_index" on "sessions"("user_id");
CREATE INDEX "sessions_last_activity_index" on "sessions"("last_activity");
CREATE TABLE IF NOT EXISTS "todos"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "content" varchar not null,
  "is_done" tinyint(1) not null default '0',
  "deadline" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  "visibility" varchar check("visibility" in('private', 'public')) not null default 'private',
  "is_edited" tinyint(1) not null default '0',
  "who_liked" varchar,
  foreign key("user_id") references "users"("id")
);

INSERT INTO migrations VALUES(5,'0001_01_01_000000_create_users_table',1);
INSERT INTO migrations VALUES(6,'2025_07_11_094830_create_todos_table',1);
INSERT INTO migrations VALUES(7,'2025_07_29_041137_add_timeline-columns_to_todos',2);
INSERT INTO migrations VALUES(8,'2025_08_04_065434_add_likes_count_columns_to_todos',3);
