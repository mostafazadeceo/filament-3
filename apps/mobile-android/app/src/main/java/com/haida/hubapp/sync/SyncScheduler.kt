package com.haida.hubapp.sync

import androidx.work.BackoffPolicy
import androidx.work.Constraints
import androidx.work.ExistingPeriodicWorkPolicy
import androidx.work.NetworkType
import androidx.work.PeriodicWorkRequestBuilder
import androidx.work.WorkManager
import java.time.Duration
import javax.inject.Inject
import javax.inject.Singleton

@Singleton
class SyncScheduler @Inject constructor(
    private val workManager: WorkManager
) {
    fun schedule() {
        val request = PeriodicWorkRequestBuilder<SyncWorker>(Duration.ofMinutes(15))
            .setConstraints(
                Constraints.Builder()
                    .setRequiredNetworkType(NetworkType.CONNECTED)
                    .build()
            )
            .setBackoffCriteria(BackoffPolicy.EXPONENTIAL, Duration.ofSeconds(30))
            .build()

        workManager.enqueueUniquePeriodicWork(
            "hub_sync",
            ExistingPeriodicWorkPolicy.KEEP,
            request
        )
    }
}
