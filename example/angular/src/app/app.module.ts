import {NgModule} from '@angular/core';
import {BrowserModule} from '@angular/platform-browser';

import {AppRoutingModule} from './app-routing.module';
import {AppComponent} from './app.component';
import {MailingListsComponent} from './views/mailing-lists/mailing-lists.component';
import {SubscribersComponent} from './views/subscribers/subscribers.component';
import {TemplatesComponent} from './views/templates/templates.component';
import {LoginComponent} from './views/login/login.component';
import {NgKiniAuthModule} from 'ng-kiniauth';
import {environment} from '../environments/environment';
import {FormsModule, ReactiveFormsModule} from '@angular/forms';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import {SessionInterceptor} from './session.interceptor';
import {HTTP_INTERCEPTORS} from '@angular/common/http';
import { MailingsComponent } from './views/mailings/mailings.component';
import {NgxKinimailerModule} from 'ngx-kinimailer';
import { MailingListComponent } from './views/mailing-lists/mailing-list/mailing-list.component';
import { TemplateComponent } from './views/templates/template/template.component';
import {NgxResizableModule} from '@3dgenomes/ngx-resizable';
import {MatProgressSpinnerModule} from '@angular/material/progress-spinner';
import {CodemirrorModule} from '@ctrl/ngx-codemirror';
import { MailingComponent } from './views/mailings/mailing/mailing.component';
import { MailingScheduleComponent } from './views/mailings/mailing/mailing-schedule/mailing-schedule.component';
import { MailingStatusComponent } from './views/mailings/mailing/mailing-status/mailing-status.component';

@NgModule({
    declarations: [
        AppComponent,
        MailingListsComponent,
        SubscribersComponent,
        TemplatesComponent,
        LoginComponent,
        MailingsComponent,
        MailingListComponent,
        TemplateComponent,
        MailingComponent,
        MailingScheduleComponent,
        MailingStatusComponent
    ],
    imports: [
        BrowserModule,
        AppRoutingModule,
        FormsModule,
        ReactiveFormsModule,
        NgKiniAuthModule.forRoot({
            guestHttpURL: `${environment.backendURL}/guest`,
            accessHttpURL: `${environment.backendURL}/account`
        }),
        NgxKinimailerModule.forRoot({
            backendURL: `${environment.backendURL}/account`
        }),
        BrowserAnimationsModule,
        NgxResizableModule,
        MatProgressSpinnerModule,
        CodemirrorModule
    ],
    providers: [
        {
            provide: HTTP_INTERCEPTORS,
            useClass: SessionInterceptor,
            multi: true
        }
    ],
    bootstrap: [AppComponent]
})
export class AppModule {
}
