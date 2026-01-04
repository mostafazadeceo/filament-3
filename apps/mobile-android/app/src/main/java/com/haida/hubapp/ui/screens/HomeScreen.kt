package com.haida.hubapp.ui.screens

import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.PaddingValues
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.padding
import androidx.compose.material3.Card
import androidx.compose.material3.CardDefaults
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp

@Composable
fun HomeScreen(padding: PaddingValues) {
    Column(
        modifier = Modifier
            .fillMaxSize()
            .padding(padding)
            .padding(16.dp),
        verticalArrangement = Arrangement.spacedBy(12.dp)
    ) {
        Text(text = "کابین عملیات", style = MaterialTheme.typography.headlineSmall)
        Text(text = "دسترسی سریع به ماژول‌های عملیاتی هاب.", style = MaterialTheme.typography.bodyMedium)

        listOf(
            "POS و فروش آفلاین",
            "پشتیبانی و تیکتینگ",
            "حضور و غیاب هوشمند",
            "تسک‌ها و جلسات",
            "رمزارز و تسویه",
            "باشگاه مشتریان",
            "ممیزی هوشمند"
        ).forEach { title ->
            Card(
                colors = CardDefaults.cardColors(containerColor = MaterialTheme.colorScheme.surface),
                elevation = CardDefaults.cardElevation(defaultElevation = 2.dp)
            ) {
                Text(
                    text = title,
                    modifier = Modifier.padding(16.dp),
                    style = MaterialTheme.typography.titleMedium
                )
            }
        }
    }
}
