package com.haida.hubapp.data.local;

import android.database.Cursor;
import android.os.CancellationSignal;
import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.room.CoroutinesRoom;
import androidx.room.EntityInsertionAdapter;
import androidx.room.RoomDatabase;
import androidx.room.RoomSQLiteQuery;
import androidx.room.util.CursorUtil;
import androidx.room.util.DBUtil;
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
public final class SyncCursorDao_Impl implements SyncCursorDao {
  private final RoomDatabase __db;

  private final EntityInsertionAdapter<SyncCursorEntity> __insertionAdapterOfSyncCursorEntity;

  public SyncCursorDao_Impl(@NonNull final RoomDatabase __db) {
    this.__db = __db;
    this.__insertionAdapterOfSyncCursorEntity = new EntityInsertionAdapter<SyncCursorEntity>(__db) {
      @Override
      @NonNull
      protected String createQuery() {
        return "INSERT OR REPLACE INTO `app_sync_cursors` (`module`,`cursor`,`updatedAt`) VALUES (?,?,?)";
      }

      @Override
      protected void bind(@NonNull final SupportSQLiteStatement statement,
          @NonNull final SyncCursorEntity entity) {
        if (entity.getModule() == null) {
          statement.bindNull(1);
        } else {
          statement.bindString(1, entity.getModule());
        }
        if (entity.getCursor() == null) {
          statement.bindNull(2);
        } else {
          statement.bindString(2, entity.getCursor());
        }
        if (entity.getUpdatedAt() == null) {
          statement.bindNull(3);
        } else {
          statement.bindString(3, entity.getUpdatedAt());
        }
      }
    };
  }

  @Override
  public Object upsert(final SyncCursorEntity cursor, final Continuation<? super Unit> arg1) {
    return CoroutinesRoom.execute(__db, true, new Callable<Unit>() {
      @Override
      @NonNull
      public Unit call() throws Exception {
        __db.beginTransaction();
        try {
          __insertionAdapterOfSyncCursorEntity.insert(cursor);
          __db.setTransactionSuccessful();
          return Unit.INSTANCE;
        } finally {
          __db.endTransaction();
        }
      }
    }, arg1);
  }

  @Override
  public Object get(final String module, final Continuation<? super SyncCursorEntity> arg1) {
    final String _sql = "SELECT * FROM app_sync_cursors WHERE module = ? LIMIT 1";
    final RoomSQLiteQuery _statement = RoomSQLiteQuery.acquire(_sql, 1);
    int _argIndex = 1;
    if (module == null) {
      _statement.bindNull(_argIndex);
    } else {
      _statement.bindString(_argIndex, module);
    }
    final CancellationSignal _cancellationSignal = DBUtil.createCancellationSignal();
    return CoroutinesRoom.execute(__db, false, _cancellationSignal, new Callable<SyncCursorEntity>() {
      @Override
      @Nullable
      public SyncCursorEntity call() throws Exception {
        final Cursor _cursor = DBUtil.query(__db, _statement, false, null);
        try {
          final int _cursorIndexOfModule = CursorUtil.getColumnIndexOrThrow(_cursor, "module");
          final int _cursorIndexOfCursor = CursorUtil.getColumnIndexOrThrow(_cursor, "cursor");
          final int _cursorIndexOfUpdatedAt = CursorUtil.getColumnIndexOrThrow(_cursor, "updatedAt");
          final SyncCursorEntity _result;
          if (_cursor.moveToFirst()) {
            final String _tmpModule;
            if (_cursor.isNull(_cursorIndexOfModule)) {
              _tmpModule = null;
            } else {
              _tmpModule = _cursor.getString(_cursorIndexOfModule);
            }
            final String _tmpCursor;
            if (_cursor.isNull(_cursorIndexOfCursor)) {
              _tmpCursor = null;
            } else {
              _tmpCursor = _cursor.getString(_cursorIndexOfCursor);
            }
            final String _tmpUpdatedAt;
            if (_cursor.isNull(_cursorIndexOfUpdatedAt)) {
              _tmpUpdatedAt = null;
            } else {
              _tmpUpdatedAt = _cursor.getString(_cursorIndexOfUpdatedAt);
            }
            _result = new SyncCursorEntity(_tmpModule,_tmpCursor,_tmpUpdatedAt);
          } else {
            _result = null;
          }
          return _result;
        } finally {
          _cursor.close();
          _statement.release();
        }
      }
    }, arg1);
  }

  @NonNull
  public static List<Class<?>> getRequiredConverters() {
    return Collections.emptyList();
  }
}
