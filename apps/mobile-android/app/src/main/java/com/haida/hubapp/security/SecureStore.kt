package com.haida.hubapp.security

interface SecureStore {
    fun saveTokens(accessToken: String, refreshToken: String?, tenantId: String?)
    fun getAccessToken(): String?
    fun getRefreshToken(): String?
    fun getTenantId(): String?
    fun getDeviceIdentifier(): String
    fun getDeviceServerId(): Long?
    fun setDeviceServerId(value: Long?)
    fun getLastFcmToken(): String?
    fun setLastFcmToken(value: String?)
    fun clear()
}
