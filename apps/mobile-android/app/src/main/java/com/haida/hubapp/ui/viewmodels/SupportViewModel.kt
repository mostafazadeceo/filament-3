package com.haida.hubapp.ui.viewmodels

import androidx.lifecycle.ViewModel
import com.haida.hubapp.data.repository.SyncRepository
import com.haida.hubapp.util.Idempotency
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.CoroutineScope
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.launch
import javax.inject.Inject

@HiltViewModel
class SupportViewModel @Inject constructor(
    private val syncRepository: SyncRepository
) : ViewModel() {
    fun createTicket(subject: String) {
        CoroutineScope(Dispatchers.IO).launch {
            val requestId = Idempotency.key("support")
            syncRepository.enqueue(
                module = "support",
                action = "ticket.create",
                payload = mapOf(
                    "record_id" to requestId,
                    "subject" to subject,
                    "priority" to "normal"
                ),
                idempotencyKey = requestId
            )
        }
    }
}
