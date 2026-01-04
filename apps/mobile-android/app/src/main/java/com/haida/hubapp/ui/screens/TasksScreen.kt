package com.haida.hubapp.ui.screens

import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.PaddingValues
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.padding
import androidx.compose.material3.Button
import androidx.compose.material3.Card
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.TextField
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import androidx.hilt.navigation.compose.hiltViewModel
import com.haida.hubapp.ui.viewmodels.TasksViewModel

@Composable
fun TasksScreen(padding: PaddingValues) {
    val viewModel = hiltViewModel<TasksViewModel>()
    val (title, setTitle) = remember { mutableStateOf("") }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .padding(padding)
            .padding(16.dp),
        verticalArrangement = Arrangement.spacedBy(12.dp)
    ) {
        Card {
            Column(modifier = Modifier.padding(16.dp), verticalArrangement = Arrangement.spacedBy(8.dp)) {
                Text(text = "تسک‌ها", style = MaterialTheme.typography.titleMedium)
                Text(text = "ثبت و پیگیری تسک‌ها با همگام‌سازی LWW.")
                TextField(value = title, onValueChange = setTitle, label = { Text("عنوان تسک") })
                Button(onClick = { viewModel.createTask(title) }) {
                    Text(text = "تسک جدید")
                }
            }
        }
    }
}
