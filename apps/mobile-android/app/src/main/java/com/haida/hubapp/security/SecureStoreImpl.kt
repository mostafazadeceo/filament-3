package com.haida.hubapp.security

import android.content.Context
import androidx.security.crypto.EncryptedSharedPreferences
import androidx.security.crypto.MasterKey
import dagger.hilt.android.qualifiers.ApplicationContext
import java.util.UUID
import javax.inject.Inject
import javax.inject.Singleton

@Singleton
class SecureStoreImpl @Inject constructor(
    @ApplicationContext private val context: Context
) : SecureStore {
    private val masterKey = MasterKey.Builder(context)
        .setKeyScheme(MasterKey.KeyScheme.AES256_GCM)
        .build()

    private val prefs = EncryptedSharedPreferences.create(
        context,
        "hub_secure_store",
        masterKey,
        EncryptedSharedPreferences.PrefKeyEncryptionScheme.AES256_SIV,
        EncryptedSharedPreferences.PrefValueEncryptionScheme.AES256_GCM
    )

    override fun saveTokens(accessToken: String, refreshToken: String?, tenantId: String?) {
        prefs.edit()
            .putString(KEY_ACCESS, accessToken)
            .putString(KEY_REFRESH, refreshToken)
            .putString(KEY_TENANT, tenantId)
            .apply()
    }

    override fun getAccessToken(): String? = prefs.getString(KEY_ACCESS, null)

    override fun getRefreshToken(): String? = prefs.getString(KEY_REFRESH, null)

    override fun getTenantId(): String? = prefs.getString(KEY_TENANT, null)

    override fun getDeviceIdentifier(): String {
        val existing = prefs.getString(KEY_DEVICE_ID, null)
        if (!existing.isNullOrBlank()) {
            return existing
        }

        val generated = UUID.randomUUID().toString()
        prefs.edit().putString(KEY_DEVICE_ID, generated).apply()
        return generated
    }

    override fun getDeviceServerId(): Long? {
        return prefs.getString(KEY_DEVICE_SERVER_ID, null)?.toLongOrNull()
    }

    override fun setDeviceServerId(value: Long?) {
        prefs.edit().putString(KEY_DEVICE_SERVER_ID, value?.toString()).apply()
    }

    override fun getLastFcmToken(): String? = prefs.getString(KEY_FCM_TOKEN, null)

    override fun setLastFcmToken(value: String?) {
        prefs.edit().putString(KEY_FCM_TOKEN, value).apply()
    }

    override fun clear() {
        prefs.edit()
            .remove(KEY_ACCESS)
            .remove(KEY_REFRESH)
            .remove(KEY_TENANT)
            .remove(KEY_DEVICE_SERVER_ID)
            .apply()
    }

    companion object {
        private const val KEY_ACCESS = "access_token"
        private const val KEY_REFRESH = "refresh_token"
        private const val KEY_TENANT = "tenant_id"
        private const val KEY_DEVICE_ID = "device_id"
        private const val KEY_DEVICE_SERVER_ID = "device_server_id"
        private const val KEY_FCM_TOKEN = "fcm_token"
    }
}
