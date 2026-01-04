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
class PosViewModel @Inject constructor(
    private val syncRepository: SyncRepository
) : ViewModel() {
    fun createOfflineOrder() {
        CoroutineScope(Dispatchers.IO).launch {
            val requestId = Idempotency.key("pos")
            syncRepository.enqueue(
                module = "pos",
                action = "order.create",
                payload = mapOf(
                    "record_id" to requestId,
                    "status" to "draft",
                    "total" to 0
                ),
                idempotencyKey = requestId
            )
        }
    }
}
