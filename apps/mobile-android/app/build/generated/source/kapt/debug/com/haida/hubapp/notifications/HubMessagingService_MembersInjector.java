package com.haida.hubapp.notifications;

import com.haida.hubapp.data.repository.DeviceRepository;
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
public final class HubMessagingService_MembersInjector implements MembersInjector<HubMessagingService> {
  private final Provider<DeviceRepository> deviceRepositoryProvider;

  public HubMessagingService_MembersInjector(Provider<DeviceRepository> deviceRepositoryProvider) {
    this.deviceRepositoryProvider = deviceRepositoryProvider;
  }

  public static MembersInjector<HubMessagingService> create(
      Provider<DeviceRepository> deviceRepositoryProvider) {
    return new HubMessagingService_MembersInjector(deviceRepositoryProvider);
  }

  @Override
  public void injectMembers(HubMessagingService instance) {
    injectDeviceRepository(instance, deviceRepositoryProvider.get());
  }

  @InjectedFieldSignature("com.haida.hubapp.notifications.HubMessagingService.deviceRepository")
  public static void injectDeviceRepository(HubMessagingService instance,
      DeviceRepository deviceRepository) {
    instance.deviceRepository = deviceRepository;
  }
}
