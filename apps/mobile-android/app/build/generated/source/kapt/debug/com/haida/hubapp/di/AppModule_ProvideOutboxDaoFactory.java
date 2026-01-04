package com.haida.hubapp.di;

import com.haida.hubapp.data.local.AppDatabase;
import com.haida.hubapp.data.local.OutboxDao;
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
public final class AppModule_ProvideOutboxDaoFactory implements Factory<OutboxDao> {
  private final Provider<AppDatabase> dbProvider;

  public AppModule_ProvideOutboxDaoFactory(Provider<AppDatabase> dbProvider) {
    this.dbProvider = dbProvider;
  }

  @Override
  public OutboxDao get() {
    return provideOutboxDao(dbProvider.get());
  }

  public static AppModule_ProvideOutboxDaoFactory create(Provider<AppDatabase> dbProvider) {
    return new AppModule_ProvideOutboxDaoFactory(dbProvider);
  }

  public static OutboxDao provideOutboxDao(AppDatabase db) {
    return Preconditions.checkNotNullFromProvides(AppModule.INSTANCE.provideOutboxDao(db));
  }
}
