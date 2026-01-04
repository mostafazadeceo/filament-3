package com.haida.hubapp.data.local

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query

@Dao
interface SyncChangeDao {
    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun upsert(change: SyncChangeEntity)

    @Query("DELETE FROM app_sync_changes WHERE module = :module AND entity = :entity AND id = :id")
    suspend fun delete(module: String, entity: String, id: String)
}
