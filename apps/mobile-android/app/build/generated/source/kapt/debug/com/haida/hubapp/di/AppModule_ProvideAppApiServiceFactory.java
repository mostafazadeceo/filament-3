package com.haida.hubapp.di;

import com.haida.hubapp.data.remote.AppApiService;
import dagger.internal.DaggerGenerated;
import dagger.internal.Factory;
import dagger.internal.Preconditions;
import dagger.internal.QualifierMetadata;
import dagger.internal.ScopeMetadata;
import javax.annotation.processing.Generated;
import javax.inject.Provider;
import retrofit2.Retrofit;

@ScopeMetadata
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
public final class AppModule_ProvideAppApiServiceFactory implements Factory<AppApiService> {
  private final Provider<Retrofit> retrofitProvider;

  public AppModule_ProvideAppApiServiceFactory(Provider<Retrofit> retrofitProvider) {
    this.retrofitProvider = retrofitProvider;
  }

  @Override
  public AppApiService get() {
    return provideAppApiService(retrofitProvider.get());
  }

  public static AppModule_ProvideAppApiServiceFactory create(Provider<Retrofit> retrofitProvider) {
    return new AppModule_ProvideAppApiServiceFactory(retrofitProvider);
  }

  public static AppApiService provideAppApiService(Retrofit retrofit) {
    return Preconditions.checkNotNullFromProvides(AppModule.INSTANCE.provideAppApiService(retrofit));
  }
}
