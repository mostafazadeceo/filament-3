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
class TasksViewModel @Inject constructor(
    private val syncRepository: SyncRepository
) : ViewModel() {
    fun createTask(title: String) {
        CoroutineScope(Dispatchers.IO).launch {
            val requestId = Idempotency.key("task")
            syncRepository.enqueue(
                module = "tasks",
                action = "task.create",
                payload = mapOf(
                    "record_id" to requestId,
                    "title" to title,
                    "status" to "open"
                ),
                idempotencyKey = requestId
            )
        }
    }
}
