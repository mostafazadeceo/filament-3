package com.haida.hubapp

import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.lifecycle.lifecycleScope
import com.haida.hubapp.data.repository.DeviceRepository
import com.haida.hubapp.ui.HubApp
import com.haida.hubapp.ui.theme.HaidaHubTheme
import com.haida.hubapp.sync.SyncScheduler
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.launch
import javax.inject.Inject

@AndroidEntryPoint
class MainActivity : ComponentActivity() {
    @Inject
    lateinit var syncScheduler: SyncScheduler
    @Inject
    lateinit var deviceRepository: DeviceRepository

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        syncScheduler.schedule()
        lifecycleScope.launch {
            deviceRepository.ensureDeviceRegistered()
            deviceRepository.flushPendingToken()
        }
        setContent {
            HaidaHubTheme {
                HubApp()
            }
        }
    }
}
