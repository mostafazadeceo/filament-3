package com.haida.hubapp.notifications;

@dagger.hilt.android.AndroidEntryPoint()
@kotlin.Metadata(mv = {1, 9, 0}, k = 1, xi = 48, d1 = {"\u0000.\n\u0002\u0018\u0002\n\u0002\u0018\u0002\n\u0002\b\u0002\n\u0002\u0018\u0002\n\u0002\b\u0005\n\u0002\u0018\u0002\n\u0000\n\u0002\u0010\u0002\n\u0000\n\u0002\u0018\u0002\n\u0002\b\u0002\n\u0002\u0010\u000e\n\u0000\b\u0007\u0018\u00002\u00020\u0001B\u0005\u00a2\u0006\u0002\u0010\u0002J\u0010\u0010\u000b\u001a\u00020\f2\u0006\u0010\r\u001a\u00020\u000eH\u0016J\u0010\u0010\u000f\u001a\u00020\f2\u0006\u0010\u0010\u001a\u00020\u0011H\u0016R\u001e\u0010\u0003\u001a\u00020\u00048\u0006@\u0006X\u0087.\u00a2\u0006\u000e\n\u0000\u001a\u0004\b\u0005\u0010\u0006\"\u0004\b\u0007\u0010\bR\u000e\u0010\t\u001a\u00020\nX\u0082\u0004\u00a2\u0006\u0002\n\u0000\u00a8\u0006\u0012"}, d2 = {"Lcom/haida/hubapp/notifications/HubMessagingService;", "Lcom/google/firebase/messaging/FirebaseMessagingService;", "()V", "deviceRepository", "Lcom/haida/hubapp/data/repository/DeviceRepository;", "getDeviceRepository", "()Lcom/haida/hubapp/data/repository/DeviceRepository;", "setDeviceRepository", "(Lcom/haida/hubapp/data/repository/DeviceRepository;)V", "serviceScope", "Lkotlinx/coroutines/CoroutineScope;", "onMessageReceived", "", "message", "Lcom/google/firebase/messaging/RemoteMessage;", "onNewToken", "token", "", "app_debug"})
public final class HubMessagingService extends com.google.firebase.messaging.FirebaseMessagingService {
    @javax.inject.Inject()
    public com.haida.hubapp.data.repository.DeviceRepository deviceRepository;
    @org.jetbrains.annotations.NotNull()
    private final kotlinx.coroutines.CoroutineScope serviceScope = null;
    
    public HubMessagingService() {
        super();
    }
    
    @org.jetbrains.annotations.NotNull()
    public final com.haida.hubapp.data.repository.DeviceRepository getDeviceRepository() {
        return null;
    }
    
    public final void setDeviceRepository(@org.jetbrains.annotations.NotNull()
    com.haida.hubapp.data.repository.DeviceRepository p0) {
    }
    
    @java.lang.Override()
    public void onNewToken(@org.jetbrains.annotations.NotNull()
    java.lang.String token) {
    }
    
    @java.lang.Override()
    public void onMessageReceived(@org.jetbrains.annotations.NotNull()
    com.google.firebase.messaging.RemoteMessage message) {
    }
}