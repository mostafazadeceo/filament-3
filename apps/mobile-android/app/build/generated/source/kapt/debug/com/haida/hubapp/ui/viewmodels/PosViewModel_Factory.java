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
public final class PosViewModel_Factory implements Factory<PosViewModel> {
  private final Provider<SyncRepository> syncRepositoryProvider;

  public PosViewModel_Factory(Provider<SyncRepository> syncRepositoryProvider) {
    this.syncRepositoryProvider = syncRepositoryProvider;
  }

  @Override
  public PosViewModel get() {
    return newInstance(syncRepositoryProvider.get());
  }

  public static PosViewModel_Factory create(Provider<SyncRepository> syncRepositoryProvider) {
    return new PosViewModel_Factory(syncRepositoryProvider);
  }

  public static PosViewModel newInstance(SyncRepository syncRepository) {
    return new PosViewModel(syncRepository);
  }
}
