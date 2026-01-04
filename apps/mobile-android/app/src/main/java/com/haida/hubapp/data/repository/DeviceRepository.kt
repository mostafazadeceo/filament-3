package com.haida.hubapp.data.repository

import android.os.Build
import com.haida.hubapp.BuildConfig
import com.haida.hubapp.data.remote.AppApiService
import com.haida.hubapp.data.remote.DeviceRegisterRequest
import com.haida.hubapp.data.remote.DeviceTokenRequest
import com.haida.hubapp.security.SecureStore
import javax.inject.Inject
import javax.inject.Singleton

@Singleton
class DeviceRepository @Inject constructor(
    private val api: AppApiService,
    private val store: SecureStore
) {
    suspend fun ensureDeviceRegistered(): Long? {
        if (store.getAccessToken().isNullOrBlank()) {
            return null
        }

        return try {
            val deviceId = store.getDeviceIdentifier()
            val response = api.registerDevice(
                DeviceRegisterRequest(
                    device_id = deviceId,
                    platform = "android",
                    name = Build.MODEL,
                    metadata = mapOf(
                        "sdk" to Build.VERSION.SDK_INT,
                        "manufacturer" to Build.MANUFACTURER,
                        "app_version" to BuildConfig.VERSION_NAME
                    )
                )
            )
            store.setDeviceServerId(response.data.id)
            response.data.id
        } catch (ex: Exception) {
            null
        }
    }

    suspend fun registerFcmToken(token: String) {
        if (token.isBlank()) {
            return
        }

        store.setLastFcmToken(token)
        if (store.getAccessToken().isNullOrBlank()) {
            return
        }

        val deviceServerId = store.getDeviceServerId() ?: ensureDeviceRegistered() ?: return

        try {
            api.registerDeviceToken(
                deviceServerId,
                DeviceTokenRequest(provider = "fcm", token = token)
            )
        } catch (ex: Exception) {
            // Retry on next app start or token refresh.
        }
    }

    suspend fun flushPendingToken() {
        val token = store.getLastFcmToken()
        if (!token.isNullOrBlank()) {
            registerFcmToken(token)
        }
    }
}
