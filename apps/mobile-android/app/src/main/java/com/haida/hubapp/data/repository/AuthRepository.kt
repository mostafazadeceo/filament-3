package com.haida.hubapp.data.repository

import com.haida.hubapp.data.remote.AppApiService
import com.haida.hubapp.data.remote.AuthResponse
import com.haida.hubapp.data.remote.LoginRequest
import com.haida.hubapp.data.remote.RefreshRequest
import com.haida.hubapp.security.PlayIntegrityManager
import com.haida.hubapp.security.SecureStore
import javax.inject.Inject
import javax.inject.Singleton

@Singleton
class AuthRepository @Inject constructor(
    private val api: AppApiService,
    private val store: SecureStore,
    private val playIntegrityManager: PlayIntegrityManager
) {
    suspend fun login(email: String, password: String, tenantId: String? = null): AuthResponse {
        val integrityToken = playIntegrityManager.requestIntegrityToken()
        val response = api.login(LoginRequest(email, password, tenantId, integrityToken))
        store.saveTokens(response.access_token, response.refresh_token, response.tenant?.id?.toString())
        return response
    }

    suspend fun refresh(): AuthResponse? {
        val token = store.getRefreshToken() ?: return null
        val response = api.refresh(RefreshRequest(token))
        store.saveTokens(response.access_token, response.refresh_token, response.tenant?.id?.toString())
        return response
    }

    suspend fun logout() {
        api.logout()
        store.clear()
    }
}
