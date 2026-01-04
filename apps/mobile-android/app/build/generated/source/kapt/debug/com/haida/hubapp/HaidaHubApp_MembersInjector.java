package com.haida.hubapp;

import androidx.hilt.work.HiltWorkerFactory;
import dagger.MembersInjector;
import dagger.internal.DaggerGenerated;
import dagger.internal.InjectedFieldSignature;
import dagger.internal.QualifierMetadata;
import javax.annotation.processing.Generated;
import javax.inject.Provider;

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
public final class HaidaHubApp_MembersInjector implements MembersInjector<HaidaHubApp> {
  private final Provider<HiltWorkerFactory> workerFactoryProvider;

  public HaidaHubApp_MembersInjector(Provider<HiltWorkerFactory> workerFactoryProvider) {
    this.workerFactoryProvider = workerFactoryProvider;
  }

  public static MembersInjector<HaidaHubApp> create(
      Provider<HiltWorkerFactory> workerFactoryProvider) {
    return new HaidaHubApp_MembersInjector(workerFactoryProvider);
  }

  @Override
  public void injectMembers(HaidaHubApp instance) {
    injectWorkerFactory(instance, workerFactoryProvider.get());
  }

  @InjectedFieldSignature("com.haida.hubapp.HaidaHubApp.workerFactory")
  public static void injectWorkerFactory(HaidaHubApp instance, HiltWorkerFactory workerFactory) {
    instance.workerFactory = workerFactory;
  }
}
