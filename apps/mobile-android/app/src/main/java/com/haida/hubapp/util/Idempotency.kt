package com.haida.hubapp.util

import java.util.UUID

object Idempotency {
    fun key(prefix: String): String = "$prefix-${UUID.randomUUID()}"
}
