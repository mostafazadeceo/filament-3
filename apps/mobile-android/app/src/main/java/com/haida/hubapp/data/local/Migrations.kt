package com.haida.hubapp.data.local

import androidx.room.migration.Migration
import androidx.sqlite.db.SupportSQLiteDatabase

val MIGRATION_1_2 = object : Migration(1, 2) {
    override fun migrate(db: SupportSQLiteDatabase) {
        db.execSQL(
            """
            CREATE TABLE IF NOT EXISTS app_sync_changes (
                module TEXT NOT NULL,
                entity TEXT NOT NULL,
                id TEXT NOT NULL,
                action TEXT NOT NULL,
                payload TEXT NOT NULL,
                updatedAt TEXT NOT NULL,
                PRIMARY KEY(module, entity, id)
            )
            """.trimIndent()
        )
    }
}
