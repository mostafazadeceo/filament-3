package com.haida.hubapp.data.repository;

import com.haida.hubapp.data.remote.AppApiService;
import com.haida.hubapp.security.PlayIntegrityManager;
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
public final class AuthRepository_Factory implements Factory<AuthRepository> {
  private final Provider<AppApiService> apiProvider;

  private final Provider<SecureStore> storeProvider;

  private final Provider<PlayIntegrityManager> playIntegrityManagerProvider;

  public AuthRepository_Factory(Provider<AppApiService> apiProvider,
      Provider<SecureStore> storeProvider,
      Provider<PlayIntegrityManager> playIntegrityManagerProvider) {
    this.apiProvider = apiProvider;
    this.storeProvider = storeProvider;
    this.playIntegrityManagerProvider = playIntegrityManagerProvider;
  }

  @Override
  public AuthRepository get() {
    return newInstance(apiProvider.get(), storeProvider.get(), playIntegrityManagerProvider.get());
  }

  public static AuthRepository_Factory create(Provider<AppApiService> apiProvider,
      Provider<SecureStore> storeProvider,
      Provider<PlayIntegrityManager> playIntegrityManagerProvider) {
    return new AuthRepository_Factory(apiProvider, storeProvider, playIntegrityManagerProvider);
  }

  public static AuthRepository newInstance(AppApiService api, SecureStore store,
      PlayIntegrityManager playIntegrityManager) {
    return new AuthRepository(api, store, playIntegrityManager);
  }
}
