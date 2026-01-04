package com.haida.hubapp.di;

import com.haida.hubapp.data.local.AppDatabase;
import com.haida.hubapp.data.local.SyncCursorDao;
import dagger.internal.DaggerGenerated;
import dagger.internal.Factory;
import dagger.internal.Preconditions;
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
public final class AppModule_ProvideCursorDaoFactory implements Factory<SyncCursorDao> {
  private final Provider<AppDatabase> dbProvider;

  public AppModule_ProvideCursorDaoFactory(Provider<AppDatabase> dbProvider) {
    this.dbProvider = dbProvider;
  }

  @Override
  public SyncCursorDao get() {
    return provideCursorDao(dbProvider.get());
  }

  public static AppModule_ProvideCursorDaoFactory create(Provider<AppDatabase> dbProvider) {
    return new AppModule_ProvideCursorDaoFactory(dbProvider);
  }

  public static SyncCursorDao provideCursorDao(AppDatabase db) {
    return Preconditions.checkNotNullFromProvides(AppModule.INSTANCE.provideCursorDao(db));
  }
}
