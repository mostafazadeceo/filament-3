package com.haida.hubapp.data.local

import androidx.room.Entity
import androidx.room.PrimaryKey

@Entity(tableName = "app_sync_cursors")
data class SyncCursorEntity(
    @PrimaryKey val module: String,
    val cursor: String,
    val updatedAt: String
)
