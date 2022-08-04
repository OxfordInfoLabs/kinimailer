import {ModuleWithProviders, NgModule} from '@angular/core';
import {MailingListsComponent} from './components/mailing-lists/mailing-lists.component';
import {MailingListComponent} from './components/mailing-lists/mailing-list/mailing-list.component';
import {RouterModule} from '@angular/router';
import {CommonModule} from '@angular/common';
import {FormsModule} from '@angular/forms';
import { TemplatesComponent } from './components/templates/templates.component';
import { TemplateComponent } from './components/templates/template/template.component';
import {NgxResizableModule} from '@3dgenomes/ngx-resizable';
import {MatProgressSpinnerModule} from '@angular/material/progress-spinner';
import {BrowserModule} from '@angular/platform-browser';
import {CodemirrorModule} from '@ctrl/ngx-codemirror';
import {QuillModule} from 'ngx-quill';
import { MailingsComponent } from './components/mailings/mailings.component';
import { MailingComponent } from './components/mailings/mailing/mailing.component';
import {MatDialogModule} from '@angular/material/dialog';
import { NewMailingComponent } from './components/mailings/mailing/new-mailing/new-mailing.component';
import {MatTabsModule} from '@angular/material/tabs';
import { MailingScheduleComponent } from './components/mailings/mailing/mailing-schedule/mailing-schedule.component';
import {MatSelectModule} from '@angular/material/select';
import {MatIconModule} from '@angular/material/icon';
import {MatButtonModule} from '@angular/material/button';
import {MatSnackBarModule} from '@angular/material/snack-bar';


@NgModule({
    declarations: [
        MailingListsComponent,
        MailingListComponent,
        TemplatesComponent,
        TemplateComponent,
        MailingsComponent,
        MailingComponent,
        NewMailingComponent,
        MailingScheduleComponent
    ],
    imports: [
        BrowserModule,
        RouterModule,
        CommonModule,
        FormsModule,
        NgxResizableModule,
        MatProgressSpinnerModule,
        CodemirrorModule,
        QuillModule.forRoot(),
        MatDialogModule,
        MatTabsModule,
        MatSelectModule,
        MatIconModule,
        MatButtonModule,
        MatSnackBarModule
    ],
    exports: [
        MailingListsComponent,
        MailingListComponent,
        TemplatesComponent,
        TemplateComponent,
        MailingsComponent,
        MailingComponent,
        MailingScheduleComponent
    ]
})
export class NgxKinimailerModule {
    static forRoot(conf?: KinimailerModuleConfig): ModuleWithProviders<NgxKinimailerModule> {
        return {
            ngModule: NgxKinimailerModule,
            providers: [
                {provide: KinimailerModuleConfig, useValue: conf || {}}
            ]
        };
    }
}

export class KinimailerModuleConfig {
    backendURL: string;
}
