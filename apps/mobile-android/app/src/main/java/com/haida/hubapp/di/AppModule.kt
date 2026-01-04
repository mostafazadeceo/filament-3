package com.haida.hubapp.di

import android.content.Context
import androidx.room.Room
import androidx.work.WorkManager
import com.haida.hubapp.BuildConfig
import com.haida.hubapp.data.local.AppDatabase
import com.haida.hubapp.data.local.MIGRATION_1_2
import com.haida.hubapp.data.local.OutboxDao
import com.haida.hubapp.data.local.SyncChangeDao
import com.haida.hubapp.data.local.SyncCursorDao
import com.haida.hubapp.data.remote.AppApiService
import com.haida.hubapp.security.SecureStore
import com.haida.hubapp.security.SecureStoreImpl
import com.squareup.moshi.Moshi
import com.squareup.moshi.kotlin.reflect.KotlinJsonAdapterFactory
import dagger.Binds
import dagger.Module
import dagger.Provides
import dagger.hilt.InstallIn
import dagger.hilt.android.qualifiers.ApplicationContext
import dagger.hilt.components.SingletonComponent
import okhttp3.Interceptor
import okhttp3.OkHttpClient
import okhttp3.logging.HttpLoggingInterceptor
import retrofit2.Retrofit
import retrofit2.converter.moshi.MoshiConverterFactory
import javax.inject.Singleton

@Module
@InstallIn(SingletonComponent::class)
abstract class SecureModule {
    @Binds
    abstract fun bindSecureStore(impl: SecureStoreImpl): SecureStore
}

@Module
@InstallIn(SingletonComponent::class)
object AppModule {
    @Provides
    @Singleton
    fun provideDatabase(@ApplicationContext context: Context): AppDatabase {
        return Room.databaseBuilder(context, AppDatabase::class.java, "hub_app.db")
            .addMigrations(MIGRATION_1_2)
            .fallbackToDestructiveMigration()
            .build()
    }

    @Provides
    fun provideOutboxDao(db: AppDatabase): OutboxDao = db.outboxDao()

    @Provides
    fun provideCursorDao(db: AppDatabase): SyncCursorDao = db.syncCursorDao()

    @Provides
    fun provideChangeDao(db: AppDatabase): SyncChangeDao = db.syncChangeDao()

    @Provides
    @Singleton
    fun provideMoshi(): Moshi = Moshi.Builder().add(KotlinJsonAdapterFactory()).build()

    @Provides
    @Singleton
    fun provideOkHttp(store: SecureStore): OkHttpClient {
        val authInterceptor = Interceptor { chain ->
            val request = chain.request().newBuilder()
            val token = store.getAccessToken()
            if (!token.isNullOrBlank()) {
                request.addHeader("Authorization", "Bearer $token")
            }
            if (BuildConfig.API_KEY.isNotBlank()) {
                request.addHeader("X-Api-Key", BuildConfig.API_KEY)
            }
            val tenantId = store.getTenantId()
            if (!tenantId.isNullOrBlank()) {
                request.addHeader("X-Tenant-ID", tenantId)
            }
            val deviceId = store.getDeviceIdentifier()
            if (deviceId.isNotBlank()) {
                request.addHeader("X-Device-ID", deviceId)
            }
            chain.proceed(request.build())
        }

        return OkHttpClient.Builder()
            .addInterceptor(authInterceptor)
            .addInterceptor(HttpLoggingInterceptor().apply {
                level = HttpLoggingInterceptor.Level.BASIC
            })
            .build()
    }

    @Provides
    @Singleton
    fun provideRetrofit(client: OkHttpClient, moshi: Moshi): Retrofit {
        return Retrofit.Builder()
            .baseUrl(BuildConfig.API_BASE_URL)
            .client(client)
            .addConverterFactory(MoshiConverterFactory.create(moshi))
            .build()
    }

    @Provides
    fun provideAppApiService(retrofit: Retrofit): AppApiService =
        retrofit.create(AppApiService::class.java)

    @Provides
    fun provideWorkManager(@ApplicationContext context: Context): WorkManager =
        WorkManager.getInstance(context)
}
