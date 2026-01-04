package com.haida.hubapp.data.local

import androidx.room.Database
import androidx.room.RoomDatabase

@Database(
    entities = [OutboxEntity::class, SyncCursorEntity::class, SyncChangeEntity::class],
    version = 2,
    exportSchema = false
)
abstract class AppDatabase : RoomDatabase() {
    abstract fun outboxDao(): OutboxDao
    abstract fun syncCursorDao(): SyncCursorDao
    abstract fun syncChangeDao(): SyncChangeDao
}
