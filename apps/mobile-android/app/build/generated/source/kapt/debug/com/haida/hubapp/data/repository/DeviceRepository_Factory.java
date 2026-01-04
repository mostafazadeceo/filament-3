package com.haida.hubapp.data.repository;

import com.haida.hubapp.data.remote.AppApiService;
import com.haida.hubapp.security.SecureStore;
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
public final class DeviceRepository_Factory implements Factory<DeviceRepository> {
  private final Provider<AppApiService> apiProvider;

  private final Provider<SecureStore> storeProvider;

  public DeviceRepository_Factory(Provider<AppApiService> apiProvider,
      Provider<SecureStore> storeProvider) {
    this.apiProvider = apiProvider;
    this.storeProvider = storeProvider;
  }

  @Override
  public DeviceRepository get() {
    return newInstance(apiProvider.get(), storeProvider.get());
  }

  public static DeviceRepository_Factory create(Provider<AppApiService> apiProvider,
      Provider<SecureStore> storeProvider) {
    return new DeviceRepository_Factory(apiProvider, storeProvider);
  }

  public static DeviceRepository newInstance(AppApiService api, SecureStore store) {
    return new DeviceRepository(api, store);
  }
}
