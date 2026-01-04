package com.haida.hubapp.data.local;

import androidx.annotation.NonNull;
import androidx.room.CoroutinesRoom;
import androidx.room.EntityInsertionAdapter;
import androidx.room.RoomDatabase;
import androidx.room.SharedSQLiteStatement;
import androidx.sqlite.db.SupportSQLiteStatement;
import java.lang.Class;
import java.lang.Exception;
import java.lang.Object;
import java.lang.Override;
import java.lang.String;
import java.lang.SuppressWarnings;
import java.util.Collections;
import java.util.List;
import java.util.concurrent.Callable;
import javax.annotation.processing.Generated;
import kotlin.Unit;
import kotlin.coroutines.Continuation;

@Generated("androidx.room.RoomProcessor")
@SuppressWarnings({"unchecked", "deprecation"})
public final class SyncChangeDao_Impl implements SyncChangeDao {
  private final RoomDatabase __db;

  private final EntityInsertionAdapter<SyncChangeEntity> __insertionAdapterOfSyncChangeEntity;

  private final SharedSQLiteStatement __preparedStmtOfDelete;

  public SyncChangeDao_Impl(@NonNull final RoomDatabase __db) {
    this.__db = __db;
    this.__insertionAdapterOfSyncChangeEntity = new EntityInsertionAdapter<SyncChangeEntity>(__db) {
      @Override
      @NonNull
      protected String createQuery() {
        return "INSERT OR REPLACE INTO `app_sync_changes` (`module`,`entity`,`id`,`action`,`payload`,`updatedAt`) VALUES (?,?,?,?,?,?)";
      }

      @Override
      protected void bind(@NonNull final SupportSQLiteStatement statement,
          @NonNull final SyncChangeEntity entity) {
        if (entity.getModule() == null) {
          statement.bindNull(1);
        } else {
          statement.bindString(1, entity.getModule());
        }
        if (entity.getEntity() == null) {
          statement.bindNull(2);
        } else {
          statement.bindString(2, entity.getEntity());
        }
        if (entity.getId() == null) {
          statement.bindNull(3);
        } else {
          statement.bindString(3, entity.getId());
        }
        if (entity.getAction() == null) {
          statement.bindNull(4);
        } else {
          statement.bindString(4, entity.getAction());
        }
        if (entity.getPayload() == null) {
          statement.bindNull(5);
        } else {
          statement.bindString(5, entity.getPayload());
        }
        if (entity.getUpdatedAt() == null) {
          statement.bindNull(6);
        } else {
          statement.bindString(6, entity.getUpdatedAt());
        }
      }
    };
    this.__preparedStmtOfDelete = new SharedSQLiteStatement(__db) {
      @Override
      @NonNull
      public String createQuery() {
        final String _query = "DELETE FROM app_sync_changes WHERE module = ? AND entity = ? AND id = ?";
        return _query;
      }
    };
  }

  @Override
  public Object upsert(final SyncChangeEntity change, final Continuation<? super Unit> arg1) {
    return CoroutinesRoom.execute(__db, true, new Callable<Unit>() {
      @Override
      @NonNull
      public Unit call() throws Exception {
        __db.beginTransaction();
        try {
          __insertionAdapterOfSyncChangeEntity.insert(change);
          __db.setTransactionSuccessful();
          return Unit.INSTANCE;
        } finally {
          __db.endTransaction();
        }
      }
    }, arg1);
  }

  @Override
  public Object delete(final String module, final String entity, final String id,
      final Continuation<? super Unit> arg3) {
    return CoroutinesRoom.execute(__db, true, new Callable<Unit>() {
      @Override
      @NonNull
      public Unit call() throws Exception {
        final SupportSQLiteStatement _stmt = __preparedStmtOfDelete.acquire();
        int _argIndex = 1;
        if (module == null) {
          _stmt.bindNull(_argIndex);
        } else {
          _stmt.bindString(_argIndex, module);
        }
        _argIndex = 2;
        if (entity == null) {
          _stmt.bindNull(_argIndex);
        } else {
          _stmt.bindString(_argIndex, entity);
        }
        _argIndex = 3;
        if (id == null) {
          _stmt.bindNull(_argIndex);
        } else {
          _stmt.bindString(_argIndex, id);
        }
        try {
          __db.beginTransaction();
          try {
            _stmt.executeUpdateDelete();
            __db.setTransactionSuccessful();
            return Unit.INSTANCE;
          } finally {
            __db.endTransaction();
          }
        } finally {
          __preparedStmtOfDelete.release(_stmt);
        }
      }
    }, arg3);
  }

  @NonNull
  public static List<Class<?>> getRequiredConverters() {
    return Collections.emptyList();
  }
}
