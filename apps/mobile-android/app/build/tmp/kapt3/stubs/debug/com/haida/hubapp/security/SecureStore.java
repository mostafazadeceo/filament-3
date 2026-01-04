package com.haida.hubapp.security;

@kotlin.Metadata(mv = {1, 9, 0}, k = 1, xi = 48, d1 = {"\u0000 \n\u0002\u0018\u0002\n\u0002\u0010\u0000\n\u0000\n\u0002\u0010\u0002\n\u0000\n\u0002\u0010\u000e\n\u0002\b\u0002\n\u0002\u0010\t\n\u0002\b\r\bf\u0018\u00002\u00020\u0001J\b\u0010\u0002\u001a\u00020\u0003H&J\n\u0010\u0004\u001a\u0004\u0018\u00010\u0005H&J\b\u0010\u0006\u001a\u00020\u0005H&J\u000f\u0010\u0007\u001a\u0004\u0018\u00010\bH&\u00a2\u0006\u0002\u0010\tJ\n\u0010\n\u001a\u0004\u0018\u00010\u0005H&J\n\u0010\u000b\u001a\u0004\u0018\u00010\u0005H&J\n\u0010\f\u001a\u0004\u0018\u00010\u0005H&J$\u0010\r\u001a\u00020\u00032\u0006\u0010\u000e\u001a\u00020\u00052\b\u0010\u000f\u001a\u0004\u0018\u00010\u00052\b\u0010\u0010\u001a\u0004\u0018\u00010\u0005H&J\u0017\u0010\u0011\u001a\u00020\u00032\b\u0010\u0012\u001a\u0004\u0018\u00010\bH&\u00a2\u0006\u0002\u0010\u0013J\u0012\u0010\u0014\u001a\u00020\u00032\b\u0010\u0012\u001a\u0004\u0018\u00010\u0005H&\u00a8\u0006\u0015"}, d2 = {"Lcom/haida/hubapp/security/SecureStore;", "", "clear", "", "getAccessToken", "", "getDeviceIdentifier", "getDeviceServerId", "", "()Ljava/lang/Long;", "getLastFcmToken", "getRefreshToken", "getTenantId", "saveTokens", "accessToken", "refreshToken", "tenantId", "setDeviceServerId", "value", "(Ljava/lang/Long;)V", "setLastFcmToken", "app_debug"})
public abstract interface SecureStore {
    
    public abstract void saveTokens(@org.jetbrains.annotations.NotNull()
    java.lang.String accessToken, @org.jetbrains.annotations.Nullable()
    java.lang.String refreshToken, @org.jetbrains.annotations.Nullable()
    java.lang.String tenantId);
    
    @org.jetbrains.annotations.Nullable()
    public abstract java.lang.String getAccessToken();
    
    @org.jetbrains.annotations.Nullable()
    public abstract java.lang.String getRefreshToken();
    
    @org.jetbrains.annotations.Nullable()
    public abstract java.lang.String getTenantId();
    
    @org.jetbrains.annotations.NotNull()
    public abstract java.lang.String getDeviceIdentifier();
    
    @org.jetbrains.annotations.Nullable()
    public abstract java.lang.Long getDeviceServerId();
    
    public abstract void setDeviceServerId(@org.jetbrains.annotations.Nullable()
    java.lang.Long value);
    
    @org.jetbrains.annotations.Nullable()
    public abstract java.lang.String getLastFcmToken();
    
    public abstract void setLastFcmToken(@org.jetbrains.annotations.Nullable()
    java.lang.String value);
    
    public abstract void clear();
}