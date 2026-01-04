package com.haida.hubapp

import com.haida.hubapp.util.Idempotency
import org.junit.Assert.assertTrue
import org.junit.Test

class IdempotencyTest {
    @Test
    fun generatesKey() {
        val key = Idempotency.key("task")
        assertTrue(key.startsWith("task-"))
    }
}
