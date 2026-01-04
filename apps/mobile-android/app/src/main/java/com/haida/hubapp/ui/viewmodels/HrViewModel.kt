package com.haida.hubapp.ui.viewmodels

import androidx.lifecycle.ViewModel
import com.haida.hubapp.data.repository.SyncRepository
import com.haida.hubapp.util.Idempotency
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.CoroutineScope
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.launch
import java.time.Instant
import javax.inject.Inject

@HiltViewModel
class HrViewModel @Inject constructor(
    private val syncRepository: SyncRepository
) : ViewModel() {
    fun checkIn() = enqueue("checkin")

    fun checkOut() = enqueue("checkout")

    private fun enqueue(type: String) {
        CoroutineScope(Dispatchers.IO).launch {
            val requestId = Idempotency.key("attendance")
            syncRepository.enqueue(
                module = "attendance",
                action = type,
                payload = mapOf(
                    "record_id" to requestId,
                    "type" to type,
                    "clocked_at" to Instant.now().toString()
                ),
                idempotencyKey = requestId
            )
        }
    }
}
