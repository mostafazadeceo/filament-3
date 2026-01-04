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
public final class SecureStoreImpl_Factory implements Factory<SecureStoreImpl> {
  private final Provider<Context> contextProvider;

  public SecureStoreImpl_Factory(Provider<Context> contextProvider) {
    this.contextProvider = contextProvider;
  }

  @Override
  public SecureStoreImpl get() {
    return newInstance(contextProvider.get());
  }

  public static SecureStoreImpl_Factory create(Provider<Context> contextProvider) {
    return new SecureStoreImpl_Factory(contextProvider);
  }

  public static SecureStoreImpl newInstance(Context context) {
    return new SecureStoreImpl(context);
  }
}
