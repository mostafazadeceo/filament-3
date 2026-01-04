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
public final class HrViewModel_Factory implements Factory<HrViewModel> {
  private final Provider<SyncRepository> syncRepositoryProvider;

  public HrViewModel_Factory(Provider<SyncRepository> syncRepositoryProvider) {
    this.syncRepositoryProvider = syncRepositoryProvider;
  }

  @Override
  public HrViewModel get() {
    return newInstance(syncRepositoryProvider.get());
  }

  public static HrViewModel_Factory create(Provider<SyncRepository> syncRepositoryProvider) {
    return new HrViewModel_Factory(syncRepositoryProvider);
  }

  public static HrViewModel newInstance(SyncRepository syncRepository) {
    return new HrViewModel(syncRepository);
  }
}
