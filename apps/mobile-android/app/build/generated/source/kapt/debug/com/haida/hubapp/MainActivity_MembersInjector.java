package com.haida.hubapp;

import com.haida.hubapp.data.repository.DeviceRepository;
import com.haida.hubapp.sync.SyncScheduler;
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
public final class MainActivity_MembersInjector implements MembersInjector<MainActivity> {
  private final Provider<SyncScheduler> syncSchedulerProvider;

  private final Provider<DeviceRepository> deviceRepositoryProvider;

  public MainActivity_MembersInjector(Provider<SyncScheduler> syncSchedulerProvider,
      Provider<DeviceRepository> deviceRepositoryProvider) {
    this.syncSchedulerProvider = syncSchedulerProvider;
    this.deviceRepositoryProvider = deviceRepositoryProvider;
  }

  public static MembersInjector<MainActivity> create(Provider<SyncScheduler> syncSchedulerProvider,
      Provider<DeviceRepository> deviceRepositoryProvider) {
    return new MainActivity_MembersInjector(syncSchedulerProvider, deviceRepositoryProvider);
  }

  @Override
  public void injectMembers(MainActivity instance) {
    injectSyncScheduler(instance, syncSchedulerProvider.get());
    injectDeviceRepository(instance, deviceRepositoryProvider.get());
  }

  @InjectedFieldSignature("com.haida.hubapp.MainActivity.syncScheduler")
  public static void injectSyncScheduler(MainActivity instance, SyncScheduler syncScheduler) {
    instance.syncScheduler = syncScheduler;
  }

  @InjectedFieldSignature("com.haida.hubapp.MainActivity.deviceRepository")
  public static void injectDeviceRepository(MainActivity instance,
      DeviceRepository deviceRepository) {
    instance.deviceRepository = deviceRepository;
  }
}
