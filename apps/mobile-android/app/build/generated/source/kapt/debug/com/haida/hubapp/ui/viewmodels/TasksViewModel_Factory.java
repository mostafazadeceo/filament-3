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
public final class TasksViewModel_Factory implements Factory<TasksViewModel> {
  private final Provider<SyncRepository> syncRepositoryProvider;

  public TasksViewModel_Factory(Provider<SyncRepository> syncRepositoryProvider) {
    this.syncRepositoryProvider = syncRepositoryProvider;
  }

  @Override
  public TasksViewModel get() {
    return newInstance(syncRepositoryProvider.get());
  }

  public static TasksViewModel_Factory create(Provider<SyncRepository> syncRepositoryProvider) {
    return new TasksViewModel_Factory(syncRepositoryProvider);
  }

  public static TasksViewModel newInstance(SyncRepository syncRepository) {
    return new TasksViewModel(syncRepository);
  }
}
