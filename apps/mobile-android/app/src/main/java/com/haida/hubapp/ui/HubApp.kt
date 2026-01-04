package com.haida.hubapp.ui

import androidx.compose.material3.Badge
import androidx.compose.material3.BadgedBox
import androidx.compose.material3.BottomAppBar
import androidx.compose.material3.Icon
import androidx.compose.material3.NavigationBarItem
import androidx.compose.material3.Scaffold
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.collectAsState
import androidx.compose.runtime.CompositionLocalProvider
import androidx.compose.ui.Modifier
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.LayoutDirection
import androidx.compose.ui.platform.LocalLayoutDirection
import androidx.navigation.compose.NavHost
import androidx.navigation.compose.composable
import androidx.navigation.compose.currentBackStackEntryAsState
import androidx.navigation.compose.rememberNavController
import com.haida.hubapp.R
import com.haida.hubapp.ui.screens.AuditorScreen
import com.haida.hubapp.ui.screens.CryptoScreen
import com.haida.hubapp.ui.screens.HrScreen
import com.haida.hubapp.ui.screens.HomeScreen
import com.haida.hubapp.ui.screens.LoyaltyScreen
import com.haida.hubapp.ui.screens.MeetingsScreen
import com.haida.hubapp.ui.screens.PosScreen
import com.haida.hubapp.ui.screens.SupportScreen
import com.haida.hubapp.ui.screens.TasksScreen
import kotlinx.coroutines.flow.MutableStateFlow

private val syncBadge = MutableStateFlow(false)

@Composable
fun HubApp() {
    val navController = rememberNavController()
    val backStackEntry = navController.currentBackStackEntryAsState()
    val currentRoute = backStackEntry.value?.destination?.route
    val syncAlert = syncBadge.collectAsState()

    val destinations = listOf(
        HubDestination("home", "خانه", R.drawable.ic_home),
        HubDestination("pos", "POS", R.drawable.ic_pos),
        HubDestination("support", "پشتیبانی", R.drawable.ic_support),
        HubDestination("tasks", "تسک", R.drawable.ic_tasks)
    )

    CompositionLocalProvider(LocalLayoutDirection provides LayoutDirection.Rtl) {
        Scaffold(
            bottomBar = {
                BottomAppBar {
                    destinations.forEach { destination ->
                        NavigationBarItem(
                            selected = currentRoute == destination.route,
                            onClick = { navController.navigate(destination.route) },
                            icon = {
                                BadgedBox(badge = {
                                    if (destination.route == "pos" && syncAlert.value) {
                                        Badge()
                                    }
                                }) {
                                    Icon(
                                        painter = painterResource(destination.icon),
                                        contentDescription = destination.label
                                    )
                                }
                            },
                            label = { Text(destination.label, fontWeight = FontWeight.SemiBold) }
                        )
                    }
                }
            }
        ) { padding ->
            NavHost(
                navController = navController,
                startDestination = "home",
                modifier = Modifier
            ) {
                composable("home") { HomeScreen(padding) }
                composable("pos") { PosScreen(padding) }
                composable("support") { SupportScreen(padding) }
                composable("tasks") { TasksScreen(padding) }
                composable("meetings") { MeetingsScreen(padding) }
                composable("hr") { HrScreen(padding) }
                composable("crypto") { CryptoScreen(padding) }
                composable("loyalty") { LoyaltyScreen(padding) }
                composable("auditor") { AuditorScreen(padding) }
            }
        }
    }
}

data class HubDestination(
    val route: String,
    val label: String,
    val icon: Int
)
