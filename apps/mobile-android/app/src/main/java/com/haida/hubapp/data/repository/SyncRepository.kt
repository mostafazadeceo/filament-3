package com.haida.hubapp.data.repository

import com.haida.hubapp.data.local.OutboxDao
import com.haida.hubapp.data.local.OutboxEntity
import com.haida.hubapp.data.local.SyncChangeDao
import com.haida.hubapp.data.local.SyncChangeEntity
import com.haida.hubapp.data.local.SyncCursorDao
import com.haida.hubapp.data.local.SyncCursorEntity
import com.haida.hubapp.data.remote.AppApiService
import com.haida.hubapp.data.remote.PushItem
import com.haida.hubapp.data.remote.PushRequest
import com.haida.hubapp.data.remote.SyncChange
import com.squareup.moshi.Moshi
import com.squareup.moshi.Types
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.withContext
import javax.inject.Inject
import javax.inject.Singleton

@Singleton
class SyncRepository @Inject constructor(
    private val outboxDao: OutboxDao,
    private val cursorDao: SyncCursorDao,
    private val changeDao: SyncChangeDao,
    private val api: AppApiService,
    private val moshi: Moshi
) {
    private val mapAdapter = moshi.adapter<Map<String, Any?>>(Types.newParameterizedType(Map::class.java, String::class.java, Any::class.java))

    suspend fun enqueue(module: String, action: String, payload: Map<String, Any?>, idempotencyKey: String) {
        val jsonPayload = mapAdapter.toJson(payload)
        val now = java.time.Instant.now().toString()
        outboxDao.upsert(
            OutboxEntity(
                id = idempotencyKey,
                module = module,
                action = action,
                payload = jsonPayload,
                status = "pending",
                retries = 0,
                idempotencyKey = idempotencyKey,
                updatedAt = now
            )
        )
    }

    suspend fun pushOutbox() = withContext(Dispatchers.IO) {
        val pending = outboxDao.pending()
        if (pending.isEmpty()) {
            return@withContext
        }

        val payload = pending.map { item ->
            val body = mapAdapter.fromJson(item.payload) ?: emptyMap()
            PushItem(
                id = item.id,
                module = item.module,
                action = item.action,
                payload = body,
                idempotency_key = item.idempotencyKey
            )
        }

        try {
            val response = api.push(PushRequest(payload))
            val now = java.time.Instant.now().toString()
            val processed = response.results.map { it.id }.toSet()
            response.results.forEach { result ->
                val status = if (result.status == "accepted") "completed" else "failed"
                val current = pending.firstOrNull { it.id == result.id }
                val retries = if (status == "failed") (current?.retries ?: 0) + 1 else (current?.retries ?: 0)
                outboxDao.updateStatus(result.id, status, retries, now)
            }
            pending.filter { it.id !in processed }.forEach { item ->
                outboxDao.updateStatus(item.id, "failed", item.retries + 1, now)
            }
        } catch (ex: Exception) {
            val now = java.time.Instant.now().toString()
            pending.forEach { item ->
                outboxDao.updateStatus(item.id, "failed", item.retries + 1, now)
            }
            throw ex
        }
    }

    suspend fun pullChanges() = withContext(Dispatchers.IO) {
        val cursor = cursorDao.get("global")?.cursor
        val response = api.pull(cursor)
        applyChanges(response.changes)
        cursorDao.upsert(SyncCursorEntity("global", response.next_cursor, java.time.Instant.now().toString()))
    }

    private suspend fun applyChanges(changes: List<SyncChange>) {
        changes.forEach { change ->
            if (change.action == "delete") {
                changeDao.delete(change.module, change.entity, change.id)
                return@forEach
            }

            val payloadJson = mapAdapter.toJson(change.payload)
            changeDao.upsert(
                SyncChangeEntity(
                    module = change.module,
                    entity = change.entity,
                    id = change.id,
                    action = change.action,
                    payload = payloadJson,
                    updatedAt = change.updated_at
                )
            )
        }
    }
}
