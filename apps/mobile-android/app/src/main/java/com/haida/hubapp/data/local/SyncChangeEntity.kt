package com.haida.hubapp.data.local

import androidx.room.Entity

@Entity(tableName = "app_sync_changes", primaryKeys = ["module", "entity", "id"])
data class SyncChangeEntity(
    val module: String,
    val entity: String,
    val id: String,
    val action: String,
    val payload: String,
    val updatedAt: String
)
