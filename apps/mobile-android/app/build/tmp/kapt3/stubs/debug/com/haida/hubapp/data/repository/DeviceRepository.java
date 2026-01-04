package com.haida.hubapp.data.repository;

@javax.inject.Singleton()
@kotlin.Metadata(mv = {1, 9, 0}, k = 1, xi = 48, d1 = {"\u00000\n\u0002\u0018\u0002\n\u0002\u0010\u0000\n\u0000\n\u0002\u0018\u0002\n\u0000\n\u0002\u0018\u0002\n\u0002\b\u0002\n\u0002\u0010\t\n\u0002\b\u0002\n\u0002\u0010\u0002\n\u0002\b\u0002\n\u0002\u0010\u000e\n\u0002\b\u0002\b\u0007\u0018\u00002\u00020\u0001B\u0017\b\u0007\u0012\u0006\u0010\u0002\u001a\u00020\u0003\u0012\u0006\u0010\u0004\u001a\u00020\u0005\u00a2\u0006\u0002\u0010\u0006J\u0010\u0010\u0007\u001a\u0004\u0018\u00010\bH\u0086@\u00a2\u0006\u0002\u0010\tJ\u000e\u0010\n\u001a\u00020\u000bH\u0086@\u00a2\u0006\u0002\u0010\tJ\u0016\u0010\f\u001a\u00020\u000b2\u0006\u0010\r\u001a\u00020\u000eH\u0086@\u00a2\u0006\u0002\u0010\u000fR\u000e\u0010\u0002\u001a\u00020\u0003X\u0082\u0004\u00a2\u0006\u0002\n\u0000R\u000e\u0010\u0004\u001a\u00020\u0005X\u0082\u0004\u00a2\u0006\u0002\n\u0000\u00a8\u0006\u0010"}, d2 = {"Lcom/haida/hubapp/data/repository/DeviceRepository;", "", "api", "Lcom/haida/hubapp/data/remote/AppApiService;", "store", "Lcom/haida/hubapp/security/SecureStore;", "(Lcom/haida/hubapp/data/remote/AppApiService;Lcom/haida/hubapp/security/SecureStore;)V", "ensureDeviceRegistered", "", "(Lkotlin/coroutines/Continuation;)Ljava/lang/Object;", "flushPendingToken", "", "registerFcmToken", "token", "", "(Ljava/lang/String;Lkotlin/coroutines/Continuation;)Ljava/lang/Object;", "app_debug"})
public final class DeviceRepository {
    @org.jetbrains.annotations.NotNull()
    private final com.haida.hubapp.data.remote.AppApiService api = null;
    @org.jetbrains.annotations.NotNull()
    private final com.haida.hubapp.security.SecureStore store = null;
    
    @javax.inject.Inject()
    public DeviceRepository(@org.jetbrains.annotations.NotNull()
    com.haida.hubapp.data.remote.AppApiService api, @org.jetbrains.annotations.NotNull()
    com.haida.hubapp.security.SecureStore store) {
        super();
    }
    
    @org.jetbrains.annotations.Nullable()
    public final java.lang.Object ensureDeviceRegistered(@org.jetbrains.annotations.NotNull()
    kotlin.coroutines.Continuation<? super java.lang.Long> $completion) {
        return null;
    }
    
    @org.jetbrains.annotations.Nullable()
    public final java.lang.Object registerFcmToken(@org.jetbrains.annotations.NotNull()
    java.lang.String token, @org.jetbrains.annotations.NotNull()
    kotlin.coroutines.Continuation<? super kotlin.Unit> $completion) {
        return null;
    }
    
    @org.jetbrains.annotations.Nullable()
    public final java.lang.Object flushPendingToken(@org.jetbrains.annotations.NotNull()
    kotlin.coroutines.Continuation<? super kotlin.Unit> $completion) {
        return null;
    }
}