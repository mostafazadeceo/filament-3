package com.haida.hubapp.ui.viewmodels;

import com.haida.hubapp.data.repository.SyncRepository;
import dagger.internal.DaggerGenerated;
import dagger.internal.Factory;
import dagger.internal.QualifierMetadata;
import dagger.internal.ScopeMetadata;
import javax.annotation.processing.Generated;
import javax.inject.Provider;

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
public final class SupportViewModel_Factory implements Factory<SupportViewModel> {
  private final Provider<SyncRepository> syncRepositoryProvider;

  public SupportViewModel_Factory(Provider<SyncRepository> syncRepositoryProvider) {
    this.syncRepositoryProvider = syncRepositoryProvider;
  }

  @Override
  public SupportViewModel get() {
    return newInstance(syncRepositoryProvider.get());
  }

  public static SupportViewModel_Factory create(Provider<SyncRepository> syncRepositoryProvider) {
    return new SupportViewModel_Factory(syncRepositoryProvider);
  }

  public static SupportViewModel newInstance(SyncRepository syncRepository) {
    return new SupportViewModel(syncRepository);
  }
}
