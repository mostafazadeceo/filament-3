package com.haida.hubapp.ui.screens

import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.PaddingValues
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.padding
import androidx.compose.material3.Button
import androidx.compose.material3.Card
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import androidx.hilt.navigation.compose.hiltViewModel
import com.haida.hubapp.ui.viewmodels.HrViewModel

@Composable
fun HrScreen(padding: PaddingValues) {
    val viewModel = hiltViewModel<HrViewModel>()

    Column(
        modifier = Modifier
            .fillMaxSize()
            .padding(padding)
            .padding(16.dp),
        verticalArrangement = Arrangement.spacedBy(12.dp)
    ) {
        Card {
            Column(modifier = Modifier.padding(16.dp), verticalArrangement = Arrangement.spacedBy(8.dp)) {
                Text(text = "حضور و غیاب", style = MaterialTheme.typography.titleMedium)
                Text(text = "چک‌این/چک‌اوت آفلاین با تایید مدیر.")
                Button(onClick = { viewModel.checkIn() }) {
                    Text(text = "چک‌این")
                }
                Button(onClick = { viewModel.checkOut() }) {
                    Text(text = "چک‌اوت")
                }
            }
        }
    }
}
