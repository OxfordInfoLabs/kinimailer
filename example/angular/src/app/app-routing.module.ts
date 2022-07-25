import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import {MailingListsComponent} from './views/mailing-lists/mailing-lists.component';
import {LoginComponent} from './views/login/login.component';
import {AuthGuard} from './guards/auth.guard';
import {SubscribersComponent} from './views/subscribers/subscribers.component';
import {TemplatesComponent} from './views/templates/templates.component';
import {MailingListComponent} from './views/mailing-lists/mailing-list/mailing-list.component';
import {TemplateComponent} from './views/templates/template/template.component';
import {MailingsComponent} from './views/mailings/mailings.component';
import {MailingComponent} from './views/mailings/mailing/mailing.component';

const routes: Routes = [
    {
        path: '',
        redirectTo: '/mailing-lists',
        pathMatch: 'full'
    },
    {
        path: 'mailing-lists',
        component: MailingListsComponent,
        canActivate: [AuthGuard],
        data: {
            title: 'Mailing Lists'
        }
    },
    {
        path: 'mailing-lists/:id',
        component: MailingListComponent,
        canActivate: [AuthGuard],
        data: {
            title: 'Mailing Lists'
        }
    },
    {
        path: 'subscribers',
        component: SubscribersComponent,
        canActivate: [AuthGuard],
        data: {
            title: 'Subscribers'
        }
    },
    {
        path: 'templates',
        component: TemplatesComponent,
        canActivate: [AuthGuard],
        data: {
            title: 'Templates'
        }
    },
    {
        path: 'templates/:id',
        component: TemplateComponent,
        canActivate: [AuthGuard],
        data: {
            title: 'Templates'
        }
    },
    {
        path: 'mailings',
        component: MailingsComponent,
        canActivate: [AuthGuard],
        data: {
            title: 'Mailings'
        }
    },
    {
        path: 'mailings/:id',
        component: MailingComponent,
        canActivate: [AuthGuard],
        data: {
            title: 'Mailings'
        }
    },
    {
        path: 'login',
        component: LoginComponent
    }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
