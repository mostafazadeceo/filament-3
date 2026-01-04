package com.haida.hubapp.data.repository;

import com.haida.hubapp.data.local.OutboxDao;
import com.haida.hubapp.data.local.SyncChangeDao;
import com.haida.hubapp.data.local.SyncCursorDao;
import com.haida.hubapp.data.remote.AppApiService;
import com.squareup.moshi.Moshi;
import dagger.internal.DaggerGenerated;
import dagger.internal.Factory;
import dagger.internal.QualifierMetadata;
import dagger.internal.ScopeMetadata;
import javax.annotation.processing.Generated;
import javax.inject.Provider;

@ScopeMetadata("javax.inject.Singleton")
@QualifierMetadata
@DaggerGenerated
@Generated(
    value = "dagger.internal.codegen.ComponentProcessor",
    comments = "https://dagger.dev"
)
@SuppressWarnings({
    "unchecked",
    "rawtypes",
    "KotlinInternal",
    "KotlinInternalInJava",
    "cast"
})
public final class SyncRepository_Factory implements Factory<SyncRepository> {
  private final Provider<OutboxDao> outboxDaoProvider;

  private final Provider<SyncCursorDao> cursorDaoProvider;

  private final Provider<SyncChangeDao> changeDaoProvider;

  private final Provider<AppApiService> apiProvider;

  private final Provider<Moshi> moshiProvider;

  public SyncRepository_Factory(Provider<OutboxDao> outboxDaoProvider,
      Provider<SyncCursorDao> cursorDaoProvider, Provider<SyncChangeDao> changeDaoProvider,
      Provider<AppApiService> apiProvider, Provider<Moshi> moshiProvider) {
    this.outboxDaoProvider = outboxDaoProvider;
    this.cursorDaoProvider = cursorDaoProvider;
    this.changeDaoProvider = changeDaoProvider;
    this.apiProvider = apiProvider;
    this.moshiProvider = moshiProvider;
  }

  @Override
  public SyncRepository get() {
    return newInstance(outboxDaoProvider.get(), cursorDaoProvider.get(), changeDaoProvider.get(), apiProvider.get(), moshiProvider.get());
  }

  public static SyncRepository_Factory create(Provider<OutboxDao> outboxDaoProvider,
      Provider<SyncCursorDao> cursorDaoProvider, Provider<SyncChangeDao> changeDaoProvider,
      Provider<AppApiService> apiProvider, Provider<Moshi> moshiProvider) {
    return new SyncRepository_Factory(outboxDaoProvider, cursorDaoProvider, changeDaoProvider, apiProvider, moshiProvider);
  }

  public static SyncRepository newInstance(OutboxDao outboxDao, SyncCursorDao cursorDao,
      SyncChangeDao changeDao, AppApiService api, Moshi moshi) {
    return new SyncRepository(outboxDao, cursorDao, changeDao, api, moshi);
  }
}
