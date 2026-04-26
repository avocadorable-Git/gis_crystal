# Database Migrations

This folder tracks all database schema changes for gis_crystal.

## How to Set Up Database from Scratch

Run migration files **in order**:

```bash
mysql -u root -p < 001_create_tables.sql
```

## Rules

- Every schema change must have a new migration file: `002_add_xyz.sql`, `003_alter_xyz.sql`, etc.
- Never edit an already-committed migration file.
- Always commit migration files to GitHub alongside the feature code that needs them.
- Migration files are what allow anyone to clone the repo and reproduce the exact database.
