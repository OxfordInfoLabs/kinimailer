import {Component, OnInit} from '@angular/core';
import {MailingService} from '../../../services/mailing.service';
import {ActivatedRoute, Router} from '@angular/router';
import {MatDialog} from '@angular/material/dialog';
import {NewMailingComponent} from './new-mailing/new-mailing.component';
import {DomSanitizer, SafeHtml} from '@angular/platform-browser';
import {TemplateService} from '../../../services/template.service';
import * as _ from 'lodash';
import {SendTestMailingComponent} from './send-test-mailing/send-test-mailing.component';

@Component({
    selector: 'km-mailing',
    templateUrl: './mailing.component.html',
    styleUrls: ['./mailing.component.sass']
})
export class MailingComponent implements OnInit {

    public mailing: any = {};
    public showIframePane = false;
    public iframeLoading = false;
    public iframeURL: string = null;
    public templateHTML: SafeHtml = '';
    public template: any = {};

    private mailingId: number;

    constructor(private mailingService: MailingService,
                private route: ActivatedRoute,
                private dialog: MatDialog,
                private sanitise: DomSanitizer,
                private templateService: TemplateService,
                private router: Router) {
    }

    ngOnInit(): void {
        this.route.params.subscribe(params => {
            this.mailingId = params.id;
            this.loadMailing();
        });
    }

    public iframeLoaded(event) {
        setTimeout(() => {
            this.iframeLoading = false;
        }, 500);
    }

    public iframeResizeStart(event) {
        this.showIframePane = true;
    }

    public iframeResizeEnd(event) {
        this.showIframePane = false;
    }

    public async sanitiseHTML() {
        const html: string = await this.templateService.evaluateTemplate({
            id: this.template.id,
            sections: this.mailing.templateSections,
            parameters: this.mailing.templateParameters,
            html: this.template.html
        });
        // this.iframeLoading = true;
        this.templateHTML = this.sanitise.bypassSecurityTrustHtml(html);
    }

    public openNewMailingDialog(mailing) {
        const dialogRef = this.dialog.open(NewMailingComponent, {
            width: '1200px',
            height: '800px',
            data: {
                mailing
            }
        });

        dialogRef.afterClosed().subscribe(res => {

        });
    }

    public sendTest(mailing: any) {
        const dialogRef = this.dialog.open(SendTestMailingComponent, {
            width: '1000px',
            height: '850px',
            data: {
                mailing
            }
        });

        dialogRef.afterClosed().subscribe(res => {

        });
    }

    public async next() {
        await this.save();
        this.router.navigate(['/mailings', this.mailing.id, 'schedule']);
    }

    public async save() {
        await this.mailingService.saveMailing(this.mailing);
    }

    private async loadMailing(): Promise<void> {
        this.mailing = await this.mailingService.getMailing(this.mailingId);
        this.mailingId = this.mailing.id;
        if (!this.mailingId) {
            this.openNewMailingDialog(null);
        } else {
            this.template = this.mailing.template || {};
            if (!Array.isArray(this.mailing.templateSections)) {
                this.mailing.templateSections = [];
            }
            _.forEach(this.template.sections, section => {
                if (!_.find(this.mailing.templateSections, {key: section.key})) {
                    this.mailing.templateSections.push(section);
                }
            });

            if (!Array.isArray(this.mailing.templateParameters)) {
                this.mailing.templateParameters = [];
            }
            _.forEach(this.template.parameters, parameter => {
                if (!_.find(this.mailing.templateParameters, {key: parameter.key})) {
                    this.mailing.templateParameters.push(parameter);
                }
            });

            this.mailing.templateSections.forEach(templateSection => {
                if (!templateSection.data || (templateSection.data && Array.isArray(templateSection.data))) {
                    templateSection.data = {};
                }
            });

            this.mailing.templateParameters.forEach(templateParameter => {
                if (!templateParameter.data || (templateParameter.data && Array.isArray(templateParameter.data))) {
                    templateParameter.data = {};
                }
            });

            if (this.template.html) {
                this.sanitiseHTML();
            }
        }
    }



}
