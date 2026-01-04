package com.haida.hubapp.data.local;

import androidx.annotation.NonNull;
import androidx.room.DatabaseConfiguration;
import androidx.room.InvalidationTracker;
import androidx.room.RoomDatabase;
import androidx.room.RoomOpenHelper;
import androidx.room.migration.AutoMigrationSpec;
import androidx.room.migration.Migration;
import androidx.room.util.DBUtil;
import androidx.room.util.TableInfo;
import androidx.sqlite.db.SupportSQLiteDatabase;
import androidx.sqlite.db.SupportSQLiteOpenHelper;
import java.lang.Class;
import java.lang.Override;
import java.lang.String;
import java.lang.SuppressWarnings;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.HashSet;
import java.util.List;
import java.util.Map;
import java.util.Set;
import javax.annotation.processing.Generated;

@Generated("androidx.room.RoomProcessor")
@SuppressWarnings({"unchecked", "deprecation"})
public final class AppDatabase_Impl extends AppDatabase {
  private volatile OutboxDao _outboxDao;

  private volatile SyncCursorDao _syncCursorDao;

  private volatile SyncChangeDao _syncChangeDao;

  @Override
  @NonNull
  protected SupportSQLiteOpenHelper createOpenHelper(@NonNull final DatabaseConfiguration config) {
    final SupportSQLiteOpenHelper.Callback _openCallback = new RoomOpenHelper(config, new RoomOpenHelper.Delegate(2) {
      @Override
      public void createAllTables(@NonNull final SupportSQLiteDatabase db) {
        db.execSQL("CREATE TABLE IF NOT EXISTS `app_outbox` (`id` TEXT NOT NULL, `module` TEXT NOT NULL, `action` TEXT NOT NULL, `payload` TEXT NOT NULL, `status` TEXT NOT NULL, `retries` INTEGER NOT NULL, `idempotencyKey` TEXT NOT NULL, `updatedAt` TEXT NOT NULL, PRIMARY KEY(`id`))");
        db.execSQL("CREATE TABLE IF NOT EXISTS `app_sync_cursors` (`module` TEXT NOT NULL, `cursor` TEXT NOT NULL, `updatedAt` TEXT NOT NULL, PRIMARY KEY(`module`))");
        db.execSQL("CREATE TABLE IF NOT EXISTS `app_sync_changes` (`module` TEXT NOT NULL, `entity` TEXT NOT NULL, `id` TEXT NOT NULL, `action` TEXT NOT NULL, `payload` TEXT NOT NULL, `updatedAt` TEXT NOT NULL, PRIMARY KEY(`module`, `entity`, `id`))");
        db.execSQL("CREATE TABLE IF NOT EXISTS room_master_table (id INTEGER PRIMARY KEY,identity_hash TEXT)");
        db.execSQL("INSERT OR REPLACE INTO room_master_table (id,identity_hash) VALUES(42, '0d280b6161e243e652a655086c3a9e23')");
      }

      @Override
      public void dropAllTables(@NonNull final SupportSQLiteDatabase db) {
        db.execSQL("DROP TABLE IF EXISTS `app_outbox`");
        db.execSQL("DROP TABLE IF EXISTS `app_sync_cursors`");
        db.execSQL("DROP TABLE IF EXISTS `app_sync_changes`");
        final List<? extends RoomDatabase.Callback> _callbacks = mCallbacks;
        if (_callbacks != null) {
          for (RoomDatabase.Callback _callback : _callbacks) {
            _callback.onDestructiveMigration(db);
          }
        }
      }

      @Override
      public void onCreate(@NonNull final SupportSQLiteDatabase db) {
        final List<? extends RoomDatabase.Callback> _callbacks = mCallbacks;
        if (_callbacks != null) {
          for (RoomDatabase.Callback _callback : _callbacks) {
            _callback.onCreate(db);
          }
        }
      }

      @Override
      public void onOpen(@NonNull final SupportSQLiteDatabase db) {
        mDatabase = db;
        internalInitInvalidationTracker(db);
        final List<? extends RoomDatabase.Callback> _callbacks = mCallbacks;
        if (_callbacks != null) {
          for (RoomDatabase.Callback _callback : _callbacks) {
            _callback.onOpen(db);
          }
        }
      }

      @Override
      public void onPreMigrate(@NonNull final SupportSQLiteDatabase db) {
        DBUtil.dropFtsSyncTriggers(db);
      }

      @Override
      public void onPostMigrate(@NonNull final SupportSQLiteDatabase db) {
      }

      @Override
      @NonNull
      public RoomOpenHelper.ValidationResult onValidateSchema(
          @NonNull final SupportSQLiteDatabase db) {
        final HashMap<String, TableInfo.Column> _columnsAppOutbox = new HashMap<String, TableInfo.Column>(8);
        _columnsAppOutbox.put("id", new TableInfo.Column("id", "TEXT", true, 1, null, TableInfo.CREATED_FROM_ENTITY));
        _columnsAppOutbox.put("module", new TableInfo.Column("module", "TEXT", true, 0, null, TableInfo.CREATED_FROM_ENTITY));
        _columnsAppOutbox.put("action", new TableInfo.Column("action", "TEXT", true, 0, null, TableInfo.CREATED_FROM_ENTITY));
        _columnsAppOutbox.put("payload", new TableInfo.Column("payload", "TEXT", true, 0, null, TableInfo.CREATED_FROM_ENTITY));
        _columnsAppOutbox.put("status", new TableInfo.Column("status", "TEXT", true, 0, null, TableInfo.CREATED_FROM_ENTITY));
        _columnsAppOutbox.put("retries", new TableInfo.Column("retries", "INTEGER", true, 0, null, TableInfo.CREATED_FROM_ENTITY));
        _columnsAppOutbox.put("idempotencyKey", new TableInfo.Column("idempotencyKey", "TEXT", true, 0, null, TableInfo.CREATED_FROM_ENTITY));
        _columnsAppOutbox.put("updatedAt", new TableInfo.Column("updatedAt", "TEXT", true, 0, null, TableInfo.CREATED_FROM_ENTITY));
        final HashSet<TableInfo.ForeignKey> _foreignKeysAppOutbox = new HashSet<TableInfo.ForeignKey>(0);
        final HashSet<TableInfo.Index> _indicesAppOutbox = new HashSet<TableInfo.Index>(0);
        final TableInfo _infoAppOutbox = new TableInfo("app_outbox", _columnsAppOutbox, _foreignKeysAppOutbox, _indicesAppOutbox);
        final TableInfo _existingAppOutbox = TableInfo.read(db, "app_outbox");
        if (!_infoAppOutbox.equals(_existingAppOutbox)) {
          return new RoomOpenHelper.ValidationResult(false, "app_outbox(com.haida.hubapp.data.local.OutboxEntity).\n"
                  + " Expected:\n" + _infoAppOutbox + "\n"
                  + " Found:\n" + _existingAppOutbox);
        }
        final HashMap<String, TableInfo.Column> _columnsAppSyncCursors = new HashMap<String, TableInfo.Column>(3);
        _columnsAppSyncCursors.put("module", new TableInfo.Column("module", "TEXT", true, 1, null, TableInfo.CREATED_FROM_ENTITY));
        _columnsAppSyncCursors.put("cursor", new TableInfo.Column("cursor", "TEXT", true, 0, null, TableInfo.CREATED_FROM_ENTITY));
        _columnsAppSyncCursors.put("updatedAt", new TableInfo.Column("updatedAt", "TEXT", true, 0, null, TableInfo.CREATED_FROM_ENTITY));
        final HashSet<TableInfo.ForeignKey> _foreignKeysAppSyncCursors = new HashSet<TableInfo.ForeignKey>(0);
        final HashSet<TableInfo.Index> _indicesAppSyncCursors = new HashSet<TableInfo.Index>(0);
        final TableInfo _infoAppSyncCursors = new TableInfo("app_sync_cursors", _columnsAppSyncCursors, _foreignKeysAppSyncCursors, _indicesAppSyncCursors);
        final TableInfo _existingAppSyncCursors = TableInfo.read(db, "app_sync_cursors");
        if (!_infoAppSyncCursors.equals(_existingAppSyncCursors)) {
          return new RoomOpenHelper.ValidationResult(false, "app_sync_cursors(com.haida.hubapp.data.local.SyncCursorEntity).\n"
                  + " Expected:\n" + _infoAppSyncCursors + "\n"
                  + " Found:\n" + _existingAppSyncCursors);
        }
        final HashMap<String, TableInfo.Column> _columnsAppSyncChanges = new HashMap<String, TableInfo.Column>(6);
        _columnsAppSyncChanges.put("module", new TableInfo.Column("module", "TEXT", true, 1, null, TableInfo.CREATED_FROM_ENTITY));
        _columnsAppSyncChanges.put("entity", new TableInfo.Column("entity", "TEXT", true, 2, null, TableInfo.CREATED_FROM_ENTITY));
        _columnsAppSyncChanges.put("id", new TableInfo.Column("id", "TEXT", true, 3, null, TableInfo.CREATED_FROM_ENTITY));
        _columnsAppSyncChanges.put("action", new TableInfo.Column("action", "TEXT", true, 0, null, TableInfo.CREATED_FROM_ENTITY));
        _columnsAppSyncChanges.put("payload", new TableInfo.Column("payload", "TEXT", true, 0, null, TableInfo.CREATED_FROM_ENTITY));
        _columnsAppSyncChanges.put("updatedAt", new TableInfo.Column("updatedAt", "TEXT", true, 0, null, TableInfo.CREATED_FROM_ENTITY));
        final HashSet<TableInfo.ForeignKey> _foreignKeysAppSyncChanges = new HashSet<TableInfo.ForeignKey>(0);
        final HashSet<TableInfo.Index> _indicesAppSyncChanges = new HashSet<TableInfo.Index>(0);
        final TableInfo _infoAppSyncChanges = new TableInfo("app_sync_changes", _columnsAppSyncChanges, _foreignKeysAppSyncChanges, _indicesAppSyncChanges);
        final TableInfo _existingAppSyncChanges = TableInfo.read(db, "app_sync_changes");
        if (!_infoAppSyncChanges.equals(_existingAppSyncChanges)) {
          return new RoomOpenHelper.ValidationResult(false, "app_sync_changes(com.haida.hubapp.data.local.SyncChangeEntity).\n"
                  + " Expected:\n" + _infoAppSyncChanges + "\n"
                  + " Found:\n" + _existingAppSyncChanges);
        }
        return new RoomOpenHelper.ValidationResult(true, null);
      }
    }, "0d280b6161e243e652a655086c3a9e23", "b76cdd2183e1d4219c81f37e45ed893e");
    final SupportSQLiteOpenHelper.Configuration _sqliteConfig = SupportSQLiteOpenHelper.Configuration.builder(config.context).name(config.name).callback(_openCallback).build();
    final SupportSQLiteOpenHelper _helper = config.sqliteOpenHelperFactory.create(_sqliteConfig);
    return _helper;
  }

  @Override
  @NonNull
  protected InvalidationTracker createInvalidationTracker() {
    final HashMap<String, String> _shadowTablesMap = new HashMap<String, String>(0);
    final HashMap<String, Set<String>> _viewTables = new HashMap<String, Set<String>>(0);
    return new InvalidationTracker(this, _shadowTablesMap, _viewTables, "app_outbox","app_sync_cursors","app_sync_changes");
  }

  @Override
  public void clearAllTables() {
    super.assertNotMainThread();
    final SupportSQLiteDatabase _db = super.getOpenHelper().getWritableDatabase();
    try {
      super.beginTransaction();
      _db.execSQL("DELETE FROM `app_outbox`");
      _db.execSQL("DELETE FROM `app_sync_cursors`");
      _db.execSQL("DELETE FROM `app_sync_changes`");
      super.setTransactionSuccessful();
    } finally {
      super.endTransaction();
      _db.query("PRAGMA wal_checkpoint(FULL)").close();
      if (!_db.inTransaction()) {
        _db.execSQL("VACUUM");
      }
    }
  }

  @Override
  @NonNull
  protected Map<Class<?>, List<Class<?>>> getRequiredTypeConverters() {
    final HashMap<Class<?>, List<Class<?>>> _typeConvertersMap = new HashMap<Class<?>, List<Class<?>>>();
    _typeConvertersMap.put(OutboxDao.class, OutboxDao_Impl.getRequiredConverters());
    _typeConvertersMap.put(SyncCursorDao.class, SyncCursorDao_Impl.getRequiredConverters());
    _typeConvertersMap.put(SyncChangeDao.class, SyncChangeDao_Impl.getRequiredConverters());
    return _typeConvertersMap;
  }

  @Override
  @NonNull
  public Set<Class<? extends AutoMigrationSpec>> getRequiredAutoMigrationSpecs() {
    final HashSet<Class<? extends AutoMigrationSpec>> _autoMigrationSpecsSet = new HashSet<Class<? extends AutoMigrationSpec>>();
    return _autoMigrationSpecsSet;
  }

  @Override
  @NonNull
  public List<Migration> getAutoMigrations(
      @NonNull final Map<Class<? extends AutoMigrationSpec>, AutoMigrationSpec> autoMigrationSpecs) {
    final List<Migration> _autoMigrations = new ArrayList<Migration>();
    return _autoMigrations;
  }

  @Override
  public OutboxDao outboxDao() {
    if (_outboxDao != null) {
      return _outboxDao;
    } else {
      synchronized(this) {
        if(_outboxDao == null) {
          _outboxDao = new OutboxDao_Impl(this);
        }
        return _outboxDao;
      }
    }
  }

  @Override
  public SyncCursorDao syncCursorDao() {
    if (_syncCursorDao != null) {
      return _syncCursorDao;
    } else {
      synchronized(this) {
        if(_syncCursorDao == null) {
          _syncCursorDao = new SyncCursorDao_Impl(this);
        }
        return _syncCursorDao;
      }
    }
  }

  @Override
  public SyncChangeDao syncChangeDao() {
    if (_syncChangeDao != null) {
      return _syncChangeDao;
    } else {
      synchronized(this) {
        if(_syncChangeDao == null) {
          _syncChangeDao = new SyncChangeDao_Impl(this);
        }
        return _syncChangeDao;
      }
    }
  }
}
