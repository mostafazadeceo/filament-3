package com.haida.hubapp.di;

import com.haida.hubapp.data.local.AppDatabase;
import com.haida.hubapp.data.local.SyncChangeDao;
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
public final class AppModule_ProvideChangeDaoFactory implements Factory<SyncChangeDao> {
  private final Provider<AppDatabase> dbProvider;

  public AppModule_ProvideChangeDaoFactory(Provider<AppDatabase> dbProvider) {
    this.dbProvider = dbProvider;
  }

  @Override
  public SyncChangeDao get() {
    return provideChangeDao(dbProvider.get());
  }

  public static AppModule_ProvideChangeDaoFactory create(Provider<AppDatabase> dbProvider) {
    return new AppModule_ProvideChangeDaoFactory(dbProvider);
  }

  public static SyncChangeDao provideChangeDao(AppDatabase db) {
    return Preconditions.checkNotNullFromProvides(AppModule.INSTANCE.provideChangeDao(db));
  }
}
