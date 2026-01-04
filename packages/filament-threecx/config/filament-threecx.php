<?php

return [
    'tables' => [
        'instances' => 'threecx_instances',
        'token_caches' => 'threecx_token_caches',
        'sync_cursors' => 'threecx_sync_cursors',
        'call_logs' => 'threecx_call_logs',
        'contacts' => 'threecx_contacts',
        'api_audit_logs' => 'threecx_api_audit_logs',
    ],

    'features' => [
        'xapi_enabled' => (bool) env('THREECX_XAPI_ENABLED', true),
        'call_control_enabled' => (bool) env('THREECX_CALL_CONTROL_ENABLED', false),
        'crm_connector_enabled' => (bool) env('THREECX_CRM_CONNECTOR_ENABLED', false),
        'api_explorer_enabled' => (bool) env('THREECX_API_EXPLORER_ENABLED', false),
        'websocket_listener_enabled' => (bool) env('THREECX_WS_LISTENER_ENABLED', false),
    ],

    'cache' => [
        'enabled' => (bool) env('THREECX_CACHE_ENABLED', true),
        'store' => env('THREECX_CACHE_STORE', null),
        'db_fallback' => (bool) env('THREECX_CACHE_DB_FALLBACK', true),
    ],

    'auth' => [
        'token_path' => env('THREECX_AUTH_TOKEN_PATH', '/connect/token'),
        'grant_type' => env('THREECX_AUTH_GRANT_TYPE', 'client_credentials'),
        'client_auth' => env('THREECX_AUTH_CLIENT_AUTH', 'basic'),
        'scopes' => [
            'xapi' => env('THREECX_SCOPE_XAPI', 'xapi'),
            'call_control' => env('THREECX_SCOPE_CALL_CONTROL', 'call_control'),
        ],
    ],

    'xapi' => [
        'base_path' => env('THREECX_XAPI_BASE_PATH', '/xapi'),
        'health_path' => env('THREECX_XAPI_HEALTH_PATH', '/health'),
        'version_path' => env('THREECX_XAPI_VERSION_PATH', '/version'),
        'capabilities_path' => env('THREECX_XAPI_CAPABILITIES_PATH', '/capabilities'),
        'contacts_path' => env('THREECX_XAPI_CONTACTS_PATH', '/contacts'),
        'call_history_path' => env('THREECX_XAPI_CALL_HISTORY_PATH', '/call-history'),
        'chat_history_path' => env('THREECX_XAPI_CHAT_HISTORY_PATH', '/chat-history'),
    ],

    'call_control' => [
        'base_path' => env('THREECX_CALL_CONTROL_BASE_PATH', '/call-control'),
        'entities_path' => env('THREECX_CALL_CONTROL_ENTITIES_PATH', '/entities'),
        'dn_state_path' => env('THREECX_CALL_CONTROL_DN_STATE_PATH', '/dn/{dn}'),
        'calls_path' => env('THREECX_CALL_CONTROL_CALLS_PATH', '/calls'),
        'call_transfer_path' => env('THREECX_CALL_CONTROL_TRANSFER_PATH', '/calls/{call}/transfer'),
        'call_terminate_path' => env('THREECX_CALL_CONTROL_TERMINATE_PATH', '/calls/{call}'),
        'from_key' => env('THREECX_CALL_CONTROL_FROM_KEY', 'from'),
        'to_key' => env('THREECX_CALL_CONTROL_TO_KEY', 'to'),
    ],

    'http' => [
        'timeout_seconds' => (int) env('THREECX_HTTP_TIMEOUT', 30),
        'retry_times' => (int) env('THREECX_HTTP_RETRY_TIMES', 2),
        'retry_sleep_ms' => (int) env('THREECX_HTTP_RETRY_SLEEP', 500),
        'user_agent' => env('THREECX_HTTP_USER_AGENT', 'Haida-ThreeCx/1.0'),
    ],

    'rate_limit' => [
        'max_requests' => (int) env('THREECX_RATE_MAX', 60),
        'per_seconds' => (int) env('THREECX_RATE_SECONDS', 1),
    ],

    'api' => [
        'rate_limit' => env('THREECX_API_RATE_LIMIT', '60,1'),
    ],

    'crm_connector' => [
        'enabled' => (bool) env('THREECX_CRM_CONNECTOR_ENABLED', false),
        'auth_mode' => env('THREECX_CRM_AUTH_MODE', 'connector_key'),
        'connector_key_header' => env('THREECX_CRM_KEY_HEADER', 'X-ThreeCX-Connector-Key'),
        'instance_param' => env('THREECX_CRM_INSTANCE_PARAM', 'instance_id'),
        'tenant_param' => env('THREECX_CRM_TENANT_PARAM', 'tenant_id'),
        'rate_limit' => env('THREECX_CRM_RATE_LIMIT', '30,1'),
        'max_results' => (int) env('THREECX_CRM_MAX_RESULTS', 10),
        'store_raw_payload' => (bool) env('THREECX_CRM_STORE_RAW', false),
        'store_call_raw_payload' => (bool) env('THREECX_CRM_STORE_CALL_RAW', false),
        'store_chat_raw_payload' => (bool) env('THREECX_CRM_STORE_CHAT_RAW', false),
    ],

    'api_explorer' => [
        'enabled' => (bool) env('THREECX_API_EXPLORER_ENABLED', false),
        'denylist' => [
            'recording',
            'audio',
            'monitor',
            'stream',
            'listen',
            'whisper',
            'barge',
            'spy',
        ],
        'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'],
        'max_body_bytes' => (int) env('THREECX_API_EXPLORER_MAX_BODY', 65536),
    ],

    'sync' => [
        'batch_size' => (int) env('THREECX_SYNC_BATCH', 100),
        'store_raw_payload' => (bool) env('THREECX_SYNC_STORE_RAW', false),
    ],

    'openapi_cache' => [
        'enabled' => (bool) env('THREECX_OPENAPI_CACHE_ENABLED', false),
        'path' => env('THREECX_OPENAPI_PATH', '/openapi.json'),
        'ttl_seconds' => (int) env('THREECX_OPENAPI_TTL', 3600),
    ],

    'notifications' => [
        'panel' => env('THREECX_NOTIFY_PANEL', 'tenant'),
    ],

    'logging' => [
        'enabled' => (bool) env('THREECX_LOGGING_ENABLED', true),
        'redact_request_body' => (bool) env('THREECX_LOG_REDACT_REQUEST', true),
        'redact_response_body' => (bool) env('THREECX_LOG_REDACT_RESPONSE', true),
    ],

    'retention' => [
        'call_logs_days' => (int) env('THREECX_RETENTION_CALL_LOGS_DAYS', 180),
        'api_audit_days' => (int) env('THREECX_RETENTION_API_AUDIT_DAYS', 90),
        'sync_cursor_days' => (int) env('THREECX_RETENTION_SYNC_DAYS', 365),
    ],

    'security' => [
        'redacted_fields' => [
            'authorization',
            'access_token',
            'refresh_token',
            'client_secret',
            'password',
        ],
    ],
];
