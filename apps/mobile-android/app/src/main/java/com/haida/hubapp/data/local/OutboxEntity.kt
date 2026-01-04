package com.haida.hubapp.data.local

import androidx.room.Entity
import androidx.room.PrimaryKey

@Entity(tableName = "app_outbox")
data class OutboxEntity(
    @PrimaryKey val id: String,
    val module: String,
    val action: String,
    val payload: String,
    val status: String,
    val retries: Int,
    val idempotencyKey: String,
    val updatedAt: String
)
