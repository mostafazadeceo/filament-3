package com.haida.hubapp.data.local;

import android.database.Cursor;
import android.os.CancellationSignal;
import androidx.annotation.NonNull;
import androidx.room.CoroutinesRoom;
import androidx.room.EntityInsertionAdapter;
import androidx.room.RoomDatabase;
import androidx.room.RoomSQLiteQuery;
import androidx.room.SharedSQLiteStatement;
import androidx.room.util.CursorUtil;
import androidx.room.util.DBUtil;
import androidx.sqlite.db.SupportSQLiteStatement;
import java.lang.Class;
import java.lang.Exception;
import java.lang.Object;
import java.lang.Override;
import java.lang.String;
import java.lang.SuppressWarnings;
import java.util.ArrayList;
import java.util.Collections;
import java.util.List;
import java.util.concurrent.Callable;
import javax.annotation.processing.Generated;
import kotlin.Unit;
import kotlin.coroutines.Continuation;

@Generated("androidx.room.RoomProcessor")
@SuppressWarnings({"unchecked", "deprecation"})
public final class OutboxDao_Impl implements OutboxDao {
  private final RoomDatabase __db;

  private final EntityInsertionAdapter<OutboxEntity> __insertionAdapterOfOutboxEntity;

  private final SharedSQLiteStatement __preparedStmtOfUpdateStatus;

  public OutboxDao_Impl(@NonNull final RoomDatabase __db) {
    this.__db = __db;
    this.__insertionAdapterOfOutboxEntity = new EntityInsertionAdapter<OutboxEntity>(__db) {
      @Override
      @NonNull
      protected String createQuery() {
        return "INSERT OR REPLACE INTO `app_outbox` (`id`,`module`,`action`,`payload`,`status`,`retries`,`idempotencyKey`,`updatedAt`) VALUES (?,?,?,?,?,?,?,?)";
      }

      @Override
      protected void bind(@NonNull final SupportSQLiteStatement statement,
          @NonNull final OutboxEntity entity) {
        if (entity.getId() == null) {
          statement.bindNull(1);
        } else {
          statement.bindString(1, entity.getId());
        }
        if (entity.getModule() == null) {
          statement.bindNull(2);
        } else {
          statement.bindString(2, entity.getModule());
        }
        if (entity.getAction() == null) {
          statement.bindNull(3);
        } else {
          statement.bindString(3, entity.getAction());
        }
        if (entity.getPayload() == null) {
          statement.bindNull(4);
        } else {
          statement.bindString(4, entity.getPayload());
        }
        if (entity.getStatus() == null) {
          statement.bindNull(5);
        } else {
          statement.bindString(5, entity.getStatus());
        }
        statement.bindLong(6, entity.getRetries());
        if (entity.getIdempotencyKey() == null) {
          statement.bindNull(7);
        } else {
          statement.bindString(7, entity.getIdempotencyKey());
        }
        if (entity.getUpdatedAt() == null) {
          statement.bindNull(8);
        } else {
          statement.bindString(8, entity.getUpdatedAt());
        }
      }
    };
    this.__preparedStmtOfUpdateStatus = new SharedSQLiteStatement(__db) {
      @Override
      @NonNull
      public String createQuery() {
        final String _query = "UPDATE app_outbox SET status = ?, retries = ?, updatedAt = ? WHERE id = ?";
        return _query;
      }
    };
  }

  @Override
  public Object upsert(final OutboxEntity item, final Continuation<? super Unit> arg1) {
    return CoroutinesRoom.execute(__db, true, new Callable<Unit>() {
      @Override
      @NonNull
      public Unit call() throws Exception {
        __db.beginTransaction();
        try {
          __insertionAdapterOfOutboxEntity.insert(item);
          __db.setTransactionSuccessful();
          return Unit.INSTANCE;
        } finally {
          __db.endTransaction();
        }
      }
    }, arg1);
  }

  @Override
  public Object updateStatus(final String id, final String status, final int retries,
      final String updatedAt, final Continuation<? super Unit> arg4) {
    return CoroutinesRoom.execute(__db, true, new Callable<Unit>() {
      @Override
      @NonNull
      public Unit call() throws Exception {
        final SupportSQLiteStatement _stmt = __preparedStmtOfUpdateStatus.acquire();
        int _argIndex = 1;
        if (status == null) {
          _stmt.bindNull(_argIndex);
        } else {
          _stmt.bindString(_argIndex, status);
        }
        _argIndex = 2;
        _stmt.bindLong(_argIndex, retries);
        _argIndex = 3;
        if (updatedAt == null) {
          _stmt.bindNull(_argIndex);
        } else {
          _stmt.bindString(_argIndex, updatedAt);
        }
        _argIndex = 4;
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
          __preparedStmtOfUpdateStatus.release(_stmt);
        }
      }
    }, arg4);
  }

  @Override
  public Object pending(final Continuation<? super List<OutboxEntity>> arg0) {
    final String _sql = "SELECT * FROM app_outbox WHERE status IN ('pending','failed') ORDER BY updatedAt ASC";
    final RoomSQLiteQuery _statement = RoomSQLiteQuery.acquire(_sql, 0);
    final CancellationSignal _cancellationSignal = DBUtil.createCancellationSignal();
    return CoroutinesRoom.execute(__db, false, _cancellationSignal, new Callable<List<OutboxEntity>>() {
      @Override
      @NonNull
      public List<OutboxEntity> call() throws Exception {
        final Cursor _cursor = DBUtil.query(__db, _statement, false, null);
        try {
          final int _cursorIndexOfId = CursorUtil.getColumnIndexOrThrow(_cursor, "id");
          final int _cursorIndexOfModule = CursorUtil.getColumnIndexOrThrow(_cursor, "module");
          final int _cursorIndexOfAction = CursorUtil.getColumnIndexOrThrow(_cursor, "action");
          final int _cursorIndexOfPayload = CursorUtil.getColumnIndexOrThrow(_cursor, "payload");
          final int _cursorIndexOfStatus = CursorUtil.getColumnIndexOrThrow(_cursor, "status");
          final int _cursorIndexOfRetries = CursorUtil.getColumnIndexOrThrow(_cursor, "retries");
          final int _cursorIndexOfIdempotencyKey = CursorUtil.getColumnIndexOrThrow(_cursor, "idempotencyKey");
          final int _cursorIndexOfUpdatedAt = CursorUtil.getColumnIndexOrThrow(_cursor, "updatedAt");
          final List<OutboxEntity> _result = new ArrayList<OutboxEntity>(_cursor.getCount());
          while (_cursor.moveToNext()) {
            final OutboxEntity _item;
            final String _tmpId;
            if (_cursor.isNull(_cursorIndexOfId)) {
              _tmpId = null;
            } else {
              _tmpId = _cursor.getString(_cursorIndexOfId);
            }
            final String _tmpModule;
            if (_cursor.isNull(_cursorIndexOfModule)) {
              _tmpModule = null;
            } else {
              _tmpModule = _cursor.getString(_cursorIndexOfModule);
            }
            final String _tmpAction;
            if (_cursor.isNull(_cursorIndexOfAction)) {
              _tmpAction = null;
            } else {
              _tmpAction = _cursor.getString(_cursorIndexOfAction);
            }
            final String _tmpPayload;
            if (_cursor.isNull(_cursorIndexOfPayload)) {
              _tmpPayload = null;
            } else {
              _tmpPayload = _cursor.getString(_cursorIndexOfPayload);
            }
            final String _tmpStatus;
            if (_cursor.isNull(_cursorIndexOfStatus)) {
              _tmpStatus = null;
            } else {
              _tmpStatus = _cursor.getString(_cursorIndexOfStatus);
            }
            final int _tmpRetries;
            _tmpRetries = _cursor.getInt(_cursorIndexOfRetries);
            final String _tmpIdempotencyKey;
            if (_cursor.isNull(_cursorIndexOfIdempotencyKey)) {
              _tmpIdempotencyKey = null;
            } else {
              _tmpIdempotencyKey = _cursor.getString(_cursorIndexOfIdempotencyKey);
            }
            final String _tmpUpdatedAt;
            if (_cursor.isNull(_cursorIndexOfUpdatedAt)) {
              _tmpUpdatedAt = null;
            } else {
              _tmpUpdatedAt = _cursor.getString(_cursorIndexOfUpdatedAt);
            }
            _item = new OutboxEntity(_tmpId,_tmpModule,_tmpAction,_tmpPayload,_tmpStatus,_tmpRetries,_tmpIdempotencyKey,_tmpUpdatedAt);
            _result.add(_item);
          }
          return _result;
        } finally {
          _cursor.close();
          _statement.release();
        }
      }
    }, arg0);
  }

  @NonNull
  public static List<Class<?>> getRequiredConverters() {
    return Collections.emptyList();
  }
}
