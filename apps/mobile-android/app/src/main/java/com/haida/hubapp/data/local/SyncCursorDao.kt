package com.haida.hubapp.data.local

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query

@Dao
interface SyncCursorDao {
    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun upsert(cursor: SyncCursorEntity)

    @Query("SELECT * FROM app_sync_cursors WHERE module = :module LIMIT 1")
    suspend fun get(module: String): SyncCursorEntity?
}
