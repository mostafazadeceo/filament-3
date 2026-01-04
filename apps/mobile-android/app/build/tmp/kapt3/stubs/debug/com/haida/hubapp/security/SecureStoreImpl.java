package com.haida.hubapp.security;

@javax.inject.Singleton()
@kotlin.Metadata(mv = {1, 9, 0}, k = 1, xi = 48, d1 = {"\u00004\n\u0002\u0018\u0002\n\u0002\u0018\u0002\n\u0000\n\u0002\u0018\u0002\n\u0002\b\u0002\n\u0002\u0018\u0002\n\u0000\n\u0002\u0018\u0002\n\u0000\n\u0002\u0010\u0002\n\u0000\n\u0002\u0010\u000e\n\u0002\b\u0002\n\u0002\u0010\t\n\u0002\b\u000e\b\u0007\u0018\u0000 \u001c2\u00020\u0001:\u0001\u001cB\u0011\b\u0007\u0012\b\b\u0001\u0010\u0002\u001a\u00020\u0003\u00a2\u0006\u0002\u0010\u0004J\b\u0010\t\u001a\u00020\nH\u0016J\n\u0010\u000b\u001a\u0004\u0018\u00010\fH\u0016J\b\u0010\r\u001a\u00020\fH\u0016J\u000f\u0010\u000e\u001a\u0004\u0018\u00010\u000fH\u0016\u00a2\u0006\u0002\u0010\u0010J\n\u0010\u0011\u001a\u0004\u0018\u00010\fH\u0016J\n\u0010\u0012\u001a\u0004\u0018\u00010\fH\u0016J\n\u0010\u0013\u001a\u0004\u0018\u00010\fH\u0016J$\u0010\u0014\u001a\u00020\n2\u0006\u0010\u0015\u001a\u00020\f2\b\u0010\u0016\u001a\u0004\u0018\u00010\f2\b\u0010\u0017\u001a\u0004\u0018\u00010\fH\u0016J\u0017\u0010\u0018\u001a\u00020\n2\b\u0010\u0019\u001a\u0004\u0018\u00010\u000fH\u0016\u00a2\u0006\u0002\u0010\u001aJ\u0012\u0010\u001b\u001a\u00020\n2\b\u0010\u0019\u001a\u0004\u0018\u00010\fH\u0016R\u000e\u0010\u0002\u001a\u00020\u0003X\u0082\u0004\u00a2\u0006\u0002\n\u0000R\u000e\u0010\u0005\u001a\u00020\u0006X\u0082\u0004\u00a2\u0006\u0002\n\u0000R\u000e\u0010\u0007\u001a\u00020\bX\u0082\u0004\u00a2\u0006\u0002\n\u0000\u00a8\u0006\u001d"}, d2 = {"Lcom/haida/hubapp/security/SecureStoreImpl;", "Lcom/haida/hubapp/security/SecureStore;", "context", "Landroid/content/Context;", "(Landroid/content/Context;)V", "masterKey", "Landroidx/security/crypto/MasterKey;", "prefs", "Landroid/content/SharedPreferences;", "clear", "", "getAccessToken", "", "getDeviceIdentifier", "getDeviceServerId", "", "()Ljava/lang/Long;", "getLastFcmToken", "getRefreshToken", "getTenantId", "saveTokens", "accessToken", "refreshToken", "tenantId", "setDeviceServerId", "value", "(Ljava/lang/Long;)V", "setLastFcmToken", "Companion", "app_debug"})
public final class SecureStoreImpl implements com.haida.hubapp.security.SecureStore {
    @org.jetbrains.annotations.NotNull()
    private final android.content.Context context = null;
    @org.jetbrains.annotations.NotNull()
    private final androidx.security.crypto.MasterKey masterKey = null;
    @org.jetbrains.annotations.NotNull()
    private final android.content.SharedPreferences prefs = null;
    @org.jetbrains.annotations.NotNull()
    private static final java.lang.String KEY_ACCESS = "access_token";
    @org.jetbrains.annotations.NotNull()
    private static final java.lang.String KEY_REFRESH = "refresh_token";
    @org.jetbrains.annotations.NotNull()
    private static final java.lang.String KEY_TENANT = "tenant_id";
    @org.jetbrains.annotations.NotNull()
    private static final java.lang.String KEY_DEVICE_ID = "device_id";
    @org.jetbrains.annotations.NotNull()
    private static final java.lang.String KEY_DEVICE_SERVER_ID = "device_server_id";
    @org.jetbrains.annotations.NotNull()
    private static final java.lang.String KEY_FCM_TOKEN = "fcm_token";
    @org.jetbrains.annotations.NotNull()
    public static final com.haida.hubapp.security.SecureStoreImpl.Companion Companion = null;
    
    @javax.inject.Inject()
    public SecureStoreImpl(@dagger.hilt.android.qualifiers.ApplicationContext()
    @org.jetbrains.annotations.NotNull()
    android.content.Context context) {
        super();
    }
    
    @java.lang.Override()
    public void saveTokens(@org.jetbrains.annotations.NotNull()
    java.lang.String accessToken, @org.jetbrains.annotations.Nullable()
    java.lang.String refreshToken, @org.jetbrains.annotations.Nullable()
    java.lang.String tenantId) {
    }
    
    @java.lang.Override()
    @org.jetbrains.annotations.Nullable()
    public java.lang.String getAccessToken() {
        return null;
    }
    
    @java.lang.Override()
    @org.jetbrains.annotations.Nullable()
    public java.lang.String getRefreshToken() {
        return null;
    }
    
    @java.lang.Override()
    @org.jetbrains.annotations.Nullable()
    public java.lang.String getTenantId() {
        return null;
    }
    
    @java.lang.Override()
    @org.jetbrains.annotations.NotNull()
    public java.lang.String getDeviceIdentifier() {
        return null;
    }
    
    @java.lang.Override()
    @org.jetbrains.annotations.Nullable()
    public java.lang.Long getDeviceServerId() {
        return null;
    }
    
    @java.lang.Override()
    public void setDeviceServerId(@org.jetbrains.annotations.Nullable()
    java.lang.Long value) {
    }
    
    @java.lang.Override()
    @org.jetbrains.annotations.Nullable()
    public java.lang.String getLastFcmToken() {
        return null;
    }
    
    @java.lang.Override()
    public void setLastFcmToken(@org.jetbrains.annotations.Nullable()
    java.lang.String value) {
    }
    
    @java.lang.Override()
    public void clear() {
    }
    
    @kotlin.Metadata(mv = {1, 9, 0}, k = 1, xi = 48, d1 = {"\u0000\u0014\n\u0002\u0018\u0002\n\u0002\u0010\u0000\n\u0002\b\u0002\n\u0002\u0010\u000e\n\u0002\b\u0006\b\u0086\u0003\u0018\u00002\u00020\u0001B\u0007\b\u0002\u00a2\u0006\u0002\u0010\u0002R\u000e\u0010\u0003\u001a\u00020\u0004X\u0082T\u00a2\u0006\u0002\n\u0000R\u000e\u0010\u0005\u001a\u00020\u0004X\u0082T\u00a2\u0006\u0002\n\u0000R\u000e\u0010\u0006\u001a\u00020\u0004X\u0082T\u00a2\u0006\u0002\n\u0000R\u000e\u0010\u0007\u001a\u00020\u0004X\u0082T\u00a2\u0006\u0002\n\u0000R\u000e\u0010\b\u001a\u00020\u0004X\u0082T\u00a2\u0006\u0002\n\u0000R\u000e\u0010\t\u001a\u00020\u0004X\u0082T\u00a2\u0006\u0002\n\u0000\u00a8\u0006\n"}, d2 = {"Lcom/haida/hubapp/security/SecureStoreImpl$Companion;", "", "()V", "KEY_ACCESS", "", "KEY_DEVICE_ID", "KEY_DEVICE_SERVER_ID", "KEY_FCM_TOKEN", "KEY_REFRESH", "KEY_TENANT", "app_debug"})
    public static final class Companion {
        
        private Companion() {
            super();
        }
    }
}