import {Component, Inject, OnInit} from '@angular/core';
import {MailingListService} from '../../../../services/mailing-list.service';
import {TemplateService} from '../../../../services/template.service';
import {BehaviorSubject, merge, Subject} from 'rxjs';
import {debounceTime, map, switchMap} from 'rxjs/operators';
import * as _ from 'lodash';
import {MailingService} from '../../../../services/mailing.service';
import {MAT_DIALOG_DATA, MatDialogRef} from '@angular/material/dialog';

@Component({
    selector: 'km-new-mailing',
    templateUrl: './new-mailing.component.html',
    styleUrls: ['./new-mailing.component.sass']
})
export class NewMailingComponent implements OnInit {

    public mailingLists: any = [];
    public templates: any = [];
    public mailing: any = {mailingListIds: []};
    public mailingListSearchText = new BehaviorSubject('');
    public templateSearchText = new BehaviorSubject('');
    public limit = 10;
    public offset = 0;
    public page = 1;
    public templateLimit = 10;
    public templateOffset = 0;
    public templatePage = 1;
    public endOfResults = false;
    public endOfTemplateResults = false;
    public loading = true;
    public templateLoading = true;
    public lodash = _;

    private reload = new Subject();

    constructor(private mailingListService: MailingListService,
                private templateService: TemplateService,
                private mailingService: MailingService,
                public dialogRef: MatDialogRef<NewMailingComponent>,
                @Inject(MAT_DIALOG_DATA) public data: any) {
    }

    ngOnInit(): void {
        if (this.data.mailing) {
            this.mailing = this.data.mailing;
        }
        merge(this.mailingListSearchText, this.reload)
            .pipe(
                debounceTime(300),
                // distinctUntilChanged(),
                switchMap(() =>
                    this.getMailingLists()
                )
            ).subscribe((mailingLists: any) => {
            this.endOfResults = mailingLists.length < this.limit;
            this.mailingLists = mailingLists;
            this.loading = false;
        });

        merge(this.templateSearchText, this.reload)
            .pipe(
                debounceTime(300),
                // distinctUntilChanged(),
                switchMap(() =>
                    this.getTemplates()
                )
            ).subscribe((templates: any) => {
            this.endOfTemplateResults = templates.length < this.limit;
            this.templates = templates;
            this.templates.map(template => {
                template.sectionSummary = _.map(template.sections, 'title').join(', ');
                template.parameterSummary = _.map(template.parameters, 'title').join(', ');
                return template;
            });
            this.templateLoading = false;
        });

        this.mailingListSearchText.subscribe(() => {
            this.page = 1;
            this.offset = 0;
        });

        this.templateSearchText.subscribe(() => {
            this.templatePage = 1;
            this.templateOffset = 0;
        });
    }

    public updateKey(value) {
        this.mailing.key = _.camelCase(value);
    }

    public updateMailingListIds(mailingList) {
        if (!this.mailing.mailingListIds) {
            this.mailing.mailingListIds = [];
        }
        if (!_.find(this.mailing.mailingListIds)) {
            this.mailing.mailingListIds.push(mailingList.id);
        } else {
            _.pull(this.mailing.mailingListIds, mailingList.id);
        }
    }

    public search(event: any) {
        this.mailingListSearchText.next(event.target.value);
    }

    public searchTemplate(event: any) {
        this.templateSearchText.next(event.target.value);
    }

    public async createMailing() {
        console.log(this.mailing);
        const mailingId = await this.mailingService.saveMailing(this.mailing);
        window.location.href = '/mailings/' + mailingId;
    }

    private getMailingLists() {
        return this.mailingListService.filterMailingList(
            this.mailingListSearchText.getValue() || '',
            this.offset.toString(),
            this.limit.toString()
        ).pipe(map((mailingLists: any) => {
                return mailingLists;
            })
        );
    }

    private getTemplates() {
        return this.templateService.filterTemplates(
            this.templateSearchText.getValue() || '',
            this.templateOffset.toString(),
            this.templateLimit.toString()
        ).pipe(map((templates: any) => {
                return templates;
            })
        );
    }

}
