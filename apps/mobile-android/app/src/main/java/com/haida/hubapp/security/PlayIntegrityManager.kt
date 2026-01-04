package com.haida.hubapp.security

import android.content.Context
import com.haida.hubapp.BuildConfig
import com.google.android.play.core.integrity.IntegrityManagerFactory
import com.google.android.play.core.integrity.IntegrityTokenRequest
import dagger.hilt.android.qualifiers.ApplicationContext
import java.util.UUID
import javax.inject.Inject
import javax.inject.Singleton
import kotlin.coroutines.resume
import kotlin.coroutines.resumeWithException
import kotlinx.coroutines.suspendCancellableCoroutine

@Singleton
class PlayIntegrityManager @Inject constructor(
    @ApplicationContext private val context: Context
) {
    suspend fun requestIntegrityToken(): String? {
        if (!BuildConfig.PLAY_INTEGRITY_ENABLED || BuildConfig.PLAY_INTEGRITY_PROJECT_NUMBER <= 0L) {
            return null
        }

        return try {
            val manager = IntegrityManagerFactory.create(context)
            val nonce = UUID.randomUUID().toString()
            val request = IntegrityTokenRequest.builder()
                .setCloudProjectNumber(BuildConfig.PLAY_INTEGRITY_PROJECT_NUMBER)
                .setNonce(nonce)
                .build()
            val response = suspendCancellableCoroutine { continuation ->
                manager.requestIntegrityToken(request)
                    .addOnSuccessListener { continuation.resume(it) }
                    .addOnFailureListener { continuation.resumeWithException(it) }
            }
            response.token()
        } catch (ex: Exception) {
            null
        }
    }
}
