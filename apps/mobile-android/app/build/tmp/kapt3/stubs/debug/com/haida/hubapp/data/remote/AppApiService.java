package com.haida.hubapp.data.remote;

@kotlin.Metadata(mv = {1, 9, 0}, k = 1, xi = 48, d1 = {"\u0000l\n\u0002\u0018\u0002\n\u0002\u0010\u0000\n\u0000\n\u0002\u0018\u0002\n\u0002\b\u0002\n\u0002\u0018\u0002\n\u0000\n\u0002\u0018\u0002\n\u0002\b\u0002\n\u0002\u0018\u0002\n\u0000\n\u0002\u0018\u0002\n\u0000\n\u0002\u0018\u0002\n\u0000\n\u0002\u0010\u000e\n\u0002\b\u0002\n\u0002\u0018\u0002\n\u0002\u0018\u0002\n\u0002\b\u0002\n\u0002\u0018\u0002\n\u0002\b\u0002\n\u0002\u0018\u0002\n\u0002\u0018\u0002\n\u0002\b\u0002\n\u0002\u0018\u0002\n\u0000\n\u0002\u0010\t\n\u0002\u0018\u0002\n\u0002\b\u0002\bf\u0018\u00002\u00020\u0001J\u000e\u0010\u0002\u001a\u00020\u0003H\u00a7@\u00a2\u0006\u0002\u0010\u0004J\u0018\u0010\u0005\u001a\u00020\u00062\b\b\u0001\u0010\u0007\u001a\u00020\bH\u00a7@\u00a2\u0006\u0002\u0010\tJ\u000e\u0010\n\u001a\u00020\u000bH\u00a7@\u00a2\u0006\u0002\u0010\u0004J\u000e\u0010\f\u001a\u00020\rH\u00a7@\u00a2\u0006\u0002\u0010\u0004J\u001a\u0010\u000e\u001a\u00020\u000f2\n\b\u0001\u0010\u0010\u001a\u0004\u0018\u00010\u0011H\u00a7@\u00a2\u0006\u0002\u0010\u0012J\u0018\u0010\u0013\u001a\u00020\u00142\b\b\u0001\u0010\u0007\u001a\u00020\u0015H\u00a7@\u00a2\u0006\u0002\u0010\u0016J\u0018\u0010\u0017\u001a\u00020\u00062\b\b\u0001\u0010\u0007\u001a\u00020\u0018H\u00a7@\u00a2\u0006\u0002\u0010\u0019J\u0018\u0010\u001a\u001a\u00020\u001b2\b\b\u0001\u0010\u0007\u001a\u00020\u001cH\u00a7@\u00a2\u0006\u0002\u0010\u001dJ\"\u0010\u001e\u001a\u00020\u001f2\b\b\u0001\u0010 \u001a\u00020!2\b\b\u0001\u0010\u0007\u001a\u00020\"H\u00a7@\u00a2\u0006\u0002\u0010#\u00a8\u0006$"}, d2 = {"Lcom/haida/hubapp/data/remote/AppApiService;", "", "capabilities", "Lcom/haida/hubapp/data/remote/CapabilityResponse;", "(Lkotlin/coroutines/Continuation;)Ljava/lang/Object;", "login", "Lcom/haida/hubapp/data/remote/AuthResponse;", "payload", "Lcom/haida/hubapp/data/remote/LoginRequest;", "(Lcom/haida/hubapp/data/remote/LoginRequest;Lkotlin/coroutines/Continuation;)Ljava/lang/Object;", "logout", "Lcom/haida/hubapp/data/remote/ApiMessage;", "me", "Lcom/haida/hubapp/data/remote/UserProfile;", "pull", "Lcom/haida/hubapp/data/remote/PullResponse;", "cursor", "", "(Ljava/lang/String;Lkotlin/coroutines/Continuation;)Ljava/lang/Object;", "push", "Lcom/haida/hubapp/data/remote/PushResponse;", "Lcom/haida/hubapp/data/remote/PushRequest;", "(Lcom/haida/hubapp/data/remote/PushRequest;Lkotlin/coroutines/Continuation;)Ljava/lang/Object;", "refresh", "Lcom/haida/hubapp/data/remote/RefreshRequest;", "(Lcom/haida/hubapp/data/remote/RefreshRequest;Lkotlin/coroutines/Continuation;)Ljava/lang/Object;", "registerDevice", "Lcom/haida/hubapp/data/remote/DeviceResponse;", "Lcom/haida/hubapp/data/remote/DeviceRegisterRequest;", "(Lcom/haida/hubapp/data/remote/DeviceRegisterRequest;Lkotlin/coroutines/Continuation;)Ljava/lang/Object;", "registerDeviceToken", "Lcom/haida/hubapp/data/remote/DeviceTokenResponse;", "deviceId", "", "Lcom/haida/hubapp/data/remote/DeviceTokenRequest;", "(JLcom/haida/hubapp/data/remote/DeviceTokenRequest;Lkotlin/coroutines/Continuation;)Ljava/lang/Object;", "app_debug"})
public abstract interface AppApiService {
    
    @retrofit2.http.POST(value = "/api/v1/app/auth/login")
    @org.jetbrains.annotations.Nullable()
    public abstract java.lang.Object login(@retrofit2.http.Body()
    @org.jetbrains.annotations.NotNull()
    com.haida.hubapp.data.remote.LoginRequest payload, @org.jetbrains.annotations.NotNull()
    kotlin.coroutines.Continuation<? super com.haida.hubapp.data.remote.AuthResponse> $completion);
    
    @retrofit2.http.POST(value = "/api/v1/app/auth/refresh")
    @org.jetbrains.annotations.Nullable()
    public abstract java.lang.Object refresh(@retrofit2.http.Body()
    @org.jetbrains.annotations.NotNull()
    com.haida.hubapp.data.remote.RefreshRequest payload, @org.jetbrains.annotations.NotNull()
    kotlin.coroutines.Continuation<? super com.haida.hubapp.data.remote.AuthResponse> $completion);
    
    @retrofit2.http.POST(value = "/api/v1/app/auth/logout")
    @org.jetbrains.annotations.Nullable()
    public abstract java.lang.Object logout(@org.jetbrains.annotations.NotNull()
    kotlin.coroutines.Continuation<? super com.haida.hubapp.data.remote.ApiMessage> $completion);
    
    @retrofit2.http.GET(value = "/api/v1/app/auth/me")
    @org.jetbrains.annotations.Nullable()
    public abstract java.lang.Object me(@org.jetbrains.annotations.NotNull()
    kotlin.coroutines.Continuation<? super com.haida.hubapp.data.remote.UserProfile> $completion);
    
    @retrofit2.http.GET(value = "/api/v1/app/capabilities")
    @org.jetbrains.annotations.Nullable()
    public abstract java.lang.Object capabilities(@org.jetbrains.annotations.NotNull()
    kotlin.coroutines.Continuation<? super com.haida.hubapp.data.remote.CapabilityResponse> $completion);
    
    @retrofit2.http.POST(value = "/api/v1/app/sync/push")
    @org.jetbrains.annotations.Nullable()
    public abstract java.lang.Object push(@retrofit2.http.Body()
    @org.jetbrains.annotations.NotNull()
    com.haida.hubapp.data.remote.PushRequest payload, @org.jetbrains.annotations.NotNull()
    kotlin.coroutines.Continuation<? super com.haida.hubapp.data.remote.PushResponse> $completion);
    
    @retrofit2.http.GET(value = "/api/v1/app/sync/pull")
    @org.jetbrains.annotations.Nullable()
    public abstract java.lang.Object pull(@retrofit2.http.Query(value = "cursor")
    @org.jetbrains.annotations.Nullable()
    java.lang.String cursor, @org.jetbrains.annotations.NotNull()
    kotlin.coroutines.Continuation<? super com.haida.hubapp.data.remote.PullResponse> $completion);
    
    @retrofit2.http.POST(value = "/api/v1/app/devices")
    @org.jetbrains.annotations.Nullable()
    public abstract java.lang.Object registerDevice(@retrofit2.http.Body()
    @org.jetbrains.annotations.NotNull()
    com.haida.hubapp.data.remote.DeviceRegisterRequest payload, @org.jetbrains.annotations.NotNull()
    kotlin.coroutines.Continuation<? super com.haida.hubapp.data.remote.DeviceResponse> $completion);
    
    @retrofit2.http.POST(value = "/api/v1/app/devices/{device}/tokens")
    @org.jetbrains.annotations.Nullable()
    public abstract java.lang.Object registerDeviceToken(@retrofit2.http.Path(value = "device")
    long deviceId, @retrofit2.http.Body()
    @org.jetbrains.annotations.NotNull()
    com.haida.hubapp.data.remote.DeviceTokenRequest payload, @org.jetbrains.annotations.NotNull()
    kotlin.coroutines.Continuation<? super com.haida.hubapp.data.remote.DeviceTokenResponse> $completion);
}