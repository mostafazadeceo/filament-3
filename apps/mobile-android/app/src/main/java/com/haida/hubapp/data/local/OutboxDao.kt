package com.haida.hubapp.data.local

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query

@Dao
interface OutboxDao {
    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun upsert(item: OutboxEntity)

    @Query("SELECT * FROM app_outbox WHERE status IN ('pending','failed') ORDER BY updatedAt ASC")
    suspend fun pending(): List<OutboxEntity>

    @Query("UPDATE app_outbox SET status = :status, retries = :retries, updatedAt = :updatedAt WHERE id = :id")
    suspend fun updateStatus(id: String, status: String, retries: Int, updatedAt: String)
}
