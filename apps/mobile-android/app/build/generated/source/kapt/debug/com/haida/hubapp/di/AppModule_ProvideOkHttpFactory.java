package com.haida.hubapp.di;

import com.haida.hubapp.security.SecureStore;
import dagger.internal.DaggerGenerated;
import dagger.internal.Factory;
import dagger.internal.Preconditions;
import dagger.internal.QualifierMetadata;
import dagger.internal.ScopeMetadata;
import javax.annotation.processing.Generated;
import javax.inject.Provider;
import okhttp3.OkHttpClient;

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
public final class AppModule_ProvideOkHttpFactory implements Factory<OkHttpClient> {
  private final Provider<SecureStore> storeProvider;

  public AppModule_ProvideOkHttpFactory(Provider<SecureStore> storeProvider) {
    this.storeProvider = storeProvider;
  }

  @Override
  public OkHttpClient get() {
    return provideOkHttp(storeProvider.get());
  }

  public static AppModule_ProvideOkHttpFactory create(Provider<SecureStore> storeProvider) {
    return new AppModule_ProvideOkHttpFactory(storeProvider);
  }

  public static OkHttpClient provideOkHttp(SecureStore store) {
    return Preconditions.checkNotNullFromProvides(AppModule.INSTANCE.provideOkHttp(store));
  }
}
