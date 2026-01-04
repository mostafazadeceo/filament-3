package com.haida.hubapp.security;

import android.content.Context;
import dagger.internal.DaggerGenerated;
import dagger.internal.Factory;
import dagger.internal.QualifierMetadata;
import dagger.internal.ScopeMetadata;
import javax.annotation.processing.Generated;
import javax.inject.Provider;

@ScopeMetadata("javax.inject.Singleton")
@QualifierMetadata("dagger.hilt.android.qualifiers.ApplicationContext")
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
public final class PlayIntegrityManager_Factory implements Factory<PlayIntegrityManager> {
  private final Provider<Context> contextProvider;

  public PlayIntegrityManager_Factory(Provider<Context> contextProvider) {
    this.contextProvider = contextProvider;
  }

  @Override
  public PlayIntegrityManager get() {
    return newInstance(contextProvider.get());
  }

  public static PlayIntegrityManager_Factory create(Provider<Context> contextProvider) {
    return new PlayIntegrityManager_Factory(contextProvider);
  }

  public static PlayIntegrityManager newInstance(Context context) {
    return new PlayIntegrityManager(context);
  }
}
