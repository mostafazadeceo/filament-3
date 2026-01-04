package com.haida.hubapp.data.remote

import retrofit2.http.Body
import retrofit2.http.GET
import retrofit2.http.POST
import retrofit2.http.Path
import retrofit2.http.Query

interface AppApiService {
    @POST("/api/v1/app/auth/login")
    suspend fun login(@Body payload: LoginRequest): AuthResponse

    @POST("/api/v1/app/auth/refresh")
    suspend fun refresh(@Body payload: RefreshRequest): AuthResponse

    @POST("/api/v1/app/auth/logout")
    suspend fun logout(): ApiMessage

    @GET("/api/v1/app/auth/me")
    suspend fun me(): UserProfile

    @GET("/api/v1/app/capabilities")
    suspend fun capabilities(): CapabilityResponse

    @POST("/api/v1/app/sync/push")
    suspend fun push(@Body payload: PushRequest): PushResponse

    @GET("/api/v1/app/sync/pull")
    suspend fun pull(@Query("cursor") cursor: String?): PullResponse

    @POST("/api/v1/app/devices")
    suspend fun registerDevice(@Body payload: DeviceRegisterRequest): DeviceResponse

    @POST("/api/v1/app/devices/{device}/tokens")
    suspend fun registerDeviceToken(
        @Path("device") deviceId: Long,
        @Body payload: DeviceTokenRequest
    ): DeviceTokenResponse
}

data class LoginRequest(
    val email: String,
    val password: String,
    val tenant_id: String? = null,
    val integrity_token: String? = null
)

data class RefreshRequest(
    val refresh_token: String
)

data class AuthResponse(
    val access_token: String,
    val refresh_token: String?,
    val user: UserProfile,
    val tenant: TenantInfo?
)

data class TenantInfo(
    val id: Long,
    val name: String
)

data class UserProfile(
    val id: Long,
    val name: String,
    val email: String
)

data class ApiMessage(val message: String? = null)

data class CapabilityResponse(
    val permissions: List<Capability>,
    val navigation: Map<String, String>,
    val feature_flags: Map<String, Boolean>
)

data class Capability(
    val key: String,
    val label: String
)

data class PushRequest(
    val items: List<PushItem>
)

data class PushItem(
    val id: String,
    val module: String,
    val action: String,
    val payload: Map<String, Any?>,
    val idempotency_key: String
)

data class PushResponse(
    val results: List<PushResult>
)

data class PushResult(
    val id: String,
    val status: String,
    val message: String?
)

data class PullResponse(
    val next_cursor: String,
    val changes: List<SyncChange>
)

data class SyncChange(
    val module: String,
    val entity: String,
    val id: String,
    val action: String,
    val payload: Map<String, Any?>,
    val updated_at: String
)

data class DeviceRegisterRequest(
    val device_id: String,
    val platform: String,
    val name: String?,
    val metadata: Map<String, Any?>? = null
)

data class DeviceResponse(
    val data: DeviceInfo
)

data class DeviceInfo(
    val id: Long,
    val device_id: String,
    val platform: String,
    val name: String?,
    val metadata: Map<String, Any?>?
)

data class DeviceTokenRequest(
    val provider: String,
    val token: String
)

data class DeviceTokenResponse(
    val data: DeviceTokenInfo
)

data class DeviceTokenInfo(
    val id: Long,
    val provider: String,
    val token: String,
    val status: String?
)
