package com.haida.hubapp.di;

@dagger.Module()
@kotlin.Metadata(mv = {1, 9, 0}, k = 1, xi = 48, d1 = {"\u0000R\n\u0002\u0018\u0002\n\u0002\u0010\u0000\n\u0002\b\u0002\n\u0002\u0018\u0002\n\u0000\n\u0002\u0018\u0002\n\u0000\n\u0002\u0018\u0002\n\u0000\n\u0002\u0018\u0002\n\u0000\n\u0002\u0018\u0002\n\u0002\b\u0002\n\u0002\u0018\u0002\n\u0000\n\u0002\u0018\u0002\n\u0000\n\u0002\u0018\u0002\n\u0000\n\u0002\u0018\u0002\n\u0000\n\u0002\u0018\u0002\n\u0002\b\u0004\n\u0002\u0018\u0002\n\u0000\b\u00c7\u0002\u0018\u00002\u00020\u0001B\u0007\b\u0002\u00a2\u0006\u0002\u0010\u0002J\u0010\u0010\u0003\u001a\u00020\u00042\u0006\u0010\u0005\u001a\u00020\u0006H\u0007J\u0010\u0010\u0007\u001a\u00020\b2\u0006\u0010\t\u001a\u00020\nH\u0007J\u0010\u0010\u000b\u001a\u00020\f2\u0006\u0010\t\u001a\u00020\nH\u0007J\u0012\u0010\r\u001a\u00020\n2\b\b\u0001\u0010\u000e\u001a\u00020\u000fH\u0007J\b\u0010\u0010\u001a\u00020\u0011H\u0007J\u0010\u0010\u0012\u001a\u00020\u00132\u0006\u0010\u0014\u001a\u00020\u0015H\u0007J\u0010\u0010\u0016\u001a\u00020\u00172\u0006\u0010\t\u001a\u00020\nH\u0007J\u0018\u0010\u0018\u001a\u00020\u00062\u0006\u0010\u0019\u001a\u00020\u00132\u0006\u0010\u001a\u001a\u00020\u0011H\u0007J\u0012\u0010\u001b\u001a\u00020\u001c2\b\b\u0001\u0010\u000e\u001a\u00020\u000fH\u0007\u00a8\u0006\u001d"}, d2 = {"Lcom/haida/hubapp/di/AppModule;", "", "()V", "provideAppApiService", "Lcom/haida/hubapp/data/remote/AppApiService;", "retrofit", "Lretrofit2/Retrofit;", "provideChangeDao", "Lcom/haida/hubapp/data/local/SyncChangeDao;", "db", "Lcom/haida/hubapp/data/local/AppDatabase;", "provideCursorDao", "Lcom/haida/hubapp/data/local/SyncCursorDao;", "provideDatabase", "context", "Landroid/content/Context;", "provideMoshi", "Lcom/squareup/moshi/Moshi;", "provideOkHttp", "Lokhttp3/OkHttpClient;", "store", "Lcom/haida/hubapp/security/SecureStore;", "provideOutboxDao", "Lcom/haida/hubapp/data/local/OutboxDao;", "provideRetrofit", "client", "moshi", "provideWorkManager", "Landroidx/work/WorkManager;", "app_debug"})
@dagger.hilt.InstallIn(value = {dagger.hilt.components.SingletonComponent.class})
public final class AppModule {
    @org.jetbrains.annotations.NotNull()
    public static final com.haida.hubapp.di.AppModule INSTANCE = null;
    
    private AppModule() {
        super();
    }
    
    @dagger.Provides()
    @javax.inject.Singleton()
    @org.jetbrains.annotations.NotNull()
    public final com.haida.hubapp.data.local.AppDatabase provideDatabase(@dagger.hilt.android.qualifiers.ApplicationContext()
    @org.jetbrains.annotations.NotNull()
    android.content.Context context) {
        return null;
    }
    
    @dagger.Provides()
    @org.jetbrains.annotations.NotNull()
    public final com.haida.hubapp.data.local.OutboxDao provideOutboxDao(@org.jetbrains.annotations.NotNull()
    com.haida.hubapp.data.local.AppDatabase db) {
        return null;
    }
    
    @dagger.Provides()
    @org.jetbrains.annotations.NotNull()
    public final com.haida.hubapp.data.local.SyncCursorDao provideCursorDao(@org.jetbrains.annotations.NotNull()
    com.haida.hubapp.data.local.AppDatabase db) {
        return null;
    }
    
    @dagger.Provides()
    @org.jetbrains.annotations.NotNull()
    public final com.haida.hubapp.data.local.SyncChangeDao provideChangeDao(@org.jetbrains.annotations.NotNull()
    com.haida.hubapp.data.local.AppDatabase db) {
        return null;
    }
    
    @dagger.Provides()
    @javax.inject.Singleton()
    @org.jetbrains.annotations.NotNull()
    public final com.squareup.moshi.Moshi provideMoshi() {
        return null;
    }
    
    @dagger.Provides()
    @javax.inject.Singleton()
    @org.jetbrains.annotations.NotNull()
    public final okhttp3.OkHttpClient provideOkHttp(@org.jetbrains.annotations.NotNull()
    com.haida.hubapp.security.SecureStore store) {
        return null;
    }
    
    @dagger.Provides()
    @javax.inject.Singleton()
    @org.jetbrains.annotations.NotNull()
    public final retrofit2.Retrofit provideRetrofit(@org.jetbrains.annotations.NotNull()
    okhttp3.OkHttpClient client, @org.jetbrains.annotations.NotNull()
    com.squareup.moshi.Moshi moshi) {
        return null;
    }
    
    @dagger.Provides()
    @org.jetbrains.annotations.NotNull()
    public final com.haida.hubapp.data.remote.AppApiService provideAppApiService(@org.jetbrains.annotations.NotNull()
    retrofit2.Retrofit retrofit) {
        return null;
    }
    
    @dagger.Provides()
    @org.jetbrains.annotations.NotNull()
    public final androidx.work.WorkManager provideWorkManager(@dagger.hilt.android.qualifiers.ApplicationContext()
    @org.jetbrains.annotations.NotNull()
    android.content.Context context) {
        return null;
    }
}