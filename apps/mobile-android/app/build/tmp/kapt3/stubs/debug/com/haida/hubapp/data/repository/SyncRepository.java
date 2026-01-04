package com.haida.hubapp.data.repository;

@javax.inject.Singleton()
@kotlin.Metadata(mv = {1, 9, 0}, k = 1, xi = 48, d1 = {"\u0000L\n\u0002\u0018\u0002\n\u0002\u0010\u0000\n\u0000\n\u0002\u0018\u0002\n\u0000\n\u0002\u0018\u0002\n\u0000\n\u0002\u0018\u0002\n\u0000\n\u0002\u0018\u0002\n\u0000\n\u0002\u0018\u0002\n\u0002\b\u0002\n\u0002\u0018\u0002\n\u0002\u0010$\n\u0002\u0010\u000e\n\u0002\b\u0002\n\u0002\u0010\u0002\n\u0000\n\u0002\u0010 \n\u0002\u0018\u0002\n\u0002\b\u000b\b\u0007\u0018\u00002\u00020\u0001B/\b\u0007\u0012\u0006\u0010\u0002\u001a\u00020\u0003\u0012\u0006\u0010\u0004\u001a\u00020\u0005\u0012\u0006\u0010\u0006\u001a\u00020\u0007\u0012\u0006\u0010\b\u001a\u00020\t\u0012\u0006\u0010\n\u001a\u00020\u000b\u00a2\u0006\u0002\u0010\fJ\u001c\u0010\u0012\u001a\u00020\u00132\f\u0010\u0014\u001a\b\u0012\u0004\u0012\u00020\u00160\u0015H\u0082@\u00a2\u0006\u0002\u0010\u0017J<\u0010\u0018\u001a\u00020\u00132\u0006\u0010\u0019\u001a\u00020\u00102\u0006\u0010\u001a\u001a\u00020\u00102\u0014\u0010\u001b\u001a\u0010\u0012\u0004\u0012\u00020\u0010\u0012\u0006\u0012\u0004\u0018\u00010\u00010\u000f2\u0006\u0010\u001c\u001a\u00020\u0010H\u0086@\u00a2\u0006\u0002\u0010\u001dJ\u000e\u0010\u001e\u001a\u00020\u0013H\u0086@\u00a2\u0006\u0002\u0010\u001fJ\u000e\u0010 \u001a\u00020\u0013H\u0086@\u00a2\u0006\u0002\u0010\u001fR\u000e\u0010\b\u001a\u00020\tX\u0082\u0004\u00a2\u0006\u0002\n\u0000R\u000e\u0010\u0006\u001a\u00020\u0007X\u0082\u0004\u00a2\u0006\u0002\n\u0000R\u000e\u0010\u0004\u001a\u00020\u0005X\u0082\u0004\u00a2\u0006\u0002\n\u0000Rj\u0010\r\u001a^\u0012(\u0012&\u0012\u0004\u0012\u00020\u0010\u0012\u0006\u0012\u0004\u0018\u00010\u0001 \u0011*\u0012\u0012\u0004\u0012\u00020\u0010\u0012\u0006\u0012\u0004\u0018\u00010\u0001\u0018\u00010\u000f0\u000f \u0011*.\u0012(\u0012&\u0012\u0004\u0012\u00020\u0010\u0012\u0006\u0012\u0004\u0018\u00010\u0001 \u0011*\u0012\u0012\u0004\u0012\u00020\u0010\u0012\u0006\u0012\u0004\u0018\u00010\u0001\u0018\u00010\u000f0\u000f\u0018\u00010\u000e0\u000eX\u0082\u0004\u00a2\u0006\u0002\n\u0000R\u000e\u0010\n\u001a\u00020\u000bX\u0082\u0004\u00a2\u0006\u0002\n\u0000R\u000e\u0010\u0002\u001a\u00020\u0003X\u0082\u0004\u00a2\u0006\u0002\n\u0000\u00a8\u0006!"}, d2 = {"Lcom/haida/hubapp/data/repository/SyncRepository;", "", "outboxDao", "Lcom/haida/hubapp/data/local/OutboxDao;", "cursorDao", "Lcom/haida/hubapp/data/local/SyncCursorDao;", "changeDao", "Lcom/haida/hubapp/data/local/SyncChangeDao;", "api", "Lcom/haida/hubapp/data/remote/AppApiService;", "moshi", "Lcom/squareup/moshi/Moshi;", "(Lcom/haida/hubapp/data/local/OutboxDao;Lcom/haida/hubapp/data/local/SyncCursorDao;Lcom/haida/hubapp/data/local/SyncChangeDao;Lcom/haida/hubapp/data/remote/AppApiService;Lcom/squareup/moshi/Moshi;)V", "mapAdapter", "Lcom/squareup/moshi/JsonAdapter;", "", "", "kotlin.jvm.PlatformType", "applyChanges", "", "changes", "", "Lcom/haida/hubapp/data/remote/SyncChange;", "(Ljava/util/List;Lkotlin/coroutines/Continuation;)Ljava/lang/Object;", "enqueue", "module", "action", "payload", "idempotencyKey", "(Ljava/lang/String;Ljava/lang/String;Ljava/util/Map;Ljava/lang/String;Lkotlin/coroutines/Continuation;)Ljava/lang/Object;", "pullChanges", "(Lkotlin/coroutines/Continuation;)Ljava/lang/Object;", "pushOutbox", "app_debug"})
public final class SyncRepository {
    @org.jetbrains.annotations.NotNull()
    private final com.haida.hubapp.data.local.OutboxDao outboxDao = null;
    @org.jetbrains.annotations.NotNull()
    private final com.haida.hubapp.data.local.SyncCursorDao cursorDao = null;
    @org.jetbrains.annotations.NotNull()
    private final com.haida.hubapp.data.local.SyncChangeDao changeDao = null;
    @org.jetbrains.annotations.NotNull()
    private final com.haida.hubapp.data.remote.AppApiService api = null;
    @org.jetbrains.annotations.NotNull()
    private final com.squareup.moshi.Moshi moshi = null;
    private final com.squareup.moshi.JsonAdapter<java.util.Map<java.lang.String, java.lang.Object>> mapAdapter = null;
    
    @javax.inject.Inject()
    public SyncRepository(@org.jetbrains.annotations.NotNull()
    com.haida.hubapp.data.local.OutboxDao outboxDao, @org.jetbrains.annotations.NotNull()
    com.haida.hubapp.data.local.SyncCursorDao cursorDao, @org.jetbrains.annotations.NotNull()
    com.haida.hubapp.data.local.SyncChangeDao changeDao, @org.jetbrains.annotations.NotNull()
    com.haida.hubapp.data.remote.AppApiService api, @org.jetbrains.annotations.NotNull()
    com.squareup.moshi.Moshi moshi) {
        super();
    }
    
    @org.jetbrains.annotations.Nullable()
    public final java.lang.Object enqueue(@org.jetbrains.annotations.NotNull()
    java.lang.String module, @org.jetbrains.annotations.NotNull()
    java.lang.String action, @org.jetbrains.annotations.NotNull()
    java.util.Map<java.lang.String, ? extends java.lang.Object> payload, @org.jetbrains.annotations.NotNull()
    java.lang.String idempotencyKey, @org.jetbrains.annotations.NotNull()
    kotlin.coroutines.Continuation<? super kotlin.Unit> $completion) {
        return null;
    }
    
    @org.jetbrains.annotations.Nullable()
    public final java.lang.Object pushOutbox(@org.jetbrains.annotations.NotNull()
    kotlin.coroutines.Continuation<? super kotlin.Unit> $completion) {
        return null;
    }
    
    @org.jetbrains.annotations.Nullable()
    public final java.lang.Object pullChanges(@org.jetbrains.annotations.NotNull()
    kotlin.coroutines.Continuation<? super kotlin.Unit> $completion) {
        return null;
    }
    
    private final java.lang.Object applyChanges(java.util.List<com.haida.hubapp.data.remote.SyncChange> changes, kotlin.coroutines.Continuation<? super kotlin.Unit> $completion) {
        return null;
    }
}