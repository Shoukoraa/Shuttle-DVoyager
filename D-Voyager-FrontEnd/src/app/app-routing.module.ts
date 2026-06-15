import { NgModule } from '@angular/core';
import { PreloadAllModules, RouterModule, Routes } from '@angular/router';
import { AuthGuard } from './guards/auth.guard';
import { RoleGuard } from './guards/role.guard';
import { WelcomeGuard } from './guards/welcome.guard';

const routes: Routes = [
  {
    path: 'home',
    loadChildren: () => import('./home/home.module').then( m => m.HomePageModule),
    canActivate: [WelcomeGuard]
  },
  {
    path: '',
    redirectTo: 'home',
    pathMatch: 'full'
  },
  {
    path: 'welcome',
    loadChildren: () => import('./welcome/welcome.module').then( m => m.WelcomePageModule)
  },
  {
    path: 'privacy-policy',
    loadChildren: () => import('./privacy-policy/privacy-policy.module').then( m => m.PrivacyPolicyPageModule)
  },
  {
    path: 'login',
    loadChildren: () => import('./login/login.module').then( m => m.LoginPageModule),
    canActivate: [WelcomeGuard]
  },
  {
    path: 'signup',
    loadChildren: () => import('./signup/signup.module').then( m => m.SignupPageModule),
    canActivate: [WelcomeGuard]
  },
  {
    path: 'otp',
    loadChildren: () => import('./otp/otp.module').then( m => m.OtpPageModule)
  },
  {
    path: 'forgot-password',
    loadChildren: () => import('./forgot-password/forgot-password.module').then( m => m.ForgotPasswordPageModule)
  },
  {
    path: 'schedule',
    loadChildren: () => import('./schedule/schedule.module').then( m => m.SchedulePageModule)
  },
  {
    path: 'select-seat',
    loadChildren: () => import('./select-seat/select-seat.module').then( m => m.SelectSeatPageModule)
  },
  {
    path: 'booking-summary',
    loadChildren: () => import('./booking-summary/booking-summary.module').then( m => m.BookingSummaryPageModule),
    canActivate: [AuthGuard]
  },
  {
    path: 'payment',
    loadChildren: () => import('./payment/payment.module').then( m => m.PaymentPageModule),
    canActivate: [AuthGuard]
  },
  {
    path: 'profile',
    loadChildren: () => import('./profile/profile.module').then( m => m.ProfilePageModule),
    canActivate: [AuthGuard]
  },
  {
    path: 'edit-profile',
    loadChildren: () => import('./edit-profile/edit-profile.module').then( m => m.EditProfilePageModule),
    canActivate: [AuthGuard]
  },
  {
    path: 'change-password',
    loadChildren: () => import('./change-password/change-password.module').then( m => m.ChangePasswordPageModule),
    canActivate: [AuthGuard]
  },
  {
    path: 'tickets',
    loadChildren: () => import('./tickets/tickets.module').then( m => m.TicketsPageModule),
    canActivate: [AuthGuard]
  },
  {
    path: 'promos',
    loadChildren: () => import('./promos/promos.module').then( m => m.PromosPageModule),
    canActivate: [AuthGuard]
  },
  {
    path: 'customer-service',
    loadChildren: () => import('./customer-service/customer-service.module').then( m => m.CustomerServicePageModule),
    canActivate: [AuthGuard]
  },
  {
    path: 'driver-home',
    loadChildren: () => import('./driver-home/driver-home.module').then( m => m.DriverHomePageModule),
    canActivate: [AuthGuard, RoleGuard],
    data: { role: 'driver' }
  },
  {
    path: 'driver-history',
    loadChildren: () => import('./driver-history/driver-history.module').then( m => m.DriverHistoryPageModule),
    canActivate: [AuthGuard, RoleGuard],
    data: { role: 'driver' }
  },
  {
    path: 'driver-chat',
    loadChildren: () => import('./driver-chat/driver-chat.module').then( m => m.DriverChatPageModule),
    canActivate: [AuthGuard, RoleGuard],
    data: { role: 'driver' }
  },
  {
    path: 'driver-profile',
    loadChildren: () => import('./driver-profile/driver-profile.module').then( m => m.DriverProfilePageModule),
    canActivate: [AuthGuard, RoleGuard],
    data: { role: 'driver' }
  },
  {
    path: 'chatbot',
    loadChildren: () => import('./chatbot/chatbot.module').then( m => m.ChatbotPageModule)
  },
  {
    path: 'driver-login',
    loadChildren: () => import('./driver-login/driver-login.module').then( m => m.DriverLoginPageModule),
    canActivate: [WelcomeGuard]
  },  {
    path: 'driver-reviews',
    loadChildren: () => import('./driver-reviews/driver-reviews.module').then( m => m.DriverReviewsPageModule),
    canActivate: [AuthGuard, RoleGuard],
    data: { role: 'driver' }
  }

];

@NgModule({
  imports: [
    RouterModule.forRoot(routes, { preloadingStrategy: PreloadAllModules })
  ],
  exports: [RouterModule]
})
export class AppRoutingModule { }
